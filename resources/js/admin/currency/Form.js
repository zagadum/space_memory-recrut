import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('currency-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                code:  '' ,
                symbol:  '' ,

            }
        }
    }

});
