@extends('admin.layout.theme.default')

@section('title', trans('admin.student.actions.index'))

@section('body')

     <student-listing class="admin-table" :data="{{ $data->toJson() }}" :url="'{{ url('admin/students') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">

                    <div class="admin_card-header">
                        @if (empty($group_filter['id']))
                        <p class="admin_card-header_title">{{ trans('admin.student.actions.index') }}</p>
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
                    </div>

                    <div class="card-body" v-cloak>
                        <div class="card-block">


                            <div class="card-header" >
                                @if (empty($group_filter['id']))
                                <p>{{ trans('menu.student.title') }} </p>
                                    <a class="btn btn-create-item btn-spinner" href="{{ url('admin/students/create') }}" role="button">{{ trans('admin.student.actions.create') }}<img src="{{asset('images/add_student.svg')}}"></a>
                                @else
                                    @if ( $group_filter['filterParam'] =='group')
                                        {{ trans('admin.student.title') }}    {{ trans('admin.student.title_group') }}   "{{$group_filter['name'] }}"
                                    @elseif ( $group_filter['filterParam'] =='teacher')
                                        {{ trans('admin.student.title') }}    {{ trans('admin.teacher.actions.index') }}  "{{$group_filter['name'] }}"
                                    @elseif ( $group_filter['filterParam'] =='franchisee')
                                        {{ trans('admin.student.title') }}   {{ trans('admin.franchisee.title2') }}  "{{$group_filter['name'] }}"
                                    @endif
                                    <a class="btn btn-create-item btn-spinner" href="/admin/students/create/{{$group_filter['id']}}"
                                       role="button">{{ trans('admin.student.actions.create') }}<img
                                            src="{{asset('images/add_student.svg')}}"></a>
                                @endif

                            </div>

                            <div style="overflow-x: auto">
                                <div style="min-width: 100%">
                                    <form @submit.prevent="">
                                        <div class="row justify-content-md-between"  style="width: 100%;">
                                            <div class="col col-lg-7 col-xl-5 form-group" >
                                                <div class="input-group">
                                                    <input class="form-control" placeholder="{{ trans('admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                                    <span class="input-group-append"><button type="button" class="btn  btn-create-item btn-spinner" @click="filter('search', search)" style="margin-left: 5pt"><i class="fa fa-search"></i><span style="margin-left: 5pt">{{ trans('admin.btn.search') }}</span></button></span>
                                                </div>
                                            </div>
                                            @if  (session('role')=='franchisee' || session('role')=='admin')
                                                <div class="col   form-group">
                                                    <a target="_blank" href="/admin/students/print-xls" type="button"   style="float: right; color: black" class="btn  btn-create-item btn-spinner" title="{{ trans('olympiad.practicians.action.print-list') }}"  ><i class="fa   fa-print"></i></a>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                    <table class="table table-hover table-listing">
                                        <thead>
                                        <tr>
                                            <th   is='sortable' :column="'surname'">{{ trans('admin.student.columns.fio') }}</th>
                                            <th is='sortable' :column="'start_day'">{{ trans('admin.student.columns.start_day') }}</th>
                                            <th   :column="'statistic_hw'">{{ trans('admin.student.columns.statistic_hw') }}</th>
                                            @if  (session('role')=='franchisee' || session('role')=='admin')
                                            <th is='sortable' :column="'teacher_id'">{{ trans('admin.student.columns.teacher_id') }}</th>
                                            @endif
                                            <th is='sortable' :column="'group_id'">{{ trans('admin.student.columns.group_id') }}</th>
{{--                                            <th is='sortable' :column="'discount'">{{ trans('admin.student.columns.discount') }}</th>--}}
                                            <th is='sortable' :column="'sum_aboniment'">{{ trans('admin.student.columns.sum_aboniment') }}</th>
                                            <th   :column="'rang_level'">{{ trans('admin.student.columns.rang_level') }}</th>
                                            <th is='sortable' :column="'balance'">{{ trans('admin.student.columns.balance') }}</th>
                                            <th is='sortable' :column="'diams'">{{ trans('admin.student.columns.diams') }}</th>
                                            <th></th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                            <td>@{{ item.surname }} @{{ item.lastname }} @{{ item.patronymic }}</td>
                                            <td>@{{ item.start_day | date }}</td>
                                            <td>@{{ item.statistic_hw }}</td>
                                            @if  (session('role')=='franchisee' || session('role')=='admin')
                                              <td>@{{ item.teacher_surname }} @{{ item.teacher_first_name }} </td>
                                            @endif
                                            <td> @{{ item.groups }}</td>
{{--                                            <td>@{{ item.discount }}%</td>--}}
                                            <td>@{{ item.sum_aboniment }}</td>
                                            <td>@{{ item.rang_max }} [@{{ item.rang_level }}]</td>
                                            <td @click="$modal.show('ChangeCoinsModal',{  resource_link:'/admin/students/'+item.id+'/change-coins'})"  :id="'balanceSpan_' + item.id" style="color:green">@{{ item.balance }} </td>
                                            <td @click="$modal.show('ChangeDiamsModal',{  resource_link:'/admin/students/'+item.id+'/change-diams'})"  :id="'diamsSpan_' + item.id"  style="color:blueviolet">@{{ item.diams }} </td>
                                            <td>
                                                <div class="row no-gutters table-controls">
                                                    @if  (session('role')=='admin')
                                                    <form class="col-auto" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn admin-filter-grey" title="{{ trans('admin.btn.delete') }}" ><img src="{{asset('images/fi_trash.svg')}}"></button>
                                                    </form>
                                                    @endif

                                                    <div class="col-auto" >
                                                         <form class="col-auto" @submit.prevent="BlokingItem(item.resource_url+ '/lock')">
                                                            <button v-if="item.blocked==0"   type="submit" class="btn" title="{{ trans('admin.btn.lock') }}" ><img src="{{asset('images/fi_lock.svg')}}"></button>
                                                            <button v-if="item.blocked==1"   type="submit" class="btn" title="{{ trans('admin.btn.unlock') }}" ><img src="{{asset('images/fi_lock_x.svg')}}"></button>
                                                        </form>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a class="btn admin-filter-grey" :href="item.resource_url + '/edit'" title="{{ trans('admin.btn.edit') }}" role="button"><img src="{{asset('images/fi_edit_dark.svg')}}"></a>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a class="btn"  :href="'/admin/calendar/students/'+item.id"   title="" role="button"><img src="{{asset('images/calendar-dark.svg')}}"></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                               </div>
                            </div>

                            <div class="row" v-if="pagination.state.total > 0">
                                <p class="active-franchisee col-sm">{{ trans('admin.student.pages.total') }}: <span>@{{ pagination.state.total }}</span></p>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('admin.index.no_items') }}</h3>
                                <p>{{ trans('admin.index.try_changing_items') }}</p>
                                @if (empty($group_filter['id']))
                                    <a class="btn btn-create-item btn-spinner" href="/admin/students/create" role="button">{{ trans('admin.student.actions.create') }}<img src="{{asset('images/add_student.svg')}}"></a>
                                @else
                                    <a class="btn btn-create-item btn-spinner" href="/admin/students/create/{{$group_filter['id']}}" role="button">{{ trans('admin.student.actions.create') }}<img src="{{asset('images/add_student.svg')}}"></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if (session('role')=='admin' || session('role')=='franchisee')
                    <div class="row">
                        <div class="col-md-auto">
                            <button type="button" class="block-item_btn"   onclick="window.location.href='/admin/restore/students'">{{ trans('admin.student.actions.show_block_student') }} <img src="{{asset('/images/Arro_right.svg')}}" /></button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </student-listing>



     @include('admin.student.components.modal-coins')
     @include('admin.student.components.modal-diams')
@endsection
