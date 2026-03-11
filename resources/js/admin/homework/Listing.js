import AppForm from '../app-components/Form/AppForm';
import AppListing from '../app-components/Listing/AppListing';
  import Vue from "vue";
Vue.component('homework-listing', {
  mixins: [AppListing],
    methods: {

    }
});



Vue.component('modal-duplicate-win', {
      name: 'duplicateModal',
    data() {
          return {
              modalWidth: 756,

              resource_link:'',
              dialogForm:
                  {
                      move_date:''
                  },
          }
      },
      methods: {
          beforeOpen (event) {
              //this.resource_link=event.params.resource_link;

              //var me=this.$root.$refs.studentform.GetInfoForDialogPayment();
              //this.dialogForm.fin_currency=me.fin_currency;
              //this.dialogForm.fin_price_aboniment_discount=me.fin_price_aboniment_discount;
              //this.dialogForm.fin_price_aboniment=me.fin_price_aboniment;
              //this.dialogForm.sum_pay=this.dialogForm.fin_price_aboniment_discount;

          },
        checkFormValidity() {
        //this.errors = [];
        const valid = this.$refs.formMoveTask.checkValidity();
        if (!this.dialogForm.move_date){
            //this.errors['move_date']="Name required.";
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

              this.$modal.hide('duplicateModal');
                  var _this7=this;
                   axios.post('/admin/homework/dublicate',{'move_date': this.dialogForm.move_date}).then(function (response) {
                       console.log(response.data);
                       if (response.data.success==1){
                           _this7.$notify({ type: 'success', title: 'Success!', text: response.data.message ? response.data.message : 'Item successfully dublicate.' });
                           document.location='/admin/homework';
                       }else{
                           _this7.$notify({ type: 'error', title: 'Error!', text: response.data.message ?  response.data.message : 'An error has occured.' });
                       }

                  }, function (error) {
                       _this5.$notify({ type: 'error', title: 'Error!', text: error.response.data.message ? error.response.data.message : 'An error has occured.' });
                   });

          },
          closedialog() {
              this.$modal.hide('duplicateModal');
          },

      }

  });
