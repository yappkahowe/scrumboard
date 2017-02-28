<template>
    <div>
        <transition name="fade">
            <div id="board" class="pad-20" v-show="initialized">
                <div class="row" style="margin: 10px 0 20px 0;">
                    <div class="col-md-4">
                        <h3 class="page-header cursive">
                            Scrum Board
                        </h3>
                    </div>
                    <div class="col-md-4">
                        <div class="online-teammates">
                            <div class="little-title">Viewing ({{ onlineTeammates.length }})</div>
                            <img :src="teammate.avatar_url"
                                class="photo"
                                :title="teammate.name"
                                v-for="teammate in onlineTeammates">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="offline-teammates">
                            <div class="little-title">Away ({{ offlineTeammates.length }})</div>
                            <img :src="teammate.avatar_url"
                                class="photo"
                                :title="teammate.name"
                                v-for="teammate in offlineTeammates">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-board">
                        <thead>
                            <tr>
                                <th>Teammate</th>
                                <th v-for="stage in stages">{{ stage.name }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="teammate in teammates">
                                <td class="teammate">
                                    <div class="particulars">
                                        <img :src="teammate.avatar_url" class="profile-photo">
                                        <div class="name cursive">{{ teammate.name }}</div>
                                        <div class="designation">{{ teammate.designation }}</div>
                                        <div class="online-mark" v-show="onlineTeammates.findIndex(online => online.id == teammate.id) > -1"></div>
                                    </div>

                                    <div class="help-block report">
                                        {{ teammate.last_reported_at
                                            ? 'Last report: ' + le.moment(teammate.last_reported_at).calendar()
                                            : 'No report yet.' }}
                                    </div>
                                </td>
                                <td class="stage"
                                    v-for="stage in teammate.stages"
                                    @dragover.prevent
                                    @drop="dropTask(stage, teammate, $event)">

                                    <div class="task"
                                        :draggable="task.user_id == user.id"
                                        :class="{ 'loading': !task.id, 'mine': task.user_id == user.id, 'others': task.user_id != user.id }"
                                        v-for="task in stage.tasks"
                                        @dragstart="dragTask(task, stage, teammate)">

                                        <div class="task-heading">
                                            <i class="fa fa-spinner fa-spin fa-fw" v-if="!task.id"></i>

                                            <div class="task-id" v-if="task.id">#{{ task.id }}</div>

                                            <button class="btn btn-link btn-xs btn-remove-task" 
                                                v-if="task.id && task.user_id == user.id" 
                                                @click="removeTask(task, stage, teammate)">

                                                <i class="fa fa-close"></i>
                                            </button>
                                        </div>
                                        <div class="task-body" :draggable="task.user_id == user.id" @dragstart.prevent>
                                            <textarea spellcheck="false"
                                                ref="task-input"
                                                v-model="task.description"
                                                v-if="task.id && task.user_id == user.id"
                                                @input="updateTask(task, teammate)"></textarea>
                                            <div v-if="task.user_id != user.id">{{ task.description }}</div>
                                        </div>
                                    </div>

                                    <button class="btn btn-link btn-sm btn-create-task" 
                                        v-if="teammate.id == user.id"
                                        @click="createTask(stage, teammate)">Add Task</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </transition>

        <spinner v-show="!initialized"></spinner>
    </div>
</template>

<script>
    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                le,
                stages: '',
                teammates: [],
                onlineTeammates: [],
                transfer: { task: '', stage: '', teammate: '' }
            }
        },
        computed: {
            initialized() { return this.user && this.stages && this.teammates },
            offlineTeammates() {
                return _.differenceWith(this.teammates, this.onlineTeammates, (a, b) => a.id == b.id)
            }
        },
        methods: {
            createTask(stage, teammate) {
                let newTask = { id: '', description: '', arrangement: stage.tasks.length, stage_id: stage.id }

                stage.tasks.push(newTask)
                this.createTaskInServer(newTask, teammate)
            },
            updateTask: _.debounce(function (task, teammate) {
                this.updateTaskInServer(task, teammate)
            }, 500),
            removeTask(task, stage, teammate) {
                stage.tasks.splice(stage.tasks.indexOf(task), 1)
                this.removeTaskInServer(task, teammate)
            },
            dragTask(task, stage, teammate) {
                this.transfer = { task, stage, teammate }
            },
            dropTask(newStage, teammate, event) {
                if (teammate != this.transfer.teammate) {
                    event.preventDefault()
                    return false
                }

                let oldStage = this.transfer.stage,
                    task = this.transfer.task

                if (oldStage != newStage) {
                    this.moveTask(task, oldStage, newStage)
                    this.updateTaskInServer(task, teammate)
                }
            },
            moveTask(task, oldStage, newStage) {
                oldStage.tasks.splice(oldStage.tasks.indexOf(task), 1)
                newStage.tasks.push(task)
                task.stage_id = newStage.id
            },
            onTaskCreated(task) {
                let teammate = this.teammates.find(teammate => teammate.id == task.user_id),
                    stage = teammate.stages.find(stage => stage.id == task.stage_id)

                if (stage) {
                    stage.tasks.push(task)
                    teammate.last_reported_at = task.updated_at
                }
            },
            onTaskUpdated(updatedTask) {
                let oldStage = null,
                    teammate = this.teammates.find(teammate => teammate.id == updatedTask.user_id),
                    newStage = teammate.stages.find(stage => stage.id == updatedTask.stage_id),
                    targetTask = newStage.tasks.find(task => task.id == updatedTask.id)

                // if target task is not found in its current stage, 
                // which means it is either moved into different stage or just restored from trash
                if (! targetTask) {
                    oldStage = teammate.stages.find(stage => {
                        return (targetTask = stage.tasks.find(task => task.id == updatedTask.id))
                    })

                    newStage.tasks.push(updatedTask)

                    if (oldStage) {
                        oldStage.tasks.splice(oldStage.tasks.indexOf(updatedTask), 1)
                    }
                }
                else {
                    Object.keys(updatedTask).forEach(key => { Vue.set(targetTask, key, updatedTask[key]) })
                }

                teammate.last_reported_at = updatedTask.updated_at
            },
            onTaskDeleted(deletedTask) {
                let teammate = this.teammates.find(teammate => teammate.id == deletedTask.user_id),
                    targetStage = teammate.stages.find(stage => stage.id == deletedTask.stage_id),
                    targetTaskIndex = targetStage.tasks.findIndex(task => task.id == deletedTask.id)

                targetStage.tasks.splice(targetTaskIndex, 1)
                teammate.last_reported_at = deletedTask.deleted_at
            },
            focusLastTaskInput() {
                _.last(this.$refs['task-input']).focus()
            },
            createTaskInServer(task, teammate) {
                this.$http.post('/api/tasks', task).then(response => {
                    let createdTask = response.data

                    Object.keys(createdTask).forEach(key => { Vue.set(task, key, createdTask[key]) })
                    teammate.last_reported_at = createdTask.updated_at

                    this.$nextTick(this.focusLastTaskInput)
                })
            },
            updateTaskInServer(task, teammate) {
                let body = _.merge({ _method: 'PATCH' }, task)

                this.$http.post('/api/tasks/' + task.id, body).then(response => {
                    let updatedTask = response.data

                    task.updated_at = updatedTask.updated_at
                    teammate.last_reported_at = updatedTask.updated_at
                })
            },
            removeTaskInServer(task, teammate) {
                this.$http.post('/api/tasks/' + task.id, { _method: 'DELETE' }).then(response => {
                    teammate.last_reported_at = response.data.deleted_at
                })
            },
            teammateJoining(user) {
                if (this.onlineTeammates.findIndex(teammate => teammate.id == user.id) < 0) {
                    this.onlineTeammates.push(user)
                }
            },
            teammateLeaving(user) {
                let index = this.onlineTeammates.findIndex(teammate => teammate.id == user.id)

                if (index >= 0) {
                    this.onlineTeammates.splice(index, 1)
                }
            }
        },
        created() {
            this.$http({ url: '/api/stages' }).then(response => {
                this.stages = response.data
            })

            this.$http({ url: '/api/teammates' }).then(response => {
                this.teammates = response.data
            })

            Echo.join('board')
                .here(users => this.onlineTeammates = users)
                .joining(user => this.teammateJoining(user))
                .leaving(user => this.teammateLeaving(user))
                .listen('Task.Created', e => this.onTaskCreated(e.task))
                .listen('Task.Updated', e => this.onTaskUpdated(e.task))
                .listen('Task.Deleted', e => this.onTaskDeleted(e.task))
        },
        destroyed() {
            Echo.leave('board')
        }
    }
</script>