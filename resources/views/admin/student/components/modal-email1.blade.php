<modal-changeemail1 v-cloak inline-template >
    <modal class="admin-modal" name="ChangeEmailModal1" transition="pop-out"   :focus-trap="false" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="clicktoclose" ><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">
            <p class="box-title">Изменить Емаил</p>


                <form method="post"   @submit.stop.prevent="handleSubmit" ref="formChangeEmailStudent">
                <div class="form-group row align-items-baseline">
                    <div class="col-md-12">
                        <div class="dis-flex">
                             <input type="text"  v-validate="'required|email'"  v-model="dialogForm.change_email"  class="form-control" placeholder="Новый email" id="change_email" name="change_email">
                        </div>
                        <div v-if="errors.has('change_email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('change_email') }}
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-baseline">
                    <div class="col-md-12">
                        <div class=" dis-flex">
                            <input type="text" v-validate="'required|email'"  v-model="dialogForm.change_email2" class="form-control" placeholder="Повторить новый email"
                                   id="change_email2" name="change_email2" ref="change_email2">
                        </div>
                    </div>
                </div>

                    <div class="box-btn">
                        <button type="button" class="box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                        <button class="box-btn_primary" type="submit" >{{ trans('admin.btn.yes') }}</button>
                    </div>

            </form>


        </div>

{{--        <div class="box" style="display: none" >--}}
{{--            <p class="box-message">Новый пароль отправлен на почту пользователя.</p>--}}

{{--            <div class="box-btn">--}}
{{--                <button class="btn box-btn_primary" @click="PressOK">Хорошо</button>--}}
{{--            </div>--}}
{{--        </div>--}}
    </modal>
</modal-changeemail1>

