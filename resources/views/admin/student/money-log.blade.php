@extends('admin.layout.theme.default')

@section('title', trans('admin.student.actions.index'))

@section('body')

     <student-listing class="admin-table" :data="{{ $data->toJson() }}" :url="'{{ url('admin/students') }}/{{$student->id}}/history-coins'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">

                    <div class="admin_card-header">
                        @if (empty($group_filter['id']))

                        <p class="admin_card-header_title">{{ trans('admin.student.title2') }}: {{$student->surname}} {{$student->first_name}}  {{$student->patronymic}}</p>
                         @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='group')
                            <p class="admin_card-header_title">{{ trans('admin.student.title') }}    {{ trans('admin.student.title_group') }}  "{{$group_filter['name'] }}"</p>
                        @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='franchisee')
                            <p class="admin_card-header_title">{{ trans('admin.student.title') }}    {{ trans('admin.franchisee.title2') }}  "{{$group_filter['name'] }}"</p>
                        @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='teacher')
                            <p class="admin_card-header_title">{{ trans('admin.student.title') }}    {{ trans('admin.teacher.actions.index') }}  "{{$group_filter['name'] }}"</p>
                        @endif

                        @include('admin.layout.theme.partials.header_body')
                    </div>

                    <div class="admin-breadcrumb">
                        @if (empty($group_filter['id']))
                        <a href="{{ url('admin/students') }}">{{ trans('menu.student.title') }}</a> <span>></span>
                        @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='group')
                            <a href="{{ url('admin/teacher-groups') }}"> {{ trans('menu.groups.title') }} </a> <span>></span>   {{ trans('admin.student.title_groupa') }}  "{{$group_filter['name'] }}"
                        @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='teacher')
                            <a href="{{ url('admin/teachers') }}"> {{ trans('menu.teacher.title') }} </a> <span>></span>   {{ trans('admin.teacher.title2') }}  "{{$group_filter['name'] }}"
                        @elseif (!empty($group_filter['id']) && $group_filter['filterParam'] =='franchisee')
                            <a href="{{ url('admin/franchisees') }}"> {{ trans('menu.franchisee.title') }} </a> <span>></span>   {{ trans('admin.franchisee.title') }}  "{{$group_filter['name'] }}"
                        @endif
                            <a href="/admin/students/{{$student->id}}/edit">{{$student->surname}} {{$student->first_name}}  {{$student->patronymic}}</a>
                    </div>

                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="card-header">
                                <p>{{ trans('student.bonus_history.accrual_history') }}</p>
                            </div>
                            <div style="overflow-x: auto">
                                <div style="min-width: 100%">
                                    <!-- <form @submit.prevent="">
                                        <div class="row justify-content-md-between">
                                            <div class="col col-lg-7 col-xl-5 form-group">
                                                <div class="input-group">
                                                    <input class="form-control" placeholder="{{ trans('admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                                    <span class="input-group-append">
                                                                            <button type="button" class="btn  btn-create-item btn-spinner" @click="filter('search', search)" style="margin-left: 5pt"><i class="fa fa-search"></i><span style="margin-left: 5pt">{{ trans('admin.btn.search') }}</span></button>
                                                                        </span>
                                                </div>
                                            </div>

                                        </div>
                                    </form>-->
                                   Coins: {{$student->balance}} Diams: {{$student->diams}}
                                    <table class="table table-hover table-listing">
                                        <thead>
                                        <tr>
                                            <th is='sortable' :column="'dates'">{{ trans('student.bonus_history.columns.date') }}</th>
                                            <th  :column="'coins_before'">{{ trans('student.bonus_history.columns.before') }}</th>
                                            <th   :column="'coins'">Coins</th>
                                            <th   :column="'coins_after'">{{ trans('student.bonus_history.columns.after') }}</th>
                                            <th  :column="'diams_before'">{{ trans('student.bonus_history.columns.before') }}</th>
                                            <th   :column="'diams'">Diams</th>
                                            <th   :column="'diams_after'">{{ trans('student.bonus_history.columns.after') }}</th>
                                            <th   :column="'comments'">{{ trans('student.bonus_history.columns.comments') }}</th>

                                        </tr>

                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                            <td>@{{ item.dates | date }}</td>
                                            <td><span v-if="item.coins!=0">@{{ item.coins_before }}</span></td>
                                            <td  :style="{ color: item.coins > 0 ? 'green' : 'red' }"><span v-if="item.coins!=0"><b>@{{ item.coins  }}</b></span></td>
                                            <td style="border-right: 1px solid #000000"><span v-if="item.coins!=0">@{{ item.coins_after }}</span></td>
                                            <td><span v-if="item.diams!=0">@{{ item.diams_before }}</span></td>
                                            <td style="color:blueviolet"><span v-if="item.diams!=0"><b>@{{ item.diams }}</b></span></td>
                                            <td><span v-if="item.diams!=0">@{{ item.diams_after }}</span></td>
                                            <td>@{{ item.comments }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                               </div>
                            </div>

                            <div class="row" v-if="pagination.state.total > 0">
                                <p class="active-franchisee col-sm"> Всього: <span>@{{ pagination.state.total }}</span></p>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('admin.index.no_items') }}</h3>
                                <p>{{ trans('admin.index.try_changing_items') }}</p>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </student-listing>

@endsection
