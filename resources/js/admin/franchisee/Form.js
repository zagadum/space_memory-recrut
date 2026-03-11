import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";

// $('body').on('click', '.password-control', function (e) {
//     e.preventDefault();
//
//     const $input = $('#password');
//
//     alert($input);
//     alert($input.prop('type'));
//     if ($input.prop('type') == 'password') {
//         $(this).addClass('view');
//         console.log('change to text');
//         console.log('change to text');
//         $input.prop('type', 'text');
//         alert($input.prop('type'));
//     } else {
//         $(this).removeClass('view');
//         $input.prop('type', 'password');
//     }
// });

Vue.component('franchisee-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                surname:  '' ,
                first_name:  '' ,
                patronymic:  '' ,
                country_id:  '' ,
                region_id:  '' ,
                city_id:  '' ,
                phone:  '' ,

                phone_country:  'UA' ,
                phoneSave:  '' ,
                email:  '' ,
                password:  '' ,
                fin_royalty:  '' ,
                fin_pr:  '' ,
                fin_legal:  '' ,
                fin_address:  '' ,
                fin_vid:  '' ,
                fin_regno:  '' ,
                fin_price_aboniment:  '' ,
                fin_currency:  '' ,
                passport:  '' ,
                iin:  '' ,
                subscibe_email:  '' ,
                language:  '' ,
                enabled:  true ,
                country:  '' ,
                region:  '' ,
                city:  '' ,
                 currency:  '' ,
                isRegionDisabled: true,
                isCityDisabled: true,


            },
            isLoadingCity:false,
            isLoadingRegion:false,
            RegionMe:[],
            CityMe:[],
            StartInit:1,
            isShowPodrazdel:0,
            showPassword: false,

        }
    },
    methods: {
        onChangePhone: function (payload) {
            this.form.phoneSave = payload;
        },
        //----- Load Ajax Region by country-id
        onChangeCountry: function (countryID) {
            this.isLoadingRegion=true;
            if (this.StartInit==0) {
                this.form.region_id = '';
                this.form.city_id = '';
                 this.form.city = '';
                this.form.region = '';
            }
            this.RegionMe=[];
            this.CityMe=[];
            this.form.isCityDisabled=true;
            var self = this;
            if(countryID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/franchisees/get-region-list/"+countryID,
                    success:function(res){
                        self.RegionMe=res;
                        self.CityMe=[];
                         self.isLoadingRegion=false;
                        self.form.isRegionDisabled=false;
                        self.form.isCityDisabled=false;
                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        }, //end onchene

        //----- Load Ajax City by region-id
        onChangeRegion: function (regionID) {

            this.isLoadingCity=true;

           // console.log('onChangeRegion');
            if (this.StartInit==0){
                this.form.city_id='';
                this.form.city='';
            }

            this.CityMe=[];
            var self = this;
            if(regionID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/franchisees/get-city-list/"+regionID,
                    success:function(res){

                        self.CityMe=res;
                        self.isLoadingCity=false;
                        self.form.isCitDisabled=false;
                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        },//end onchene

        handlerPodrazdelButton(){
            this.isShowPodrazdel = !this.isShowPodrazdel;
        }
    },
    created() {
        if (this.StartInit==1){
            if (this.form.country_id>0){
                this.onChangeCountry(this.form.country_id);
                if (this.form.region_id>0){
                    this.onChangeRegion(this.form.region_id);
                }
            }
        }
        this.StartInit=0;

    }
})


Vue.component('modal-changeemail', {
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

Vue.component('modal-changepassword-form', {
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


Vue.component('modal-blocking-franchisee', {
    name: 'blockingModalFranchisee',
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

                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully locked.' });
                    window.location='/admin/franchisees';
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

Vue.component('modal-unblocking-franchisee', {
    name: 'unblockingModalFranchisee',
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
                    window.location='/admin/franchisees';
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

Vue.component('modal-disablebloking-franchisee', {
    name: 'disableBlockingModalFranchisee',
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

Vue.component('modal-disabledelete-franchisee', {
    name: 'disableDeleteModalFranchisee',
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
