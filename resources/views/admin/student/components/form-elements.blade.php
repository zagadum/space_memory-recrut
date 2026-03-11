<div class="admin-form_part">
{{--    <div class="admin-form_part-title">{{ trans('admin.student.block.franchise') }}</div>--}}

    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="form-group row align-items-start">
                @if  (session('role')=='franchisee' || session('role')=='admin')
                <label class="col-md-4">{{ trans('admin.franchisee.title2') }} *</label>
                @endif
                <div class="col-md-8">

                    @if  (session('role')=='franchisee')
                        <b>{{@$Franchisee[0]->surname}} {{@$Franchisee[0]->first_name}}</b>
                    @endif
                    @if  (session('role')=='admin')
                    <multiselect v-model="form.franchisee"
                                 :options="{{ $Franchisee->toJson() }}" :multiple="false"
                                 :custom-label="nameFranchisee" track-by="id" open-direction="bottom"
                                 tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                 placeholder="{{ trans('admin.forms.select_options') }}"
                                 :show-labels="false"
                                 @input="onChangeFranchisee(form.franchisee.id)"
                                 :class="{'form-control-danger': errors.has('franchisee'), 'form-control-success': fields.franchisee && fields.franchisee.valid}"></multiselect>

                    <div v-if="errors.has('franchisee')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('franchisee') }}</div>
                        @endif
                </div>
            </div>
            <div class="form-group row align-items-start">
                <label class="col-md-4">{{ trans('admin.student.columns.teacher') }} *</label>
                <div class="col-md-8">
                    @if  (session('role')=='teacher')
                        @if (!empty($student_def['teacher']))
                                <b>{{@$student_def['teacher']['surname']}}  {{@$student_def['teacher']['first_name']}}</b>
                        @else
                            <b>{{@$student['teacher']['surname']}}  {{@$student['teacher']['first_name']}}</b>

                        @endif
                    @endif
                    @if  (session('role')=='admin' || session('role')=='franchisee')
                        <multiselect v-model="form.teacher"
                                     :disabled="form.isTeacherDisabled"
                                     :loading="isLoadingTeacher"
                                     :options="TeacherMe"
                                     :custom-label="nameTeacher"
                                     :multiple="false" track-by="id" open-direction="bottom"
                                     tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                     placeholder="{{ trans('admin.forms.select_options') }}"
                                     :show-labels="false"

                                     @input="onChangeTeacher(form.franchisee.id,form.teacher.id)"
                                     :class="{'form-control-danger': errors.has('teacher'), 'form-control-success': fields.teacher && fields.teacher}"></multiselect>
                        <div v-if="errors.has('teacher')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('teacher') }}</div>
                     @endif
                </div>
            </div>
            <div class="form-group row align-items-start">
                <label class="col-md-4">{{ trans('admin.student.columns.group_id') }} *</label>
                <div class="col-md-8">
                    <multiselect v-model="form.groupSet"
                                 :disabled="form.isGroupDisabled"

                                 :loading="isLoadingGroup"
                                 :options="GroupMe"
                                 :custom-label="nameGroup"

                                 :multiple="false" track-by="id" open-direction="bottom"
                                 tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                 placeholder="{{ trans('admin.forms.select_options') }}"
                                 :show-labels="false"
                                 @open="onOpenGroup"
                                 @input="onChangeGroupFx(form.groupSet.id)"
                                 :class="{'form-control-danger': errors.has('groupSet'), 'form-control-success': fields.groupSet && fields.groupSet}"></multiselect>
                    <div v-if="errors.has('groupSet')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('groupSet') }}</div>
                  <!-- :disabled="form.isGroupDisabled"
                               -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-form_part">
    <div class="admin-form_part-title">{{ trans('admin.student.block.general') }}</div>

    <div class="row">
        <div class="col-md-12 col-lg-6">

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('surname'), 'has-success': fields.surname && fields.surname.valid }">
                <label for="surname" class="col-form-label text-md-left" :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.surname') }}*</label>
                <div class="col-md-8">
                    <input type="text" v-model="form.surname" @input="validate($event)"
                           class="form-control"
                           :class="{'form-control-danger': errors.has('surname'), 'form-control-success': fields.surname && fields.surname.valid}"
                           id="surname" name="surname" placeholder="{{ trans('admin.student.columns.surname') }}">
                    <div v-if="errors.has('surname')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('surname') }}</div>
                </div>
            </div>

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('lastname'), 'has-success': fields.lastname && fields.lastname.valid }">
                <label for="lastname" class="col-form-label text-md-left" :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.lastname') }}*</label>
                <div class="col-md-8">
                    <input type="text" v-model="form.lastname" v-validate="'required'" @input="validate($event)"
                           class="form-control"
                           :class="{'form-control-danger': errors.has('lastname'), 'form-control-success': fields.lastname && fields.lastname.valid}"
                           id="lastname" name="lastname" placeholder="{{ trans('admin.student.columns.lastname') }}">
                    <div v-if="errors.has('lastname')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('lastname') }}</div>
                </div>
            </div>

            @if (\App\Helpers\SiteHelper::GetLang()!='pl' && \App\Helpers\SiteHelper::GetLang()!='en')
            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('patronymic'), 'has-success': fields.patronymic && fields.patronymic.valid }">
                <label for="patronymic" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.patronymic') }}</label>
                <div class="col-md-8">
                    <input type="text" v-model="form.patronymic" v-validate="''" @input="validate($event)"
                           class="form-control"
                           :class="{'form-control-danger': errors.has('patronymic'), 'form-control-success': fields.patronymic && fields.patronymic.valid}"
                           id="patronymic" name="patronymic"
                           placeholder="{{ trans('admin.student.columns.patronymic') }}">
                    <div v-if="errors.has('patronymic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('patronymic') }}</div>
                </div>
            </div>
            @endif
            <br>
            <br>

            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
                <label for="password" class="col-form-label text-md-left" :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.password') }}</label>
                <div class="col-md-8">
                    <div class=" dis-flex">
                        <div class="password-control_container">
                            <input  :type="showPassword ? 'text' : 'password'" v-model="form.password" v-validate="'min:7'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('password'), 'form-control-success': fields.password && fields.password.valid}" id="password" name="password" placeholder="{{ trans('admin.student.columns.password') }}" ref="password"  autocomplete="new-password">
                            <a href="#" class="password-control" @click.prevent="showPassword = !showPassword"></a>

                        </div>
                        @if (!empty($student->id))
                        <!-- <button :disabled="errors.has('password') || !form.password" type="button" v-if="form.id>0" class="btn-change"  @click="$modal.show('ChangePasswordModal',{ show_div:'',resource_link:'{{ url('admin/students') }}/{{@$student->id}}/change-password' })" >{{ trans('admin.btn.change') }}</button> -->
                        @endif
                    </div>
                    <div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}</div>

                    <div v-if="errors.has('password')"  ref="passwordError" class="form-control-feedback form-text" style="color: #FE3F61; font-weight: 500; font-size: 12px;">{{ trans('admin.franchisee.password_help') }}
                    </div>
                </div>
            </div>

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
                <label for="email" class="col-form-label text-md-left" :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.email') }}*</label>
                <div class="col-md-8">
                    <div class=" dis-flex">
                        <input type="text" v-model="form.email" v-validate="'required|email'" @input="validate($event)"
                               class="form-control"
                               :class="{'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid}"
                               id="email" name="email" placeholder="{{ trans('admin.student.columns.email') }}">
                        @if (!empty($student->id))
                       <!-- <button type="button" v-if="form.id>0" class="btn-change" @click="$modal.show('ChangeEmailModal1',{ show_div:'',resource_link:'{{ url('admin/students') }}/{{@$student->id}}/change-email' })">Сменить</button> -->
{{--                        <button type="button" v-if="form.id>0" class="btn-change" @click="$modal.show('ChangeEmailModal',{ foo: 'bar' })">Сменить</button>--}}
                        @endif
                    </div>
                    <div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
                </div>
            </div>

        </div>
        <div class="col-md-12 col-lg-6">
            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('dob'), 'has-success': fields.dob && fields.dob.valid }">
                <label for="dob" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.dob') }}
                    *</label>
                <div class="col-md-8">
                    <datetime v-model="form.dob"
                              :config="{ firstDayOfWeek:1,  dateFormat: 'Y-m-d H:i:S',   altInput: true,      weekNumbers:false,   altFormat: 'd.m.Y',    locale: '{{\App\Helpers\SiteHelper::GetLang()}}' , minDate: '<?php print date('d.m.Y',strtotime("-40 years"));?>', maxDate: '<?php print date('d.m.Y',strtotime("-5 years"));?>'}"
                              v-validate="'required'"
                              class="flatpickr"
                              :class="{'form-control-danger': errors.has('dob'), 'form-control-success': fields.dob && fields.dob.valid}"
                              id="dob" name="dob"
                              placeholder="{{ trans('admin.forms.select_a_date') }}"></datetime>
                    <div v-if="errors.has('dob')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dob') }}</div>
                </div>
            </div>

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('phone'), 'has-success': fields.phone && fields.phone.valid }">
                <label for="phone" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.phone') }}
                     </label>
                <div class="col-md-8">

                    <input type="hidden" v-model="form.phoneSave"  />

                    <div class="number_phone-mask">
                        <vue-phone-number-input  v-model="form.phone"  :default-country-code="form.phone_country"  :show-code-on-list="true" :fetch-country="false"    ref="phoneInputRef"
                                                :translations='{"countrySelectorLabel":"{{ trans('admin.phone.code') }}","countrySelectorError":"{{ trans('admin.phone.number') }}","phoneNumberLabel":"{{ trans('admin.phone.EnterNumber') }}","example":" {{ trans('admin.phone.example') }} :"}'
                                                    :maxlength="12"  @update="onChangePhone"
                                                placeholder="{{ trans('admin.franchisee.columns.phone') }}"
                                                :class="{'form-control-danger': errors.has('phone'), 'form-control-success': fields.phone && fields.phone.valid}"></vue-phone-number-input>
                    </div>
                    <div v-if="errors.has('phone')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('phone') }}</div>
                </div>
            </div>

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('language'), 'has-success': fields.language && fields.language.valid }">
                <label for="language" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.language') }}
                    * </label>
                <div class="col-md-8">
                    <multiselect v-model="form.language" :options="{{ SiteHelper::Locales(true, true)->toJson() }}"
                                 tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                 placeholder="{{ trans('admin.forms.select_options') }}"
                                 :show-labels="false" open-direction="bottom"></multiselect>

                    <div v-if="errors.has('language')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('language') }}
                    </div>
                </div>
            </div>
            <br>
            <br>


            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('subcribe_email'), 'has-success': fields.subcribe_email && fields.subcribe_email.valid }">
                <label for="subcribe_email" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.subcribe_email') }}</label>
                <div class="col-md-8">
                    <input type="text" v-model="form.subcribe_email" v-validate="'email'" @input="validate($event)"
                           class="form-control"
                           :class="{'form-control-danger': errors.has('subcribe_email'), 'form-control-success': fields.subcribe_email && fields.subcribe_email.valid}"
                           id="subcribe_email" name="subcribe_email"
                           placeholder="{{ trans('admin.student.columns.subcribe_email') }}">
                    <div v-if="errors.has('subcribe_email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subcribe_email') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-form_part">
    <div class="admin-form_part-title">{{ trans('admin.student.block.parent') }}</div>

    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('parent1_surname'), 'has-success': fields.balance && fields.parent1_surname.valid }">
                <label for="parent1_surname" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent1_surname') }}
                    *</label>
                <div class="col-md-8"><input type="text" v-model="form.parent1_surname" v-validate="'required'"
                                             @input="validate($event)" class="form-control"
                                             :class="{'form-control-danger': errors.has('parent1_surname'), 'form-control-success': fields.parent1_surname && fields.parent1_surname.valid}"
                                             id="parent1_surname" name="parent1_surname"
                                             placeholder="{{ trans('admin.student.columns.parent1_surname') }}">
                    <div v-if="errors.has('parent1_surname')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent1_surname') }}</div>
                </div>
            </div>

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('parent1_lastname'), 'has-success': fields.parent1_lastname && fields.parent1_lastname.valid }">
                <label for="parent1_lastname" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent1_lastname') }}
                    *</label>
                <div class="col-md-8"><input type="text" v-model="form.parent1_lastname" v-validate="'required'"
                                             @input="validate($event)" class="form-control"
                                             :class="{'form-control-danger': errors.has('parent1_lastname'), 'form-control-success': fields.parent1_lastname && fields.parent1_lastname.valid}"
                                             id="parent1_lastname" name="parent1_lastname"
                                             placeholder="{{ trans('admin.student.columns.parent1_lastname') }}">
                    <div v-if="errors.has('parent1_lastname')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent1_lastname') }}</div>
                </div>
            </div>
            @if (\App\Helpers\SiteHelper::GetLang()!='pl'  && \App\Helpers\SiteHelper::GetLang()!='en')
            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('parent1_patronymic'), 'has-success': fields.parent1_patronymic && fields.parent1_patronymic.valid }">
                <label for="parent1_patronymic" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent1_patronymic') }}</label>
                <div class="col-md-8"><input type="text" v-model="form.parent1_patronymic" @input="validate($event)"
                                             class="form-control"
                                             :class="{'form-control-danger': errors.has('parent1_patronymic'), 'form-control-success': fields.parent1_patronymic && fields.parent1_patronymic.valid}"
                                             id="parent1_patronymic" name="parent1_patronymic"
                                             placeholder="{{ trans('admin.student.columns.parent1_patronymic') }}">
                    <div v-if="errors.has('parent1_patronymic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent1_patronymic') }}</div>
                </div>
            </div>
            @endif

            <div class="form-group row align-items-center"
                 :class="{'has-danger': errors.has('parent1_phone'), 'has-success': fields.parent1_phone && fields.parent1_phone.valid }">
                <label for="parent1_phone" class="col-form-label text-md-left"
                       :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.phone') }}
                    * </label>
                <div class="col-md-8">

                    <input type="hidden" v-model="form.phone1Save"  />
                    <div class="number_phone-mask">
                        <vue-phone-number-input v-model="form.parent1_phone" :show-code-on-list="true"  name="parent1_phone"   :default-country-code="form.parent1_phone_country"
                                                :fetch-country="false"
                                :translations='{"countrySelectorLabel":"{{ trans('admin.phone.code') }}","countrySelectorError":"{{ trans('admin.phone.number') }}","phoneNumberLabel":"{{ trans('admin.phone.EnterNumber') }}","example":" {{ trans('admin.phone.example') }} :"}'
                                :maxlength="12" @update="onChangePhoneParent1"
                                                placeholder="{{ trans('admin.franchisee.columns.parent1_phone') }}"
                                                :class="{'form-control-danger': errors.has('parent1_phone'), 'form-control-success': fields.parent1_phone && fields.parent1_phone.valid}"></vue-phone-number-input>
                    </div>
                    <div v-if="errors.has('parent1_phone')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent1_phone') }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 offset-md-4 col-md-8">
                    <button class="add_field-btn" type="button" @click="handlerPerent2Button()"><span
                                v-if="isShowParent2">- {{ trans('admin.student.actions.hide_parent_btn') }}</span><span v-else>+ {{ trans('admin.student.actions.add_parent_btn') }}</span> {{ trans('admin.student.actions.parent_btn') }}
                    </button>
                </div>
            </div>

            <div v-if="isShowParent2"> <!-- Родитель 2-->
                <div class="admin-form_part-title">{{ trans('admin.student.block.parent') }}</div>
                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent2_surname'), 'has-success': fields.balance && fields.parent2_surname.valid }">
                    <label for="parent2_surname" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent2_surname') }}
                        *</label>
                    <div class="col-md-8"><input type="text" v-model="form.parent2_surname" v-validate="'required'"
                                                 @input="validate($event)" class="form-control"
                                                 :class="{'form-control-danger': errors.has('parent2_surname'), 'form-control-success': fields.parent2_surname && fields.parent2_surname.valid}"
                                                 id="parent2_surname" name="parent2_surname"
                                                 placeholder="{{ trans('admin.student.columns.parent2_surname') }}">
                        <div v-if="errors.has('parent2_surname')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent2_surname') }}</div>
                    </div>
                </div>

                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent2_first_name'), 'has-success': fields.parent2_first_name && fields.parent2_first_name.valid }">
                    <label for="parent2_first_name" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent2_first_name') }}
                        *</label>
                    <div class="col-md-8"><input type="text" v-model="form.parent2_first_name" v-validate="'required'"
                                                 @input="validate($event)" class="form-control"
                                                 :class="{'form-control-danger': errors.has('parent2_first_name'), 'form-control-success': fields.parent2_first_name && fields.parent2_first_name.valid}"
                                                 id="parent2_first_name" name="parent2_first_name"
                                                 placeholder="{{ trans('admin.student.columns.parent2_first_name') }}">
                        <div v-if="errors.has('parent2_first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent2_first_name') }}</div>
                    </div>
                </div>
                @if (\App\Helpers\SiteHelper::GetLang()!='pl'  && \App\Helpers\SiteHelper::GetLang()!='en')
                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent2_patronymic'), 'has-success': fields.parent2_patronymic && fields.parent2_patronymic.valid }">
                    <label for="parent2_patronymic" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent2_patronymic') }}</label>
                    <div class="col-md-8"><input type="text" v-model="form.parent2_patronymic" @input="validate($event)"
                                                 class="form-control"
                                                 :class="{'form-control-danger': errors.has('parent2_patronymic'), 'form-control-success': fields.parent2_patronymic && fields.parent2_patronymic.valid}"
                                                 id="parent1_patronymic" name="parent1_patronymic"
                                                 placeholder="{{ trans('admin.student.columns.parent2_patronymic') }}">
                        <div v-if="errors.has('parent2_patronymic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent2_patronymic') }}
                        </div>
                    </div>
                </div>
                @endif
                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent2_phone'), 'has-success': fields.parent2_phone && fields.parent2_phone.valid }">
                    <label for="parent2_phone" class="col-form-label text-md-left" :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.phone') }}* </label>
                    <div class="col-md-8">

                        <input type="hidden" v-model="form.phone2Save"  />
                        <div class="number_phone-mask">
                            <vue-phone-number-input v-model="form.parent2_phone" :show-code-on-list="true"  name="parent2_phone"   :default-country-code="form.parent2_phone_country"
                                                    :fetch-country="false"
                                    :translations='{"countrySelectorLabel":"{{ trans('admin.phone.code') }}","countrySelectorError":"{{ trans('admin.phone.number') }}","phoneNumberLabel":"{{ trans('admin.phone.EnterNumber') }}","example":" {{ trans('admin.phone.example') }} :"}' :maxlength="12"
                                     @update="onChangePhoneParent2"
                                                    placeholder="{{ trans('admin.franchisee.columns.parent2_phone') }}"
                                                    :class="{'form-control-danger': errors.has('parent2_phone'), 'form-control-success': fields.parent2_phone && fields.parent2_phone.valid}"></vue-phone-number-input>
                        </div>
                        <div v-if="errors.has('parent2_phone')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent2_phone') }}</div>
                    </div>
                </div>
              <!--  <div class="row">
                    <div class="col-sm-12 offset-md-4 col-md-8">
                        <button class="add_field-btn" type="button" @click="handlerPerent3Button()"><span
                                    v-if="isShowParent3">- Скрыть</span><span v-else>+ Добавить</span> родителей

                        </button>
                    </div>
                </div>-->
            </div>
            <!-- Родитель 3-->
        <!--
            <div v-if="isShowParent3">
                <div class="admin-form_part-title">{{ trans('admin.student.block.parent') }}</div>
                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent3_surname'), 'has-success': fields.parent3_surname && fields.parent3_surname.valid }">
                    <label for="parent3_surname" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent3_surname') }}
                *</label>
            <div class="col-md-8"><input type="text" v-model="form.parent3_surname" v-validate="'required'"
                                         @input="validate($event)" class="form-control"
                                         :class="{'form-control-danger': errors.has('parent3_surname'), 'form-control-success': fields.parent3_surname && fields.parent3_surname.valid}"
                                         id="parent3_surname" name="parent3_surname"
                                         placeholder="{{ trans('admin.student.columns.parent3_surname') }}">
                        <div v-if="errors.has('parent3_surname')" class="form-control-feedback form-text" v-cloak>@{{
                            errors.first('parent3_surname') }}
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent3_first_name'), 'has-success': fields.parent3_first_name && fields.parent3_first_name.valid }">
                    <label for="parent3_first_name" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent3_first_name') }}
                *</label>
            <div class="col-md-8"><input type="text" v-model="form.parent3_first_name" v-validate="'required'"
                                         @input="validate($event)" class="form-control"
                                         :class="{'form-control-danger': errors.has('parent3_first_name'), 'form-control-success': fields.parent3_first_name && fields.parent3_first_name.valid}"
                                         id="parent3_first_name" name="parent3_first_name"
                                         placeholder="{{ trans('admin.student.columns.parent3_first_name') }}">
                        <div v-if="errors.has('parent3_first_name')" class="form-control-feedback form-text" v-cloak>@{{
                            errors.first('parent3_first_name') }}
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent3_patronymic'), 'has-success': fields.parent3_patronymic && fields.parent3_patronymic.valid }">
                    <label for="parent1_patronymic" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent3_patronymic') }}</label>
                    <div class="col-md-8"><input type="text" v-model="form.parent3_patronymic" @input="validate($event)"
                                                 class="form-control"
                                                 :class="{'form-control-danger': errors.has('parent3_patronymic'), 'form-control-success': fields.parent3_patronymic && fields.parent3_patronymic.valid}"
                                                 id="parent3_patronymic" name="parent3_patronymic"
                                                 placeholder="{{ trans('admin.student.columns.parent3_patronymic') }}">
                        <div v-if="errors.has('parent3_patronymic')" class="form-control-feedback form-text" v-cloak>@{{
                            errors.first('parent3_patronymic') }}
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-center"
                     :class="{'has-danger': errors.has('parent3_phone'), 'has-success': fields.parent3_phone && fields.parent3_phone.valid }">
                    <label for="parent3_phone" class="col-form-label text-md-left"
                           :class="isFormLocalized ? 'col-md-4' : 'col-md-4'">{{ trans('admin.student.columns.parent3_phone') }}
                * </label>
            <div class="col-md-8">
                <input type="hidden" v-model="form.parent3_phone" ref="parent3_phone" name="parent3_phone"/>
                <div class="number_phone-mask">
                    <vue-phone-number-input v-model="form.parent3_phone" :show-code-on-list="true"
                                            :fetch-country="true"
                                            :maxlength="12" @country-changed="onChangePhone"
                                            placeholder="{{ trans('admin.franchisee.columns.phone') }}"
                                                    :class="{'form-control-danger': errors.has('parent3_phone'), 'form-control-success': fields.parent3_phone && fields.parent3_phone.valid}"></vue-phone-number-input>
                        </div>
                        <div v-if="errors.has('parent3_phone')" class="form-control-feedback form-text" v-cloak>@{{
                            errors.first('parent3_phone') }}
                        </div>
                    </div>
                </div>
            </div>
