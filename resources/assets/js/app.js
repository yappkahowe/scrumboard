
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/', component: require('./components/Board.vue') },
        { path: '/my-tasks', component: require('./components/MyTasks.vue') },
        { path: '/my-teammates', component: require('./components/MyTeammates.vue') },
        { path: '/my-profile', component: require('./components/MyProfile.vue') }
    ]
})

const app = new Vue({
    router,
    data() {
        return {
            user: {}
        }
    },
    created() {
        this.$http.get('/api/me').then(response => this.user = response.data)

        this.$on('updatedMyself', profile => this.user = profile)
    }
}).$mount('#app')