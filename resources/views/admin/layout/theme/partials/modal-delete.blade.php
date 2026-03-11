<template>
    <delete-modal v-cloak inline-template>
        <modal name="deleteModal" :click_to_close="false" :adaptive="true" :focus-trap="false" class="admin-modal">

            <button type="button" class="btn-close" @click="$modal.hide('deleteModal')"><img src="{{asset('images/x.svg')}}"/>
            </button>
            <div class="box">

                <p class="box-message">{{ trans('admin.modal.delete.title') }}</p>
                <form method="post" @submit.prevent="checkForm">
                    <div class="form-group row align-items-baseline">
                        <div class="col-md-12">
                            <div class="dis-flex">
                                <textarea placeholder="Причина блокировки..."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="box-btn">
                    <button class="box-btn_primary" type="button" @click="$modal.hide('deleteModal')">{{ trans('admin.btn.cancel') }}</button>
                    <button class="box-btn_primary" type="button" @click="deleteBtnOK">{{ trans('admin.btn.ok') }}</button>
{{--                    <button type="button" class="box-btn_subprimary" @click="$modal.hide('deleteModal')">Отмена</button>--}}

{{--                    <button class="box-btn_primary" type="button" @click="deleteBtnOK">deleteBtnOK</button>--}}

{{--                    <button class="box-btn_primary" type="button" @click="delete_btn_ok">delete_btn_ok</button>--}}
                    {{--                        <button class="box-btn_primary" type="button" @click="$emit('delete_btn_ok')">$emit</button>--}}
                </div>
            </div>

        </modal>
    </delete-modal>



</template>


