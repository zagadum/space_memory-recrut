<modal-bloking-student v-cloak inline-template>
    <modal name="blokingModalStudent"  class="admin-modal" transition="pop-out" :focus-trap="false" :height="355" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="clicktoclose"><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">
            <p class="box-title">{{ trans('admin.modal.bloking_student.title') }}</p>
            <form method="post" id="formBlokingStudent"  ref="formBlokingStudent" @submit.stop.prevent="handleSubmit">
                <div class="form-group row">
                    <div class="col-md-12">
                        <label for="payment_sum">{{ trans('admin.student.columns.blocking_reason') }}</label>
                        <div class="dis-flex">
                             <textarea type="text" class="form-control" v-model="dialogForm.blocking_reason"  cols="45" rows="10"   placeholder="{{ trans('admin.student.columns.blocking_reason') }}" id="blocking_reason" name="blocking_reason"></textarea>
                        </div>
                    </div>
                </div>
                <div class="box-btn">
                    <button type="button" class="btn box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                    <button type="submit"  class="btn  box-btn_primary" >{{ trans('admin.btn.block') }}</button>
                </div>
            </form>



        </div>
    </modal>
</modal-bloking-student>
