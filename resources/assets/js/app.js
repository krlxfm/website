
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.moment = require('moment');
window.Vue = require('vue');
window.swal = require('sweetalert');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('conflict-list', require('./components/ConflictList.vue'));
Vue.component('term-selector', require('./components/TermSelector.vue'));
Vue.component('preference-list', require('./components/PreferenceList.vue'));
Vue.component('participant-list', require('./components/ParticipantList.vue'));
Vue.component('participant-search', require('./components/ParticipantSearch.vue'));

window.to12Hour = function(time) {
    var components = time.split(':');
    var hour = parseInt(components[0]);
    while(hour >= 24) { hour -= 24; }
    var pm = (hour >= 12);
    if(hour >= 12) hour -= 12;
    if(hour == 0) hour = 12;
    return hour+":"+components[1]+" "+(pm ? 'pm' : 'am');
}

const app = new Vue({
    el: '#app'
});

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
})
