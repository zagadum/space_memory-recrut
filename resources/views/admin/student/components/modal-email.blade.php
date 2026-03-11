<modal-changeemail v-cloak inline-template >
    <modal class="admin-modal" name="ChangeEmailModal" transition="pop-out"   :focus-trap="false" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="closeDialog" ><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">
            <p class="box-title">Изменить Емаил</p>

             <form method="post"  ref="formEmailStudent" @submit.stop.prevent="handleSubmit">
                <div class="form-group row align-items-baseline" :class="{'has-danger': errors.has('change_email'), 'has-success': fields.change_email && fields.change_email.valid }">
                    <div class="col-md-12">
                        <div class="dis-flex">
                             <input type="text"  v-validate="'required|email'"  v-model="form.change_email"  @input="validate($event)" class="form-control" placeholder="Новый email" :class="{'form-control-danger': errors.has('change_email'), 'form-control-success': fields.change_email && fields.change_email.valid}" id="change_email" name="change_email">
                        </div>
                        <div v-if="errors.has('change_email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('change_email') }}
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-baseline"
                     :class="{'has-danger': errors.has('change_email2'), 'has-success': fields.change_email2 && fields.change_email2.valid }">
                    <div class="col-md-12">
                        <div class=" dis-flex">
                            <input type="text" class="form-control" placeholder="Повторить новый email"
                                   :class="{'form-control-danger': errors.has('change_email2'), 'form-control-success': fields.change_email2 && fields.change_email2.valid}"
                                   id="change_email2" name="change_email2" ref="change_email2">
                        </div>
                        <div v-if="errors.has('change_password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('change_password') }}
                        </div>
                    </div>
                </div>
            </form>



            <div class="box-btn">
                <button type="btn button" class="box-btn_subprimary" @click="closeDialog">{{ trans('admin.btn.cancel') }}</button>
                <button class="btn box-btn_primary" type="button" @click="PressOK">{{ trans('admin.btn.yes') }}</button>
            </div>
        </div>

        <div class="box" style="display: none" >
            <p class="box-message">Новый пароль отправлен на почту пользователя.</p>

            <div class="box-btn">
                <button class="btn box-btn_primary" @click="PressOK">{{ trans('admin.btn.yes') }}</button>
            </div>
        </div>
    </modal>
</modal-changeemail>

