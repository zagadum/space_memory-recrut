import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('region-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                country_id:  '' ,
                enabled:  false ,

            }
        }
    }

});
