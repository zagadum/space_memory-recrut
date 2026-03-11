import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('user-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {

            }
        }
    }
});
