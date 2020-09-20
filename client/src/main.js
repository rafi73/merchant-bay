// =========================================================
// * Vuetify Material Dashboard - v2.1.0
// =========================================================
//
// * Product Page: https://www.creative-tim.com/product/vuetify-material-dashboard
// * Copyright 2019 Creative Tim (https://www.creative-tim.com)
//
// * Coded by Creative Tim
//
// =========================================================
//
// * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './plugins/base'
import './plugins/chartist'
import './plugins/vee-validate'
import vuetify from './plugins/vuetify'
import i18n from './i18n'
import axios from 'axios'
import moment from 'moment'
import DateFormater from './helpers/dateFormater'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import '@mdi/font/css/materialdesignicons.css'


Vue.use(DateFormater)



axios.defaults.baseURL = 'http://127.0.0.1:8000'
axios.defaults.headers['Accept'] = 'application/json';

Vue.prototype.$http = axios
Vue.prototype.moment = moment

// export default function setup() {
//   axios.interceptors.request.use(function(config) {
//       const token = store.getters.admin.token;
//       console.log(token)
//       if(token) {
//           config.headers.Authorization = `Bearer ${token}`;
//       }
//       return config;
//   }, function(err) {
//       return Promise.reject(err);
//   });
// }

Vue.config.productionTip = false

new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App),
}).$mount('#app')
