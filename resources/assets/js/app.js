import './bootstrap';
import router from './routes';

window.Vue = require('vue');

import Datatable from 'vuejs-datatable';
Vue.component('datatable', Datatable);

/* Элементы управления */
import Loader from './components/сontrols/Loader.vue';
Vue.component('loader', Loader);
import WelcomeMenu from './components/сontrols/WelcomeMenu.vue';
Vue.component('welcome-menu', WelcomeMenu);
import ManagerMenu from './components/сontrols/ManagerMenu.vue';
Vue.component('manager-menu', ManagerMenu);
import Modals from './components/сontrols/Modals.vue';
Vue.component('modals-component', Modals);
import Checkbox from './components/сontrols/Checkbox.vue';
Vue.component('easy-checkbox', Checkbox);
import Radio from './components/сontrols/Radio.vue';
Vue.component('easy-radio', Radio);
import InputSelect from './components/сontrols/InputSelect.vue';
Vue.component('input-select', InputSelect);
import ImageGallery from './components/controls/ImageGallery.vue';
Vue.component('ImageGallery', ImageGallery);

import { store } from './store';

const app = new Vue({
    el: '#app',
    router: router,
    store: store,
});