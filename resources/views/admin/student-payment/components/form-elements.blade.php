<div class="form-group row align-items-center" :class="{'has-danger': errors.has('student_id'), 'has-success': fields.student_id && fields.student_id.valid }">
    <label for="student_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.student_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.student_id" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('student_id'), 'form-control-success': fields.student_id && fields.student_id.valid}" id="student_id" name="student_id" placeholder="{{ trans('admin.student-payment.columns.student_id') }}">
        <div v-if="errors.has('student_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('student_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('date_pay'), 'has-success': fields.date_pay && fields.date_pay.valid }">
    <label for="date_pay" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.date_pay') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-sm-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.date_pay" :config="datePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('date_pay'), 'form-control-success': fields.date_pay && fields.date_pay.valid}" id="date_pay" name="date_pay" placeholder="{{ trans('admin.forms.select_a_date') }}"></datetime>
        </div>
        <div v-if="errors.has('date_pay')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('date_pay') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('date_finish'), 'has-success': fields.date_finish && fields.date_finish.valid }">
    <label for="date_finish" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.date_finish') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-sm-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.date_finish" :config="datePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('date_finish'), 'form-control-success': fields.date_finish && fields.date_finish.valid}" id="date_finish" name="date_finish" placeholder="{{ trans('admin.forms.select_a_date') }}"></datetime>
        </div>
        <div v-if="errors.has('date_finish')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('date_finish') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sum_aboniment'), 'has-success': fields.sum_aboniment && fields.sum_aboniment.valid }">
    <label for="sum_aboniment" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.sum_aboniment') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sum_aboniment" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sum_aboniment'), 'form-control-success': fields.sum_aboniment && fields.sum_aboniment.valid}" id="sum_aboniment" name="sum_aboniment" placeholder="{{ trans('admin.student-payment.columns.sum_aboniment') }}">
        <div v-if="errors.has('sum_aboniment')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sum_aboniment') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type_aboniment'), 'has-success': fields.type_aboniment && fields.type_aboniment.valid }">
    <label for="type_aboniment" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.type_aboniment') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.type_aboniment" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('type_aboniment'), 'form-control-success': fields.type_aboniment && fields.type_aboniment.valid}" id="type_aboniment" name="type_aboniment" placeholder="{{ trans('admin.student-payment.columns.type_aboniment') }}">
        <div v-if="errors.has('type_aboniment')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type_aboniment') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type_pay'), 'has-success': fields.type_pay && fields.type_pay.valid }">
    <label for="type_pay" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.student-payment.columns.type_pay') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.type_pay" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('type_pay'), 'form-control-success': fields.type_pay && fields.type_pay.valid}" id="type_pay" name="type_pay" placeholder="{{ trans('admin.student-payment.columns.type_pay') }}">
        <div v-if="errors.has('type_pay')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type_pay') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('enabled'), 'has-success': fields.enabled && fields.enabled.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="enabled" type="checkbox" v-model="form.enabled" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element">
        <label class="form-check-label" for="enabled">
            {{ trans('admin.student-payment.columns.enabled') }}
        </label>
        <input type="hidden" name="enabled" :value="form.enabled">
        <div v-if="errors.has('enabled')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('enabled') }}</div>
    </div>
</div>


