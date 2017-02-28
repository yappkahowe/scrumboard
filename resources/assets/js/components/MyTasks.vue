<template>
    <div id="my-tasks">
        <h3 class="page-header">My Tasks</h3>

        <div class="row">
            <div class="col-md-3 search">
                <p class="help-block">Filter by</p>

                <form class="form" @submit.prevent>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea rows="4" class="form-control" v-model="filter.description"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Created Between</label>
                        <input type="text"
                            class="form-control"
                            placeholder="YYYY-MM-DD"
                            data-provide="datepicker"
                            data-date-autoclose="true"
                            data-date-format="yyyy-mm-dd"
                            data-date-clear-btn="true"
                            ref="created-from"
                            v-model="filter.created_at.from">

                        <div class="form-control-static"><b>To</b></div>

                        <input type="text"
                            class="form-control"
                            placeholder="YYYY-MM-DD"
                            data-provide="datepicker"
                            data-date-autoclose="true"
                            data-date-format="yyyy-mm-dd"
                            data-date-clear-btn="true"
                            ref="created-to"
                            v-model="filter.created_at.to">
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-default" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>

            <div class="col-md-8 col-md-offset-1">
                <p class="help-block">List of tasks</p>
                <spinner v-if="!tasks"></spinner>
                <div class="table-responsive data" v-if="tasks">
                    <p class="help-block text-center" v-if="filteredTasks.length <= 0">No task found.</p>
                    <table class="table table-hover" v-if="filteredTasks.length > 0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="task in filteredTasks">
                                <td>{{ task.id }}</td>
                                <td>{{ task.description }}</td>
                                <td>{{ le.moment(task.created_at).format('DD MMM YYYY, HH:mm a') }}</td>
                                <td class="status" :class="task.deleted_at ? 'text-warning' : 'text-info'">
                                    {{ task.deleted_at ? 'Removed' : 'Active' }}
                                </td>
                                <td class="action">
                                    <button class="btn btn-warning btn-sm"
                                        @click="removeTask(task, $event)"
                                        v-if="!task.deleted_at">Remove</button>
                                    <button class="btn btn-success btn-sm"
                                        @click="restoreTask(task, $event)"
                                        v-if="task.deleted_at">Restore</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                le,
                tasks: '',
                filter: {
                    description: '',
                    created_at: { from: '', to: '' }
                }
            }
        },
        computed: {
            filteredTasks() {
                return this.tasks ? this.tasks.filter(task => {
                    let include = task.description.toLowerCase().includes(this.filter.description.toLowerCase())

                    if (this.filter.created_at.from) {
                        include = include && le.moment(task.created_at).isSameOrAfter(this.filter.created_at.from)
                    }

                    if (this.filter.created_at.to) {
                        include = include && le.moment(task.created_at).isSameOrBefore(le.moment(this.filter.created_at.to).endOf('day'))
                    }

                    return include
                }) : []
            }
        },
        methods: {
            resetFilter() {
                this.filter.description = ''
                this.filter.created_at.from = ''
                this.filter.created_at.to = ''

                $(this.$refs['created-from']).datepicker('clearDates')
                $(this.$refs['created-to']).datepicker('clearDates')
            },
            removeTask(task, event) {
                let $button = $(event.target)

                $button.attr('disabled', 'disabled').html('Removing...')

                this.$http.delete('/api/tasks/' + task.id).then(response => {
                    task.deleted_at = response.data.deleted_at

                    $button.removeAttr('disabled').html('Remove')
                })
            },
            restoreTask(task, event) {
                let $button = $(event.target)

                $button.attr('disabled', 'disabled').html('Restoring...')

                this.$http.post('/api/tasks/' + task.id).then(response => {
                    task.deleted_at = response.data.deleted_at

                    $button.removeAttr('disabled').html('Restore')
                })
            }
        },
        created() {
            this.$http.get('/api/my-tasks').then(response => this.tasks = response.data)
        },
        mounted() {
            let $createdFrom = $(this.$refs['created-from']),
                $createdTo = $(this.$refs['created-to'])

            $createdFrom.on('changeDate', e => {
                this.filter.created_at.from = e.format()
                $createdTo.datepicker('setStartDate', e.date)

            }).on('clearDate', e => {
                $createdTo.datepicker('setStartDate', null)
            })

            $createdTo.on('changeDate', e => { this.filter.created_at.to = e.format() })
        }
    }
</script>