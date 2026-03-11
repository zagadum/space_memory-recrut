@extends('admin.layout.theme.default')

@section('title', trans('admin.admin-user.actions.edit_profile'))

@section('body')

    <div class="container-xl">

        <div class="card">

            <profile-edit-profile-form
                :action="'{{ url('admin/profile') }}'"
                :data="{{ $adminUser->toJson() }}"

                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action">

                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.admin-user.actions.edit_profile') }}
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="avatar-upload">

{{--                                      @if ($role=='admin')--}}
{{--                                        @include('admin.includes.avatar-uploader', ['mediaCollection' => app(\app\Models\User::class)->getMediaCollection('avatar'),  'media' => $adminUser->getThumbs200ForCollection('avatar') ])--}}

{{--                                      @endif--}}

                                          @if ($role=='teacher')
                                         @include('admin.includes.avatar-uploader', ['mediaCollection' => app(\app\Models\Teacher::class)->getMediaCollection('avatar'),  'media' => $adminUser->getThumbs200ForCollection('avatar') ])

                                          @endif
                                          @if ($role=='franchisee')
                                              @include('admin.includes.avatar-uploader', ['mediaCollection' => app(\app\Models\Franchisee::class)->getMediaCollection('avatar'),  'media' => $adminUser->getThumbs200ForCollection('avatar') ])

                                          @endif
                                  </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group row align-items-center" :class="{'has-danger': errors.has('surname'), 'has-success': fields.first_name && fields.surname.valid }">
                                    <label for="first_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.surname') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
                                        <input type="text" v-model="form.surname" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('surname'), 'form-control-success': fields.surname && fields.surname.valid}" id="first_name" name="surname" placeholder="{{ trans('admin.admin-user.columns.surname') }}">
                                        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('surname') }}</div>
                                    </div>
                                </div>


                                <div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_name'), 'has-success': fields.first_name && fields.first_name.valid }">
                                    <label for="first_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.first_name') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
                                        <input type="text" v-model="form.first_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_name'), 'form-control-success': fields.first_name && fields.first_name.valid}" id="first_name" name="first_name" placeholder="{{ trans('admin.admin-user.columns.first_name') }}">
                                        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_name') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center" :class="{'has-danger': errors.has('last_name'), 'has-success': fields.last_name && fields.last_name.valid }">
                                    <label for="last_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.patronymic') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
                                        <input type="text" v-model="form.last_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('patronymic'), 'form-control-success': fields.last_name && fields.patronymic.valid}" id="last_name" name="patronymic" placeholder="{{ trans('admin.admin-user.columns.patronymic') }}">
                                        <div v-if="errors.has('patronymic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('patronymic') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center" :class="{'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
                                    <label for="email" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.email') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
                                        <input type="text" v-model="form.email" v-validate="'required|email'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid}" id="email" name="email" placeholder="{{ trans('admin.admin-user.columns.email') }}">
                                        <div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center" :class="{'has-danger': errors.has('language'), 'has-success': fields.language && fields.language.valid }">
                                    <label for="language" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.language') }}</label>
                                    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
                                        <multiselect v-model="form.language" placeholder="{{ trans('admin.forms.select_an_option') }}" :options="{{ SiteHelper::Locales()->toJson() }}" open-direction="bottom"></multiselect>
                                        <div v-if="errors.has('language')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('language') }}</div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('admin.btn.save') }}
                        </button>
                    </div>

                </form>

            </profile-edit-profile-form>

        </div>

    </div>

@endsection
