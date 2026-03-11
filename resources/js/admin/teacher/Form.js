import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";

Vue.component('teacher-form', {
    mixins: [AppForm],
    data: function() {
        return {
            showPassword: false,
            form: {
                franchisee_id:  '' ,
                surname:  '' ,
                first_name:  '' ,
                patronymic:  '' ,
                phone:  '' ,
                phone_country:  'UA' ,
                phoneSave:  '' ,
                dob:  '' ,
                email:  '' ,
                password:  '' ,
                passport:  '' ,
                iin:  '' ,
                subscibe_email:  '' ,
                language:  'uk' ,
                fin_cabinet:  false ,
                enabled:  false ,
                phoneNumber: "",

            },
            datePickerConfigMe:{ firstDayOfWeek:1,
                dateFormat: 'Y-m-d H:i:S',
                altInput: true,
                weekNumbers:1,
                altFormat: 'd.m.Y',
                locale: 'ua'}


        }
    },
    methods: {
        onChangePhone: function (payload) {
            //this.form.phoneNumber = payload.formattedNumber.replace(/\D/g, '');
            this.form.phoneSave=payload;

            //this.$refs.phone_number.$el.value = this.form.phoneNumber ;
        },
        nameFranchisee:function ({ first_name,surname }) {
            return `${surname} ${first_name}`
        }

    }
});

Vue.component('modal-delete-teacher', {
    name: 'deleteModalTeacher',
    data() {
        return {
            modalWidth: 656,
            resource_link:'',
            show_div:'rm_text',
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            if (event.params.show_div){
                this.show_div=event.params.show_div;
            }

        },
        click_to_close() {
            this.$modal.hide(this.$options.name);
        },

        deleteBtnOK()  {
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                axios.delete(this.resource_link).then(function (response) {

                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                       window.location='/admin/teachers';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        }
    }

});
Vue.component('modal-change-password-form', {
    name: 'ChangePasswordModal',
    data() {
        return {
            modalWidth: 656,
            nemail:'test'
        }
    },
    created() {
        //  this.modalWidth = window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
        beforeOpen (event) {
            console.log(event.params.foo);
            this.nemail='test@ua.fm';
            //$("#n-email").val(event.params.foo);//Set Params
        },
        signIn() {
            alert('Sign in')
        },
        register() {
            alert('Register');
            this.$notify({ type: 'success', title: 'Сохранено!', text: 'тест сообщение супер'});
        },
        close() {
            this.$modal.hide(this.$options.name);//ChangePasswordModal
        },

    }
})

Vue.component('modal-bloking-teacher', {
    name: 'blokingModalTeacher',
    data() {
        return {
            dialogForm:{
                blocking_reason:null,
            },
            modalWidth: 656,
            resource_link:'',
            show_div:'rm_text',
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            if (event.params.show_div){
                this.show_div=event.params.show_div;
            }

        },
        checkFormValidity() {
            const valid = this.$refs.formBlokingTeacher.checkValidity()
            //this.nameState = valid
            return valid
        },
        handleSubmit() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                var blocking_reason=this.dialogForm.blocking_reason;
                //var ParamsForm=JSON.stringify( this.dialogForm);
                axios.post(this.resource_link,{'blocking_reason':blocking_reason}).then(function (response) {

                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                    window.location='/admin/teachers';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        },
        click_to_close() {
            this.$modal.hide(this.$options.name);
        }
    }

});

Vue.component('modal-disablebloking-teacher', {
    name: 'disableBlockingModalTeacher',
    data() {
        return {
            modalWidth: 656,
            resource_link:'',
            show_div:'rm_text',
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            if (event.params.show_div){
                this.show_div=event.params.show_div;
            }

        },
        click_to_close() {
            this.$modal.hide(this.$options.name);
        }
    }

});

Vue.component('modal-changeemail-form', {
    // mixins: [AppForm],
    name: 'ChangeEmailModal',
    data() {
        return {
            modalWidth: 656,
            nemail:'test',
            email:''
        }
    },
    // validations: {
    //     form: {
    //
    //         email: { required, email }
    //     }
    // },
    created() {
        // this.modalWidth = window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
        beforeOpen (event) {
            //console.log(event.params.foo);
            //this.nemail='test@ua.fm';
            //$("#n-email").val(event.params.foo);//Set Params
        },
        PressOK() {
            this.$modal.hide(this.$options.name);
            this.$notify({ type: 'success', title: 'Сохранено!', text: 'Email изменен'});
        },
        closeDialog() {
            this.$modal.hide(this.$options.name);
        },
        checkForm() {
            this.$validator.validateAll()
            this.$modal.hide(this.$options.name);
            this.$notify({ type: 'success', title: 'Сохранено!', text: 'тест сообщение супер'});
        },

    }
})

Vue.component('modal-change-password-form', {
    name: 'ChangePasswordModal',
    data() {
        return {
            modalWidth: 656,
            nemail:'test',
        }
    },
    created() {
        //  this.modalWidth = window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
        beforeOpen (event) {
            console.log(event.params.foo);
            this.nemail='test@ua.fm';
            //$("#n-email").val(event.params.foo);//Set Params
        },
        PressOK() {
            this.$modal.hide(this.$options.name);
            this.$notify({ type: 'success', title: 'Сохранено!', text: 'Ссылка для потверждения пароля прислана на почту'});

        },
        closeDialog() {
            this.$modal.hide(this.$options.name);//ChangePasswordModal
        },

    }
})

Vue.component('modal-disabledelete-teacher', {
    name: 'disableDeleteModalTeacher',
    data() {
        return {
            modalWidth: 656,
            resource_link:'',
            show_div:'rm_text',
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            if (event.params.show_div){
                this.show_div=event.params.show_div;
            }

        },
        click_to_close() {
            this.$modal.hide(this.$options.name);
        }
    }

});

Vue.component('modal-unblocking-teacher', {
    name: 'unblockingModalTeacher',
    data() {
        return {
            dialogForm:{
                blocking_reason:null,
            },
            modalWidth: 656,
            resource_link:'',
            show_div:'rm_text',
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            if (event.params.show_div){
                this.show_div=event.params.show_div;
            }

        },
        // checkFormValidity() {
        //     const valid = this.$refs.formBlokingStudent.checkValidity()
        //     //this.nameState = valid
        //     return valid
        // },
        handleSubmit() {
            // Exit when the form isn't valid
            // if (!this.checkFormValidity()) {
            //     return
            // }
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                axios.delete(this.resource_link).then(function (response) {

                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully unlocked.' });
                    window.location='/admin/teachers';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });
            }
        },
        click_to_close() {
            this.$modal.hide(this.$options.name);
        },
    }

});