-->

        </div>
    </div>
</div>

<div class="admin-form_part">
    <div class="admin-form_part-title">{{ trans('admin.student.block.finance') }}</div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group row align-items-start">
                <label class="col-md-3 col-lg-2">{{ trans('admin.student.columns.start_day') }} </label>
                <div class="col-md-8">

                    <p v-html="form.start_day_group"/>
                </div>

            </div>
            <div class="form-group row align-items-start" v-if="form.showDiscount">
                <label class="col-md-3 col-lg-2">{{ trans('admin.student.columns.discount') }}</label>
                <div class="col-md-8">

                    <div class="student-discount_box">
                        <div>

                            <input class="form-check-input" id="is_twochildren" type="checkbox" v-model="form.is_twochildren"  data-vv-name="is_twochildren" name="is_twochildren"  v-on:click="twoChildrenCheck($event)"  >
                            <label class="form-check-label discount_sec-children" for="is_twochildren">{{ trans('admin.student.columns.is_twochildren') }}</label>


                            <div class="discount-multiselect" v-if="form.is_twochildren">
                                <multiselect v-model="form.twochildren" :options="TwoChildrenMe"
                                             :loading="isLoadingTwoChildren"
                                             track-by="id"
                                             :custom-label="nameChildren"
                                             tag-placeholder="{{ trans('admin.student.actions.select_first_child') }}"
                                             placeholder="{{ trans('admin.student.actions.select_first_child') }}"
                                             :show-labels="false" open-direction="bottom"></multiselect>
                            </div>
                            <div v-if="errors.has('two_children')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('two_children') }}
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="form-group row align-items-start">
                <label class="col-md-3 col-lg-2">{{ trans('admin.student.finance.price') }} </label>
                <div class="col-md-8 subscription_price">
                    <span v-html="form.fin_price_aboniment" style="margin-right: 5px;">-</span>
                    <span v-html="form.fin_currency">-</span>
                    <span>{{ trans('admin.student.finance.discount') }}</span>
                    <span v-html="form.fin_price_aboniment_discount" style="margin-right: 5px;"></span>
                    <span v-html="form.fin_currency">-</span>
                </div>
            </div>
            @if (empty($student->id))
                <div class="form-group row align-items-start">
                    <label class="col-md-3 col-lg-2">{{ trans('admin.student.finance.pay') }} <span>*</span></label>
                    <div class="col-md-8">
                        <div class="check-block">

                            <div class="check-block_item">
                                <input class="form-check-input" v-model="form.type_payment" value="online" @click="ShowFormPay('online')" id="student_payment2" type="radio" name="student_payment">
                                <label class="form-check-label" for="student_payment2">{{ trans('admin.student.finance.pay_online') }}</label>

                            </div>
                            <div class="check-block_item">

                                <input class="form-check-input" v-model="form.type_payment" value="offline" @click="ShowFormPay('offline')" id="student_payment3" type="radio" name="student_payment">
                                <label class="form-check-label" for="student_payment3">{{ trans('admin.student.finance.pay_offline') }}</label>

                            </div>
                        </div>
                        <div v-if="errors.has('fin_cabinet')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fin_cabinet') }}</div>
                    </div>
                </div>

                <!-- Payment Type Select List v-if="visiblePayForm"-->
                <div class="row" >
                    <div class="col-sm-12 offset-md-3 col-md-9 offset-lg-2 col-lg-8">
                        <div class="offline-payment">
                            <div class="form-group offline-payment_item">
                                <label>{{ trans('admin.student.finance.date_payment') }} <span>*</span></label>
                                <div class="input-group">
                                    <datetime v-model="form.PayDate" :config="{ firstDayOfWeek:1,
                                dateFormat: 'Y-m-d H:i:S',
                                altInput: true,
                                weekNumbers:false,
                                altFormat: 'd.m.Y',
                                locale: '{{\App\Helpers\SiteHelper::GetLang()}}'}"
                                              class="flatpickr"
                                              :class="{'form-control-danger': errors.has('payDate'), 'form-control-success': fields.PayDate && fields.payDate.valid}"
                                              id="payDate" name="PayDate"
                                              placeholder="{{ trans('admin.forms.select_a_date') }}"></datetime>
                                </div>
                            </div>
                            <div class="form-group offline-payment_item">
                                <label>{{ trans('admin.student.finance.period') }}*</label>
                                <div class="col-md-8">
                                    <multiselect v-model="form.PayDiscount" :options="{{ $Discount->toJson() }}"
                                                 track-by="id"
                                                  label="name"
                                                 tag-placeholder="{{ trans('admin.forms.select_options') }}"
                                                 placeholder="{{ trans('admin.forms.select_options') }}"
                                                 @input="onChangeDiscount(form.PayDiscount.discount)"
                                                 :show-labels="false" open-direction="bottom"></multiselect>
                                </div>

                            </div>

                            <div class="form-group d-flex">
                                <label>{{ trans('admin.student.finance.sum_subscription') }}:&nbsp;  <span v-html="form.sum_pay" style="margin-right: 5px;"></span><span v-html="form.fin_currency">-</span></label>


                            </div>

                            <div class="form-group">
                                    <textarea class="form-control" placeholder="{{ trans('admin.student.finance.add_comment') }}"  v-model="form.PayComment" name="PayComment"
                                              style="margin: 0"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
