@extends('admin.layout.theme.default_admin')

@section('title', trans('admin.region.actions.edit', ['name' => $region->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <region-form
                :action="'{{ $region->resource_url }}'"
                :data="{{ $region->toJson() }}"
                v-cloak
                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.region.actions.edit', ['name' => $region->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.region.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('admin.btn.save') }}
                        </button>
                    </div>

                </form>

        </region-form>

        </div>

</div>

@endsection
