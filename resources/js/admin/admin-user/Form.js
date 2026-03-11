import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('admin-user-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                first_name:  '' ,
                last_name:  '' ,
                email:  '' ,
                password:  '' ,
                activated:  false ,
                forbidden:  false ,
                language:  '' ,

            }
        }
    }
});
