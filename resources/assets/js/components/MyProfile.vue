<template>
    <div id="my-profile">
        <h3 class="page-header">My Profile</h3>

        <span class="alert alert-success toast" role="alert" ref="toast">
            <i class="fa fa-thumbs-up"></i> Your profile has been <b>saved</b>.
        </span>

        <div class="data">
            <div class="row">
                <div class="col-md-3">
                    <img :src="user.avatar_url" class="photo">
                </div>
                <div class="col-md-6">
                    <form class="form" ref="form-profile" @submit.prevent="saveProfile">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input type="text" class="form-control" v-model="profile.name">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Designation</label>
                            <input type="text" class="form-control" v-model="profile.designation">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" ref="btn-save">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <h4>Account Information</h4>

            <div class="form form-horizontal">
                <div class="form-group">
                    <label class="control-label col-md-3">Email</label>
                    <div class="form-control-static col-md-6">{{ profile.email }}</div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Google ID</label>
                    <div class="form-control-static col-md-6">{{ profile.google_id }}</div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Google Avatar URL</label>
                    <div class="form-control-static col-md-6">
                        <a :href="profile.avatar_url" target="_blank">{{ profile.avatar_url }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                profile: {
                    name: '',
                    designation: '',
                    email: '',
                    google_id: '',
                    avatar_url: ''
                }
            }
        },
        watch: {
            user() {
                this.syncUserToProfile()
            }
        },
        methods: {
            syncUserToProfile() {
                Object.keys(this.profile).forEach(key => this.profile[key] = this.user[key])
            },
            saveProfile() {
                let $button = $(this.$refs['btn-save']),
                    $toast = $(this.$refs['toast']),
                    data = { name: this.profile.name, designation: this.profile.designation }

                $button.attr('disabled', 'disabled').html('Saving Profile...')

                this.$http.post('/api/me', data).then(response => {
                    this.$router.app.$emit('updatedMyself', response.json())

                    $button.removeAttr('disabled').html('Save Profile')
                    $toast.addClass('active')

                    setTimeout(() => { $toast.removeClass('active') }, 3000)
                })
            }
        },
        created() {
            this.syncUserToProfile()
        }
    }
</script>