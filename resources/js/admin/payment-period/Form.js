import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('payment-period-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                discount:  '' ,
                term:  '' ,

            }
        }
    }

});
