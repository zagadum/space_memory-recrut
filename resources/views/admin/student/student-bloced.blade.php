
    <student-listing-block  class="admin-table" :data="{{ $data_block->toJson() }}" :url="'{{ url('admin/students/block-list') }}'" inline-template>

        <div class="row" style="display:none" id="blocklist_div">
            <div class="col">
                <div class="card">

                    <div class="card-body" v-cloak>
                        <div class="card-block">

                            <div class="card-header">
                                <p>{{ trans('admin.student.title_block') }}</p>
                                <a class="btn btn-create-item btn-spinner" href="{{ url('admin/students/print') }}" role="button">{{ trans('admin.student.actions.print') }}<img src="{{asset('images/print.svg')}}"></a>
                            </div>
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append"><button type="button" class="btn  btn-create-item btn-spinner" @click="filter('search', search)" style="margin-left: 5pt"><i class="fa fa-search"></i><span style="margin-left: 5pt">{{ trans('admin.btn.search') }}</span></button></span>
                                        </div>
                                    </div>

                                </div>
                            </form>
                            <div style="overflow-x: auto">
                                <div style="min-width: 100%">
                                    <table class="table table-hover table-listing">
                                        <thead>
                                        <tr>
                                            <th is='sortable' :column="'surname'">{{ trans('admin.student.columns.fio') }}</th>
                                            <th is='sortable' :column="'start_day'">{{ trans('admin.student.columns.start_day') }}</th>
                                            <th is='sortable' :column="'blocking_date'">{{ trans('admin.student.columns.blocking_date') }}</th>
                                            <th is='sortable' :column="'group_id'">{{ trans('admin.student.columns.group_id') }}</th>
{{--                                            <th is='sortable' :column="'discount'">{{ trans('admin.student.columns.discount') }}</th>--}}
                                            <th is='sortable' :column="'sum_aboniment'">{{ trans('admin.student.columns.sum_aboniment') }}</th>
                                            <th is='sortable' :column="'balance'">{{ trans('admin.student.columns.balance') }}</th>
                                            <th is='sortable' :column="'blocking_reason'">{{ trans('admin.student.columns.blocking_reason') }}</th>
                                            <th></th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                            <td>@{{ item.surname }} @{{ item.lastname }} @{{ item.patronymic }} </td>
                                            <td>@{{ item.start_day | date }}</td>
                                            <td>@{{ item.blocking_date | date }}</td>
                                            <td>@{{ item.groups }}</td>
{{--                                            <td>@{{ item.discount }}%</td>--}}
                                            <td>@{{ item.sum_aboniment }}</td>
                                            <td>@{{ item.balance }}</td>
                                            <td>@{{ item.blocking_reason }}</td>
                                            <td>
                                                 <div class="row no-gutters table-controls">
                                                     <form class="col-auto" @submit.prevent="UnBlokingItem(item.resource_url+'/unlock')">
                                                         <button   type="submit" class="btn" title="{{ trans('admin.btn.unlock') }}" ><img src="{{asset('images/fi_lock_x.svg')}}"></button>
                                                     </form>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row" v-if="pagination.state.total > 0">
                                <p class="active-franchisee col-sm">{{ trans('admin.student.pages.total_block') }}: <span>@{{ pagination.state.total }}</span></p>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </student-listing-block>
