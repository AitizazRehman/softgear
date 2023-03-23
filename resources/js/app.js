import 'babel-polyfill'
import Vue from 'vue'

import router from '~/router/index'
import store from '~/store/index'
import App from '$comp/App'
import '~/plugins/index'
import vuetify from '~/plugins/vuetify'
import moment from 'moment'
import { api } from '~/config'
import axios from 'axios'
import Permissions from './mixins//Permissions';
window.axios = axios;
window.Vue = Vue;
window.api = api;
window.moment =  moment;
Vue.mixin(Permissions);
export const app = new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
