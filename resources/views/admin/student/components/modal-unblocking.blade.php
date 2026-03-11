<modal-unblocking-student v-cloak inline-template>
    <modal name="unblockingModalStudent"  class="admin-modal" transition="pop-out" :focus-trap="false" :height="355" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="clicktoclose"><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">
            <p class="box-title">{{ trans('admin.modal.unbloking_student.title') }}</p>
            <form method="post" id="formUnblockingStudent"  ref="formUnblockingStudent" @submit.stop.prevent="handleSubmit">
                <div class="box-btn">
                    <button type="button" class="btn box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                    <button type="submit"  class="btn  box-btn_primary" >{{ trans('admin.btn.unlock') }}</button>
                </div>
            </form>
        </div>

    </modal>
</modal-unblocking-student>
