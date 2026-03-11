<modal-change-group-student v-cloak inline-template >
    <modal name="changeGroupModalStudent"     :adaptive="true" :focus-trap="false"  class="admin-modal" >
            <button type="button" class="btn-close" @click="clicktoclose" ><img src="{{asset('images/x.svg')}}"/></button>
            <div class="box">


                <p class="box-message">{{ trans('admin.modal.change_group.title') }}</p>
                <div class="box-btn">
                    <button type="button" class="btn box-btn_subprimary" @click="PressBtnNo">{{ trans('admin.btn.cancel') }}</button>
                    <button class="btn box-btn_primary" type="button"   @click="PressBtnOK">{{ trans('admin.btn.yes') }}</button>
                </div>
            </div>
        </modal>
</modal-change-group-student>
