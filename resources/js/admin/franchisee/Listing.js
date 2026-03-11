import AppListing from '../app-components/Listing/AppListing';
//import Vue from "vue";

Vue.component('franchisee-listing', {
    mixins: [AppListing],
    methods: {
        showStudents:function (franchisee_id, filter) {
            if (franchisee_id>0){
                document.location='/admin/students/groups'+'/'+filter+'/'+franchisee_id;
            }

        },
        showGroups:function (franchisee_id, group_filter) {
            if (franchisee_id>0){
                document.location='/admin/teacher-groups/group_filter/'+group_filter+'/'+franchisee_id;
            }

        },
        showTeacher:function (franchisee_id, group_filter) {
            if (franchisee_id>0){
                document.location='/admin/teachers/teachers_by_filter/'+group_filter+'/'+franchisee_id;
            }

        },
        showTeachers:function (franchisee_id, franchisee_filter) {
            if (franchisee_id>0){
                document.location='/admin/teachers/'+franchisee_filter+'/'+franchisee_id;
            }

        }
    }
});

