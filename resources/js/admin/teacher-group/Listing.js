import AppListing from '../app-components/Listing/AppListing';
import Vue from "vue";
Vue.component('teacher-group-listing', {
    mixins: [AppListing],
    methods: {
        showStudents:function (group_id, filter) {
            if (group_id>0){
                document.location='/admin/students/groups'+'/'+filter+'/'+group_id;
            }

        }
    }

});
