require('./bootstrap');

import globals from './vue/mixins/globals'
import Vue from 'vue';

// Custom modules
import "./vue/modules/sidebar";
import "./vue/modules/theme";
// import "./vue/modules/feather";

// font awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas);
library.add(fab);
library.add(far);
Vue.component('fa', FontAwesomeIcon);

// Components
Vue.component('example', ()=> import('@/vue/views/Example'));
Vue.component('main-navigation', ()=> import('@/vue/components/main-navigation'));

Vue.mixin(globals);

new Vue({
    el:'#app'
});
