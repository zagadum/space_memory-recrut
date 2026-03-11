@extends('admin.layout.default')

@section('title', trans('admin.student-payment.actions.edit', ['name' => $studentPayment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <student-payment-form
                :action="'{{ $studentPayment->resource_url }}'"
                :data="{{ $studentPayment->toJson() }}"
                v-cloak
                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.student-payment.actions.edit', ['name' => $studentPayment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.student-payment.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('admin.btn.save') }}
                        </button>
                    </div>

                </form>

        </student-payment-form>

        </div>

</div>

@endsection
