import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';
Vue.use(Router);
const routes = [
    {
        path       : '/front-end-app/vue/users-crud',
        name       : 'users_crud',
        components : {
            main: () => {
                return import('../vuejs-components/UserCrud');
            }
        }
    },
    {
        path       : '/front-end-app/vue/users-crud/add-user',
        name       : 'add_user',
        components : {
            top: () => {
                return import('../vuejs-components/AddUser');
            },
            main: () => {
                return import('../vuejs-components/UserCrud');
            }
        }
    }
    // {
    //     path     : '*',
    //     redirect : '/'
    // }
];
const router = new Router({
    mode : 'history',
    // mode : 'hash',
    base : process.env.BASE_URL,
    routes
});

export default router;
