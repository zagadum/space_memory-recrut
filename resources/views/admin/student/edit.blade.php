@extends('admin.layout.theme.default')

@section('title', trans('admin.student.actions.edit', ['name' => $student->email]))

@section('body')

<div class="admin-form">
        <div class="card">

            <div class="admin_card-header">
                <p class="admin_card-header_title">{{ trans('admin.student.actions.edit', ['name' => $student->first_name]) }}</p>

                @include('admin.layout.theme.partials.header_body')
            </div>


            <div class="admin-breadcrumb">
                <a href="{{ url('admin/students') }}">{{ trans('admin.student.title') }}</a> <span>></span>
                {{ trans('admin.student.title2') }}  {{$student->surname}} {{$student->first_name}}  {{$student->patronymic}}
            </div>

            <div class="admin_header-button">
                <div class="admin_header-button_left">
                    <button type="button" class="calendar-btn"  onclick="document.location='{{ url('admin/calendar/students').'/'.$student->id }}'">{{ trans('admin.calendars.students.student')}} <img src="{{asset('images/calendar-bold.svg')}}"></button>
                      <button type="button" class="dollar-btn" @click="$modal.show('ChangeCoinsModal',{  resource_link:'{{ url('admin/students') }}/{{$student->id}}/change-coins'})">{{ trans('admin.btn.coins') }} <span id="balanceSpan_{{$student->id}}">{{$student->balance}}</span></button>
                      <button type="button" class="dollar-btn" @click="$modal.show('ChangeDiamsModal',{ resource_link:'{{ url('admin/students') }}/{{$student->id}}/change-diams'})">{{ trans('admin.btn.diams') }} <span id="diamsSpan_{{$student->id}}">{{$student->diams}}</span></button>
                      <button type="button" class="dollar-btn"  onclick="document.location='{{ url('admin/students').'/'.$student->id.'/history-coins' }}'">{{ trans('admin.student.actions.history_btn') }} </button>
                  </div>
                  <div class="admin_header-button_right">
                      @if ($student->deleted==0)
                          @if  (session('role')=='admin')
                            <button type="button"  class="delete-btn" @click="$modal.show('deleteModalStudent',{ show_div:'',resource_link:'{{ url('admin/students') }}/{{$student->id}}'})">{{ trans('admin.btn.delete') }}<img src="{{asset('images/trash-bold.svg')}}"></button>
                          @endif
                        @if ($student->blocked==0)
                        <button type="button" class="block-btn"  @click="$modal.show('blokingModalStudent',{ show_div:'',resource_link:'{{ url('admin/students') }}/{{$student->id}}/lock'})">{{ trans('admin.btn.block') }}<img src="{{asset('images/lock-bold.svg')}}"></button>
                        @else
                            <button  type="submit"  class="block-btn" @click="$modal.show('unblockingModalStudent',{ show_div:'',resource_link:'{{ url('admin/students') }}/{{$student->id}}/unlock'})" >{{ trans('admin.btn.unblock') }}<img src="{{asset('images/fi_lock_x.svg')}}"></button>
                        @endif
                    @endif
                </div>
            </div>


            <student-form :action="'{{ $student->resource_url }}'" :data="{{ $student->toJson() }}" v-cloak inline-template>
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action"    ref="studentform" id="studentform" novalidate>
                    <div class="card-body">
                        @include('admin.student.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <div class="card-footer_btn-container">
                            <button type="button" class="btn btn-cancel-form" onclick="document.location='{{ url('admin/students') }}'">
                                {{ trans('admin.btn.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-save-form" :disabled="submiting">
                                {{ trans('admin.btn.save') }}
                            </button>
                        </div>
                    </div>
                </form>
        </student-form>
        </div>
</div>
<?php
    //@include('admin.student.components.modal-email')
//@include('admin.student.components.modal-email1')
//@include('admin.student.components.modal-password')
//@include('admin.student.components.statistic-student')
?>
@include('admin.student.components.modal-delete')
@include('admin.student.components.modal-change-group')

@include('admin.student.components.modal-offlinepayment')
@include('admin.student.components.modal-bloking')
@include('admin.student.components.modal-coins')
@include('admin.student.components.modal-diams')
@include('admin.student.components.modal-unblocking')


@include('admin.student.components.calendar-payment-student')
@endsection
