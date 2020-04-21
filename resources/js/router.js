import Vue from 'vue'
import VueRouter from 'vue-router'
import NewsFeed from './views/NewsFeed'
import UserShow from './views/User/Show'

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        { path: '/', name: 'home', component: NewsFeed },
        { path: '/users/:id', name: 'user.show', component: UserShow },
    ]
})
