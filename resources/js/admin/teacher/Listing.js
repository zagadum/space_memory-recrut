import AppListing from '../app-components/Listing/AppListing';
import Vue from "vue";
Vue.component('teacher-listing', {
    mixins: [AppListing],
    methods: {
        showStudents:function (teacher_id, filter) {
            if (teacher_id>0){
                document.location='/admin/students/groups'+'/'+filter+'/'+teacher_id;
            }

        },
        showGroups:function (teacher_id, group_filter) {
            if (teacher_id>0){
                document.location='/admin/teacher-groups/group_filter/'+group_filter+'/'+teacher_id;
            }

        }
    }

});
