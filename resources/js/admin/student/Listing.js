import AppListing from '../app-components/Listing/AppListing';
import Vue from "vue";
$('body').on('click', '#isShowBlockList', function(){
    if ( $( "#blocklist_div").is(":visible")) {
        $( "#blocklist_div" ).show();
    } else  {
        $( "#blocklist_div" ).hide();
    }
});

var vm =Vue.component('student-listing-block', {
    mixins: [AppListing]
});

Vue.component('student-calendar-listing', {
    mixins: [AppListing]
});
Vue.component('student-payment-listing', {
    mixins: [AppListing]
});
Vue.component('student-statistic-listing', {
    mixins: [AppListing]
});

Vue.component('student-listing', {
    mixins: [AppListing],
    created() {
        this.$root.$refs.studentform = this;
    }
});

