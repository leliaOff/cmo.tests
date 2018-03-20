import './bootstrap';
import router from './routes';

window.Vue = require('vue');

import Datatable from 'vuejs-datatable';
Vue.component('datatable', Datatable);

/* Элементы управления */
import Loader from './components/controls/Loader.vue';
Vue.component('loader', Loader);
import WelcomeMenu from './components/controls/WelcomeMenu.vue';
Vue.component('welcome-menu', WelcomeMenu);
import ManagerMenu from './components/controls/ManagerMenu.vue';
Vue.component('manager-menu', ManagerMenu);
import Modals from './components/controls/Modals.vue';
Vue.component('modals-component', Modals);
import Checkbox from './components/controls/Checkbox.vue';
Vue.component('easy-checkbox', Checkbox);
import Radio from './components/controls/Radio.vue';
Vue.component('easy-radio', Radio);
import InputSelect from './components/controls/InputSelect.vue';
Vue.component('input-select', InputSelect);
import ImageGallery from './components/controls/ImageGallery.vue';
Vue.component('ImageGallery', ImageGallery);

import { store } from './store';

const app = new Vue({
    el: '#app',
    router: router,
    store: store,
});