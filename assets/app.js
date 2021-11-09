import './styles/app.scss';
import './bootstrap.js';
import Vue from 'vue';
import vuetify from './plugins/vuetify';
import './plugins/multi-select';
import './plugins/tom-select';
import './plugins/fetch';
import './plugins/prototype-form';
import './controllers/modal-form_controller';
import Swal from 'sweetalert2';

// import Api from './Api';

import './react-components/Users';
import Home from './vuejs-components/Home';

Vue.config.productionTip = false;
Vue.component('user-crud', Home);

new Vue({
    vuetify
}).$mount('#app');
