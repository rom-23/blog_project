import Vue from 'vue';
import vuetify from './plugins/vuetify';
import store from './store';
import router from './router/router';
import UserCrud from './vuejs-components/UserCrud';
import Home from './vuejs-components/Home';

Vue.config.productionTip = false;
Vue.component('user-crud', UserCrud);
Vue.component('home', Home);

new Vue({
    vuetify,
    store,
    router
}).$mount('#app');
