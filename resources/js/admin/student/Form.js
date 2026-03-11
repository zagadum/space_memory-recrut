import AppForm from '../app-components/Form/AppForm';

import AppListing from '../app-components/Listing/AppListing';
import Vue from "vue";
$('body').on('click', '#isShowBlockList', function(){
    $('#blocklist_div').toggle();
});
Vue.component('student-payment-listing', {
    mixins: [AppListing]


});



Vue.component('student-form', {
    mixins: [AppForm],
    name: 'studentform',
    data: function() {
        return {
            form: {
                franchisee:  '' ,
                teacher:  '' ,
                groupSet:  '' ,

                franchisee_id:  '' ,
                twochildren_id:  '' ,
                group_id:  '' ,
                teacher_id:  '' ,
                email:  '' ,
                subcribe_email:  '' ,
                password:  '' ,
                surname:  '' ,
                lastname:  '' ,
                patronymic:  '' ,
                dob:  '' ,
                phone:  '' ,
                phone_country:  'UA' ,
                phoneSave:  '' ,
                phone1Save:  '' , //parent 1
                phone2Save:  '' , //parent 2

                date_finish:  '' ,
                sum_aboniment:  '' ,
                disount:  '' ,
                balance:  '' ,
                diams:  '' ,
                language:  'uk' ,

                parent1_surname:  '' ,
                parent1_lastname:  '' ,
                parent1_patronymic:  '' ,
                parent2_surname:  '' ,
                parent2_first_name:  '' ,
                parent2_patronymic:  '' ,
                parent3_surname:  '' ,
                parent3_first_name:  '' ,
                parent3_patronymic:  '' ,
                blocking_reason:  '' ,
                email_verified_at:  '' ,
                enabled:  false ,

                isTeacherDisabled:true,
                isGroupDisabled:true,
                fin_price_aboniment:0,
                fin_price_aboniment_discount:0,
                fin_currency:'UAH',
                sum_pay:0,
                discount:'',
                PayDiscount:'',
                PayDate:'',
                PayComment:'',
                is_twochildren:'',//chekbox
                twochildren:'',//Select
              //  start_day:'',
                start_day_group:'',
                type_payment:'',
                showDiscount:0,



            },
            isShowParent2:0,
            isShowParent3:0,
            isLoadingTeacher:false,
            isLoadingGroup:false,
            isLoadingTwoChildren:false,
            TeacherMe:[],
            GroupMe:[],
            TwoChildrenMe:[],
            StartInit:1,
            visiblePayForm:false,
            showPassword:false,
            prevGroup:[{ name: '', id: 0 }]
        }
    },
    methods: {
        CancelGroupSelected: function() {
            if (this.form.id>0){
                if  (this.prevGroup){

                    this.form.groupSet=   [{ name: this.prevGroup.name, id: this.prevGroup.id }];
                }

            }
        },
        GetInfoForDialogPayment: function() {
            return {'fin_price_aboniment_discount':this.form.fin_price_aboniment_discount,'fin_currency':this.form.fin_currency,'fin_price_aboniment':this.form.fin_price_aboniment};
        },
        ShowFormPay(type_payment){
            this.form.sum_pay=this.form.fin_price_aboniment_discount;
            if (this.form.PayDiscount && this.form.PayDiscount.discount>0) {
                if (this.form.form.discount > 0) {
                    this.form.sum_pay = this.form.fin_price_aboniment - (this.form.fin_price_aboniment * this.form.PayDiscount.discount / 100);
                }
            }
            if (type_payment=='offline'){
                this.visiblePayForm=true;
            }else{
                this.visiblePayForm=true;
            }
        },
        handlerPerent2Button(){
            this.isShowParent2 = !this.isShowParent2;
        },

        handlerPerent3Button(){
            this.isShowParent3 = !this.isShowParent3;
        },
        onChangePhone: function (payload) {
            this.form.phoneSave = payload;
        },
        onChangePhoneParent1: function (payload) {
            this.form.phone1Save = payload;
        },
        onChangePhoneParent2: function (payload) {
            this.form.phone2Save = payload;
        },
        nameFranchisee:function ({ first_name,surname }) {
            return `${surname} ${first_name}`
        },
        nameTeacher:function ({ first_name,surname }) {
            return `${surname} ${first_name}`
        },
        nameChildren:function ({ lastname,surname }) {
            return `${surname} ${lastname} `
        },

        nameGroup:function ({ name }) {
            return `${name}`
        },
        nameDiscount:function ({ name }) {
            return `${name}`
        },
        onChangeDiscount : function (discount) {
            this.form.sum_pay=this.form.fin_price_aboniment_discount;
            if (discount>0){
                    this.form.sum_pay=this.form.fin_price_aboniment-(this.form.fin_price_aboniment*this.form.PayDiscount.discount/100);
                    //this.form.sum_pay=this.form.fin_price_aboniment_discount-(this.form.fin_price_aboniment_discount*this.form.PayDiscount.discount/100);
            }
        },
        //----- Load Ajax Teacher by franchisee-id


        twoChildrenCheck: function (event) {
            var load=1;

            if (this.StartInit==0) {
                if (event.target.checked) {
                    load=1;
                    this.form.fin_price_aboniment_discount=parseInt(this.form.fin_price_aboniment)-parseInt(this.form.fin_price_aboniment)*10/100;
                }else{

                    this.TwoChildrenMe=[];
                    this.form.fin_price_aboniment_discount=parseInt(this.form.fin_price_aboniment);
                    load=0;
                }
            }

            this.form.sum_pay=this.form.fin_price_aboniment_discount;
            if ( this.form.PayDiscount && this.form.PayDiscount.discount>0){ //Без скидки на 2го ребенка
                    this.form.sum_pay=this.form.fin_price_aboniment-(this.form.fin_price_aboniment*this.form.PayDiscount.discount/100);
            }

            if (load==1){
                this.isLoadingTwoChildren=true;

                var FranchiseeId=this.form.franchisee_id;
                var student_id=0;
                if (this.form.id>0){
                    student_id=this.form.id;
                }
                if (this.form.franchisee.id){
                    FranchiseeId=this.form.franchisee.id;
                }

                //franchiseeID
                if(FranchiseeId>0){
                    var self = this;
                    $.ajax({
                        type:"GET",
                        contentType : 'application/json',
                        url:window.location.origin+"/admin/students/get-children/"+FranchiseeId+'/'+student_id,
                        success:function(res){
                            self.TwoChildrenMe=[];
                            if (res.children){
                                self.TwoChildrenMe=res.children;
                            }
                            self.isLoadingTwoChildren=false;

                        },
                        error: function (error) {
                            alert(JSON.stringify(error));
                        }
                    });
                }
            }

        },
        onChangeFranchisee: function (franchiseeID) {

            this.isLoadingTeacher=true;
            this.form.isTeacherDisabled=true;
            this.form.isGroupDisabled=true;

            if (this.StartInit==0) {

                this.form.groupSet = '';
                this.form.group_id = '';
                this.form.teacher = '';
                this.form.teacher_id = '';
                this.GroupMe=[];
                this.TeacherMe=[];

            }else{


                this.TwoChildrenMe=[];
                this.form.isTeacherDisabled=true;
            }
            this.isLoadingCity=true;

            var self = this;
            if(franchiseeID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/students/get-teacher-list/"+franchiseeID,
                    success:function(res){
                        self.TeacherMe=[];
                        self.GroupMe=[];
                        if (res.price){
                            if (res.price.fin_price_aboniment){
                                self.form.fin_price_aboniment=res.price.fin_price_aboniment;
                                self.form.fin_price_aboniment_discount=res.price.fin_price_aboniment;
                                if (self.form.is_twochildren==1) {
                                    self.form.fin_price_aboniment_discount = parseInt(self.form.fin_price_aboniment) - parseInt(self.form.fin_price_aboniment) * 10 / 100;
                                    self.form.sum_pay=self.form.fin_price_aboniment_discount;
                                }
                                self.form.showDiscount=1;
                                if (self.form.PayDiscount && self.form.PayDiscount.discount>0) { //без скидки на 2го реб
                                        self.form.sum_pay = self.form.fin_price_aboniment - (self.form.fin_price_aboniment * this.form.PayDiscount.discount / 100);
                                }

                            }

                            if (res.price.fin_price_aboniment){
                                self.form.fin_currency=res.price.fin_currency;
                            }
                        }

                        if (res.teacher){
                            self.TeacherMe=res.teacher;
                            self.form.isTeacherDisabled=false;
                        }
                        self.isLoadingTeacher=false;
                        self.isLoadingGroup=false;
                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        }, //end onchene
        onChangeGroupFx: function (groupId) {
            // this.isLoadingGroup=false;
            // this.form.isGroupDisabled=false;
            // console.log('onChangeGroupFx'+groupId);
            // this.form.group_id=groupId;
             if (this.form.id>0){
                 this.$modal.show('changeGroupModalStudent');
            }
        },
        //----- Load Ajax City by region-id
        onChangeTeacher: function (franchiseeID,TeacherID) {

            this.isLoadingGroup=true;

            if (this.StartInit==0) {
                this.form.group_id = '';
                this.form.groupSet = '';
                this.GroupMe=[];
            }

            var self = this;

            if(TeacherID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/students/get-groups-list/"+TeacherID,
                    success:function(res){
                        if (res.group){
                            self.GroupMe=res.group;
                            self.form.isGroupDisabled=false;
                        }else{
                            self.form.isGroupDisabled=true;
                        }

                        self.isLoadingGroup=false;

                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        },
        onOpenGroup: function () {

            if (this.form.groupSet){
                this.prevGroup=this.form.groupSet;
            }
        }
        //end onchene
    },
    created() {
        this.$root.$refs.studentform = this;
        if (this.StartInit==1){
            if (this.form.franchisee_id>0){

                this.onChangeFranchisee(this.form.franchisee_id);
                if (this.form.is_twochildren==1){
                    this.form.showDiscount=1;
                    this.twoChildrenCheck();
                }

            }else{
                if (this.form.franchisee.id>0){
                    this.onChangeFranchisee(this.form.franchisee.id);
                    if (this.form.is_twochildren==1){
                        this.form.showDiscount=1;
                        this.twoChildrenCheck();
                    }
                }
            }

            if (this.form.teacher_id>0){
                this.onChangeTeacher(this.form.franchisee_id,this.form.teacher_id);
            }else{
                // if (this.teacher.id>0){
                //     this.onChangeTeacher(this.form.franchisee.id,this.form.teacher.id);
                // }
            }


        }

        if (!this.form.id){
            this.form.isTeacherDisabled=true;
            this.form.isGroupDisabled=true;
        }

        if  (this.form.teacher.id>0){
            this.form.isTeacherDisabled=false;
        }
        if  (this.form.groupSet.id>0){
            this.form.isGroupDisabled=false;
        }

        this.StartInit=0;
        if (!(this.form.parent2_surname=== null)){
            this.isShowParent2=1;
        }

        if (this.form.parent2_surname==undefined || this.form.parent2_surname==''){
            this.isShowParent2=0;
        }
    }
});

Vue.component('modal-change-group-student', {
   // mixins: [AppForm],
    name: 'changeGroupModalStudent',
    data() {
        return {
            modalWidth: 656,
            resource_link:'',
        }
    },
    methods: {
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },
        PressBtnNo()  {
            this.$root.$refs.studentform.CancelGroupSelected();
            this.$modal.hide(this.$options.name);
        },
        PressBtnOK()  {
            this.$modal.hide(this.$options.name);
        }
    }

});


Vue.component('modal-delete-student', {
    name: 'deleteModalStudent',
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
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },

        deleteBtnOK()  {
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                axios.delete(this.resource_link).then(function (response) {

                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                   window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        }
    }

});

Vue.component('modal-offlinepayment-student', {
    name: 'paymentModalStudent',
    data() {
        return {
            modalWidth: 756,
            resource_link:'',
            dialogForm:{
                PayDiscount:'',
                payment_sum:0,
                payment_date:'',
                payment_period:'',
                comment:'',
                fin_price_aboniment:0,
                fin_price_aboniment_discount:0,
                fin_currency:'',
                sum_pay:0
            },
            submittedNames: []
        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;

            var me=this.$root.$refs.studentform.GetInfoForDialogPayment();
            this.dialogForm.fin_currency=me.fin_currency;
            this.dialogForm.fin_price_aboniment_discount=me.fin_price_aboniment_discount;
            this.dialogForm.fin_price_aboniment=me.fin_price_aboniment;
            this.dialogForm.sum_pay=this.dialogForm.fin_price_aboniment_discount;

        },
        onChangeDiscount : function (discount) {
            this.dialogForm.sum_pay=this.dialogForm.fin_price_aboniment_discount;
            if (discount>0) {
                    this.dialogForm.sum_pay = this.dialogForm.fin_price_aboniment - (this.dialogForm.fin_price_aboniment * this.dialogForm.PayDiscount.discount / 100);
                }

        },
        checkFormValidity() {
            this.errors = [];
            const valid = this.$refs.formPaymentStudent.checkValidity();
            if (!this.dialogForm.payment_date){
                this.errors['payment_date']="Name required.";
                return false;
            }
            if (!this.dialogForm.PayDiscount.id){
                this.errors.PayDiscount.push("Name required.");
                return false;
            }
           return valid;
        },
        handleSubmit(bvModalEvt) {
            // Exit when the form isn't valid
            bvModalEvt.preventDefault()
            if (!this.checkFormValidity()) {
                return;
            }

            this.$modal.hide(this.$options.name);

            if (this.resource_link){
                var _this7=this;
                var ParamsForm=JSON.stringify( this.dialogForm);

                axios.post(this.resource_link,ParamsForm).then(function (response) {
                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });

                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });
            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        }
    }

});
Vue.component('modal-bloking-student', {
    name: 'blokingModalStudent',
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
            const valid = this.$refs.formBlokingStudent.checkValidity()
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
                   window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});

Vue.component('modal-unblocking-student', {
    name: 'unblockingModalStudent',
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
        handleSubmit() {
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                axios.delete(this.resource_link).then(function (response) {
                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                    window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });
            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },
    }

});
Vue.component('modal-changediams', {
    name: 'ChangeDiamsModal',
    data() {
        return {
            dialogForm:{
                change_balance:'',
                description:'',
                balance:0,
            },
            // modalWidth: 656,
            resource_link:'',
        }
    },
    methods: {
        beforeOpen (event) {
            var _this7=this;
            this.resource_link=event.params.resource_link;
            axios.post(this.resource_link,{'get-balance':'1'}).then(function (response) {
                _this7.dialogForm.balance=response.data.diams;
            });
        },

        handleSubmit() {
        this.$modal.hide(this.$options.name);
            if (this.resource_link && this.dialogForm.change_balance!=0 && this.dialogForm.change_balance!=''){
                var _this7=this;
                var change_balance=this.dialogForm.change_balance;
                var description=this.dialogForm.description;
                axios.post(this.resource_link,{'change_balance':change_balance, 'description':description}).then(function (response) {

                    $('#balanceSpan_'+response.data.student_id).text(response.data.coins);
                    $('#diamsSpan_'+response.data.student_id).text(response.data.diams);


                    this.dialogForm.change_balance='';
                    this.dialogForm.description='';
                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });

            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});
Vue.component('modal-changecoins', {
    name: 'ChangeCoinsModal',
    data() {
        return {
            dialogForm:{
                change_balance:'',
                description:'',
                balance:0,
            },
            // modalWidth: 656,
            resource_link:'',


        }
    },
    methods: {
        beforeOpen (event) {
            this.resource_link=event.params.resource_link;
            this.dialogForm.balance=event.params.balance;
            var _this7=this;
            axios.post(this.resource_link,{'get-balance':'1'}).then(function (response) {
                _this7.dialogForm.balance=response.data.coins;
            });
        },

        handleSubmit() {
            this.$modal.hide(this.$options.name);
            if (this.resource_link && this.dialogForm.change_balance!=0 && this.dialogForm.change_balance!=''){
                var _this7=this;
                 var change_balance=this.dialogForm.change_balance;
                 var description=this.dialogForm.description;
                axios.post(this.resource_link,{'change_balance':change_balance, 'description':description}).then(function (response) {
                    $('#balanceSpan_'+response.data.student_id).text(response.data.coins);
                    $('#diamsSpan_'+response.data.student_id).text(response.data.diams);

                    this.dialogForm.change_balance='';
                    this.dialogForm.description='';
                    _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });
            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});
Vue.component('modal-changeemail', {
    name: 'ChangeEmailModal',
    data() {
        return {
            dialogForm:{
                change_email:'',
                change_email2:'',
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
            // this.errors = [];
            const valid = this.$refs.formChangeEmailStudent.checkValidity();
            if (!this.dialogForm.change_email || !this.dialogForm.change_email2 || this.dialogForm.change_email !== this.dialogForm.change_email2){
                // this.errors.push("change_balance");
                return false;
            }
            return valid;
        },


        handleSubmit() {

            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {

                return
            }
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                var change_email=this.dialogForm.change_email;
                // var description=this.dialogForm.description;
                //var ParamsForm=JSON.stringify( this.dialogForm);

                axios.post(this.resource_link,{'change_email':change_email}).then(function (response) {

                    //   $('#balanceSpan').text(`${response.data}`);
                    this.dialogForm.change_email='';
                    this.dialogForm.change_email2='';

                    if(response.data.messageExist){
                        _this7.$notify({ type: 'error', title: 'Success!', text: response.data.messageExist });
                    }
                    if(response.data.messageSuccess){
                        _this7.$notify({ type: 'success', title: 'Success!', text: response.data.messageSuccess });
                        $('#email').val(`${response.data.newEmail}`);
                    }

                    // _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                    // window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});
Vue.component('modal-changeemail1', {
    name: 'ChangeEmailModal1',
    data() {
        return {
            dialogForm:{
                change_email:'',
                change_email2:'',
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
            // this.errors = [];
            const valid = this.$refs.formChangeEmailStudent.checkValidity();
            if (!this.dialogForm.change_email || !this.dialogForm.change_email2 || this.dialogForm.change_email !== this.dialogForm.change_email2){
                // this.errors.push("change_balance");
                return false;
            }
            return valid;
        },


        handleSubmit() {

            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {

                return
            }
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                var change_email=this.dialogForm.change_email;
               // var description=this.dialogForm.description;
                //var ParamsForm=JSON.stringify( this.dialogForm);

                axios.post(this.resource_link,{'change_email':change_email}).then(function (response) {

                 //   $('#balanceSpan').text(`${response.data}`);
                    this.dialogForm.change_email='';
                    this.dialogForm.change_email2='';

                    if(response.data.messageExist){
                        _this7.$notify({ type: 'error', title: 'Success!', text: response.data.messageExist });
                    }
                    if(response.data.messageSuccess){
                        _this7.$notify({ type: 'success', title: 'Success!', text: response.data.messageSuccess });
                        $('#email').val(`${response.data.newEmail}`);
                    }

                    // _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                    // window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });


            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});



Vue.component('modal-changepassword-form', {
    name: 'ChangePasswordModal',
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
        // checkFormValidity() {
        //     // if (this.dialogForm.change_balance) {
        //     //     return true;
        //     // }
        //     // this.err = [];
        //     // if (!this.change_balance) {
        //     //     this.err['change_balance'] ='Требуется указать количество.'
        //     //     //.push('Требуется указать количество.');
        //     // }
        //     // e.preventDefault();
        //     // const valid = this.$refs.formUnblockingStudent.checkValidity()
        //     // //this.nameState = valid
        //     // return valid
        //
        //     const valid = this.$refs.formBlokingStudent.checkValidity()
        //     //this.nameState = valid
        //     return valid
        // },

        // checkFormValidity() {
        //     // this.errors = [];
        //     const valid = this.$refs.formChangeEmailStudent.checkValidity();
        //     if (!this.dialogForm.change_email || !this.dialogForm.change_email2 || this.dialogForm.change_email !== this.dialogForm.change_email2){
        //         // this.errors.push("change_balance");
        //         return false;
        //     }
        //     return valid;
        // },


        PressOK() {

         let a =   $('#password').val();



            // // Exit when the form isn't valid
            // if (!this.checkFormValidity()) {
            //     console.log("this.dialogForm.change_email2");
            //     return
            // }
            this.$modal.hide(this.$options.name);
            if (this.resource_link){
                var _this7=this;
                var change_password= $('#password').val();

                axios.post(this.resource_link,{'change_password':change_password}).then(function (response) {

                    //   $('#balanceSpan').text(`${response.data}`);
                    // this.dialogForm.change_email='';
                    // this.dialogForm.change_email2='';
                    //
                    if(response.data.messageError){
                        _this7.$notify({ type: 'error', title: 'Success!', text: response.data.messageError });
                    }
                    if(response.data.messageSuccess){
                        _this7.$notify({ type: 'success', title: 'Success!', text: response.data.messageSuccess });
                        $('#email').val(`${response.data.newEmail}`);
                    }

                    // _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                    // window.location='/admin/students';
                }.bind(this), function (error) {
                    _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                });

                // axios.delete(this.resource_link).then(function (response) {
                //
                //     _this7.$notify({ type: 'success', title: 'Success!', text: response.message ? response.data.message : 'Item successfully deleted.' });
                //     window.location='/admin/students';
                // }.bind(this), function (error) {
                //     _this7.$notify({ type: 'error', title: 'Error!', text: error.response.message ? error.response.data.message : 'An error has occured.' });
                // });


            }
        },
        clicktoclose() {
            this.$modal.hide(this.$options.name);
        },


    }

});
