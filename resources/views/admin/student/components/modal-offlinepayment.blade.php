<modal-offlinepayment-student v-cloak inline-template>
    <modal name="paymentModalStudent" class="admin-modal" transition="pop-out" :focus-trap="false" @before-open="beforeOpen" :height="597" :width="756"  >

        <button type="button" class="btn-close" @click="clicktoclose"><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">

            <p class="box-title">Добавить Оплату оффлайн</p>

            <form method="post" ref="formPaymentStudent" @submit.stop.prevent="handleSubmit">
                <div class="form-group row">
                    <div class="col-md-12">
                        <label for="payment_date">Дата оплаты*</label>
                        <div class="dis-flex">
                            <datetime v-model="dialogForm.payment_date"
                                      :config="{ firstDayOfWeek:1,  dateFormat: 'Y-m-d H:i:S',   altInput: true,      weekNumbers:false,   altFormat: 'd.m.Y',    locale: '{{\App\Helpers\SiteHelper::GetLang()}}'}"
                                      v-validate="'required'"
                                      class="flatpickr"
                                      :class="{'form-control-danger': errors.has('payment_date'), 'form-control-success': fields.payment_date && fields.payment_date.valid}"
                                      id="payment_date" name="payment_date"
                                      placeholder="Дата оплаты"></datetime>
                            <div v-if="errors.has('payment_date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('payment_date') }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <label for="payment_period">Период*</label>
                        <div class="dis-flex">
                            <multiselect v-model="dialogForm.PayDiscount" :options="{{ $Discount->toJson() }}"
                                         @input="onChangeDiscount(dialogForm.PayDiscount.discount)"
                                         track-by="id"
                                         label="name"
                                         v-validate="{ required: true }"
                                         tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                         placeholder="{{ trans('admin.forms.select_options') }}"
                                         :show-labels="false" open-direction="bottom"></multiselect>
                            <div v-if="errors.has('PayDiscount')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('PayDiscount') }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <label for="payment_sum" class="payment_sum">Сума абонементу: <span v-html="dialogForm.sum_pay" style="float:left">0</span><span v-html="dialogForm.fin_currency" style="float:left">-</span></label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <textarea class="form-control" v-model="dialogForm.comment" name="comment" placeholder="Комментарій" style="margin: 0"></textarea>
                    </div>
                </div>


                <div class="box-btn">
                    <button type="button" class="btn box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                    <button type="submit" class="btn  box-btn_primary">Сменить</button>
                </div>
            </form>


        </div>
    </modal>
</modal-offlinepayment-student>
