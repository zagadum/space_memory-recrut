import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";
Vue.component('profile-edit-profile-form', {
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

            },
            mediaCollections: ['avatar']
        }
    },
    methods: {
        onSuccess(data) {
            if(data.errorMessage) this.$notify({ type: 'error', title: 'Error!', text: data.errorMessage });
            else this.$notify({ type: 'success', title: 'Success!', text: data.message });
            if(data.notify) {
                console.log(data);
                _this7.$notify({ type: 'error', title: 'Success!', text: response.data.messageError });
                // this.$notify({ type: data.notify.type, title: data.notify.title, text: data.notify.message});
            } else if (data.redirect) {
                window.location.replace(data.redirect);
            }
        }
    }
});
