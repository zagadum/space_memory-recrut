@extends('admin.layout.theme.default')

@section('title', trans('admin.student.actions.create'))

@section('body')

    <div class="admin-form">

        <div class="card">

            <div class="admin_card-header">
                <p class="admin_card-header_title">{{ trans('admin.student.actions.create') }}</p>
                @include('admin.layout.theme.partials.header_body')
            </div>

            <div class="admin-breadcrumb">
                <a href="{{ url('admin/students') }}">{{ trans('menu.student.title') }}</a><span>></span>
                <a href="{{ url('admin/students/create') }}">{{ trans('admin.student.actions.create') }}</a>
            </div>

            <student-form :action="'{{ url('admin/students') }}'" v-cloak :data="{{ $student_def->toJson() }}" inline-template>

                <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" ref="studentform" id="studentform" novalidate>
                    <div class="card-body">
                        @include('admin.student.components.form-elements')
                    </div>

                    <div class="card-footer">
                        <div class="card-footer_btn-container">
                            <button type="button" class="btn btn-cancel-form" onclick="document.location='{{ url('admin/students') }}'">
                                {{ trans('admin.btn.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-save-form" :disabled="submiting">
                                {{ trans('admin.student.actions.create') }}
                            </button>
                        </div>
                    </div>
                </form>
            </student-form>
        </div>
    </div>
@endsection
