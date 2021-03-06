/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');


import { Form, HasError, AlertError } from 'vform'
import moment from 'moment';
import VueProgressBar from 'vue-progressbar';
import VueRouter from 'vue-router'
import Swal from 'sweetalert2';
import Gate from './Gate';


//prototyping to have access to the class anywhere in app
Vue.prototype.$gate = new Gate(window.user);


// refrence globally
window.form = Form;
window.Swal = Swal;
//refrenced globally for component communication
// instance ofvue
window.Fire = new Vue();

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

window.Toast = Toast;
//Register a component globally
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue')
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component(
  'not-found',
  require('./components/notfound.vue')
);



Vue.use(VueRouter)
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})

let routes = [
    { path: '/dashboard', component: require('./components/dashboard.vue').default },
    { path: '/profile', component: require('./components/profile.vue').default },
    { path: '/users', component: require('./components/users.vue').default },
    // has to be last
    { path: '*', component: require('./components/notfound.vue').default }
];

const router = new VueRouter({
    mode:'history',
    routes
  });

// global filter
Vue.filter('capitalize',function(text){
  return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter('date',function(created){
  return moment(created).format('MMMM Do YYYY');
})
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('profile-component', require('./components/profile.vue').default);
// Vue.component('dashboard-component', require('./components/dashboard.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data:{
      search:""
    },
    methods:{
      //use loadash debunk function waits for sometime
      // send request after 2 seconds 
      // allow automatic search without pressing enter 1000(1 sec)
      searchit:_.debounce(()=>{
        Fire.$emit('searching');
      },1000)
      }
});
