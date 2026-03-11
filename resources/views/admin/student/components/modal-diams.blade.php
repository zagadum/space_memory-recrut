
<modal-changediams v-cloak inline-template >
    <modal class="admin-modal" name="ChangeDiamsModal" transition="pop-out"  :height="430" :focus-trap="false" @before-open="beforeOpen">

        <button type="button" class="btn-close" @click="clicktoclose" ><img src="{{asset('images/x.svg')}}"></button>
        <div class="box">
            <p class="box-title">{{ trans('admin.student.changeBalance.tittle') }}</p>
            <div class="row" >
                <div class="col-12" style="font-weight: bold" >
                    {{ trans('admin.student.changeBalance.current_balance_diams') }}: <span v-text="dialogForm.balance"> </span>
                </div>
            </div>


            <form style="margin-top: 1em" method="post"  @submit.stop.prevent="handleSubmit" ref="formChangeBalanceStudent" >
                <div class="form-group row align-items-baseline" >
                    <div class="col-md-12">
                        <label for="change_balance" class="col-form-label text-md-left col-md-5" >{{ trans('admin.student.changeBalance.number') }}* </label>
                        <div class="dis-flex">
                            <input type="number"  v-validate="'required'"   v-model="dialogForm.change_balance"   class="form-control"   id="change_balance" name="change_balance">
                        </div>

                    </div>
                </div>

                <div class="form-group row align-items-baseline">
                    <div class="col-md-12">
                        <label for="description" class="col-form-label text-md-left col-md-4">{{ trans('admin.student.changeBalance.description') }}
                            </label>
                        <div class=" dis-flex">
                            <input type="text" class="form-control" v-model="dialogForm.description" class="form-control-success" id="description" name="description" ref="description">
                        </div>
                    </div>
                </div>
{{--                <div class="form-group row align-items-baseline">--}}
{{--                    <div class="box-btn" style="margin-top: 1pt">--}}
{{--                        <button class="box-btn_primary" type="button" >История</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="box-btn"  style="margin-top: 1pt">
                    <button type="button" class="box-btn_subprimary" @click="clicktoclose">{{ trans('admin.btn.cancel') }}</button>
                    <button class="box-btn_primary" type="submit" >{{ trans('admin.btn.yes') }}</button>
                </div>

            </form>


        </div>

    </modal>
</modal-changediams>

