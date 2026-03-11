import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('city-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                country_id:  '' ,
                region_id:  '' ,
                enabled:  false ,

            }
        }
    }

});
