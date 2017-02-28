<template>
    <div id="my-teammates">
        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#add-teammate-modal">Add Teammate</button>

        <span class="alert alert-success toast" role="alert" ref="toast">
            <i class="fa fa-thumbs-up"></i> New teammate <b>{{ newTeammate.name }}</b> has been successfully <b>added</b>.
        </span>

        <h3 class="page-header">My Teammates</h3>

        <div class="row">
            <div class="col-md-3 search">
                <p class="help-block">Filter by</p>

                <form class="form" @submit.prevent>
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" class="form-control" v-model="filter.name">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Designation</label>
                        <input type="text" class="form-control" v-model="filter.designation">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="text" class="form-control" v-model="filter.email">
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-default" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>

            <div class="col-md-8 col-md-offset-1">
                <p class="help-block">List of teammates</p>
                <spinner v-show="teammates.length <= 0"></spinner>
                <div class="table-responsive data" v-show="teammates.length > 0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Teammate</th>
                                <th>Designation</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="teammate in filteredTeammates">
                                <td>
                                    <img :src="teammate.avatar_url" class="photo" v-if="teammate.avatar_url">
                                </td>
                                <td>
                                    <span>{{ teammate.name }}</span>
                                </td>
                                <td>{{ teammate.designation }}</td>
                                <td>{{ teammate.email }}</td>
                                <td :class="teammate.deleted_at ? 'text-warning' : 'text-info'">
                                    {{ teammate.deleted_at ? 'Deleted' : 'Active' }}
                                </td>
                                <td class="action">
                                    <button class="btn btn-success btn-sm"
                                        @click="restoreTeammate(teammate, $event)"
                                        v-if="teammate.deleted_at">Restore</button>
                                    <button class="btn btn-warning btn-sm"
                                        @click="removeTeammate(teammate, $event)"
                                        v-if="!teammate.deleted_at">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="add-teammate-modal" class="modal fade" tabindex="-1" role="dialog" ref="add-modal">
            <form class="modal-dialog modal-sm" role="document" @submit.prevent="addTeammate">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Add Teammate</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form">
                            <div class="form-group" :class="{ 'has-error': errors.name.length > 0 }">
                                <label class="control-label">Name</label>
                                <input type="text" class="form-control" v-model="add.name" ref="add-name-input">
                                <small class="help-block" v-if="errors.name.length > 0">{{ errors.name }}</small>
                            </div>
                            <div class="form-group" :class="{ 'has-error': errors.designation.length > 0 }">
                                <label class="control-label">Designation</label>
                                <input type="text" class="form-control" v-model="add.designation">
                                <small class="help-block" v-if="errors.designation.length > 0">{{ errors.designation }}</small>
                            </div>
                            <div class="form-group" :class="{ 'has-error': errors.email.length > 0 }">
                                <label class="control-label">Email</label>
                                <input type="email" class="form-control" v-model="add.email">
                                <small class="help-block" v-if="errors.email.length > 0">{{ errors.email }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" ref="btn-modal-add">Add Teammate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                le,
                teammates: [],
                filter: {
                    name: '',
                    designation: '',
                    email: ''
                },
                add: {
                    name: '',
                    designation: '',
                    email: ''
                },
                newTeammate: {
                    name: '',
                    designation: '',
                    email: ''
                },
                errors: {
                    name: '',
                    designation: '',
                    email: ''
                }
            }
        },
        computed: {
            filteredTeammates() {
                return this.teammates.filter(teammate => {
                    let pass = true

                    if (this.filter.name) {
                        pass = pass && teammate.name.toLowerCase().includes(this.filter.name.toLowerCase())
                    }

                    if (this.filter.designation) {
                        pass = pass && teammate.designation && teammate.designation.toLowerCase().includes(this.filter.designation.toLowerCase())
                    }

                    if (this.filter.email) {
                        pass = pass && teammate.email.toLowerCase().includes(this.filter.email.toLowerCase())
                    }

                    return pass
                })
            }
        },
        methods: {
            resetFilter() {
                Object.keys(this.filter).forEach(key => this.filter[key] = '')
            },
            resetAddAndErrors() {
                Object.keys(this.add).forEach(key => this.add[key] = '')
                Object.keys(this.errors).forEach(key => this.errors[key] = '')
            },
            addTeammate() {
                let $modal = $(this.$refs['add-modal']),
                    $button = $(this.$refs['btn-modal-add'])

                $button.attr('disabled', 'disabled').html('Adding teammate...')

                this.$http.post('/api/users', this.add).then(response => {
                    this.resetAddAndErrors()

                    this.newTeammate = response.data
                    this.teammates.unshift(response.data)

                    $button.removeAttr('disabled').html('Add Teammate')
                    $modal.modal('hide')

                    setTimeout(() => { $(this.$refs.toast).addClass('active') }, 400)
                    setTimeout(() => { $(this.$refs.toast).removeClass('active') }, 5400)
                }).catch(response => {
                    let errors = response.data

                    Object.keys(this.errors).forEach(key => this.errors[key] = errors[key] ? errors[key][0]: '')
                    $button.removeAttr('disabled').html('Add Teammate')
                })
            },
            removeTeammate(teammate, event) {
                $(event.target).attr('disabled', 'disabled').html('Deleting...')

                this.$http.delete('/api/users/' + teammate.id).then(response => {
                    teammate.deleted_at = response.data.deleted_at

                    $(event.target).removeAttr('disabled').html('Delete')
                })
            },
            restoreTeammate(teammate, event) {
                $(event.target).attr('disabled', 'disabled').html('Restoring...')

                this.$http.post('/api/users/' + teammate.id).then(response => {
                    teammate.deleted_at = response.data.deleted_at

                    $(event.target).removeAttr('disabled').html('Restore')
                })
            }
        },
        created() {
            this.$http.get('/api/users').then(response => this.teammates = response.data)
        },
        mounted() {
            $(this.$refs['add-modal']).on('shown.bs.modal', () => this.$refs['add-name-input'].focus())
        }
    }
</script>