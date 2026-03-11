import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('country-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                enabled:  false ,

            }
        }
    }

});
