<modal-delete-student v-cloak inline-template >
    <modal name="deleteModalStudent"    :adaptive="true" :focus-trap="false" @before-open="beforeOpen" class="admin-modal" >
            <button type="button" class="btn-close" @click="clicktoclose" ><img src="{{asset('images/x.svg')}}"/></button>
            <div class="box">


                <p class="box-message">{{ trans('admin.modal.delete.title') }}</p>
                <div class="box-btn">
                    <button type="button" class="btn box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                    <button class="btn box-btn_primary" type="button"   @click="deleteBtnOK">{{ trans('admin.btn.ok') }}</button>
                </div>
            </div>
        </modal>
</modal-delete-student>
