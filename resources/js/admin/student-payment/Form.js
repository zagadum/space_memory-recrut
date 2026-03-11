import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('student-payment-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                student_id:  '' ,
                date_pay:  '' ,
                date_finish:  '' ,
                sum_aboniment:  '' ,
                type_aboniment:  '' ,
                type_pay:  '' ,
                enabled:  false ,

            }
        }
    }

});
