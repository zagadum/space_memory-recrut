<student-payment-listing class="admin-table "
                         :data="{{ $data->toJson() }}"
                         :url="'{{ url('admin/student-coins') }}'"
                         inline-template>

    <div class="row">
        <div class="col">
            <div class="card">

                <div class="card-body" v-cloak>
                    <div class="card-block">
                        <div class="card-header">
                            <p>Статистика</p>
                            <a class="btn btn-create-item btn-spinner" href="{{ url('admin/students/print') }}"
                               role="button">{{ trans('admin.student.actions.print') }}<img
                                        src="{{asset('images/print.svg')}}"></a>
                        </div>


                        <div style="overflow-x: auto">
                            <div style="min-width: 100%">

                                <table class="table table-hover table-listing">
                                    <thead>
                                    <tr>

                                        <th is='sortable'
                                            :column="'student_id'">{{ trans('admin.student-payment.columns.student_id') }}</th>
                                        <th is='sortable'
                                            :column="'date_pay'">{{ trans('admin.student-payment.columns.date_pay') }}</th>
                                        <th is='sortable'
                                            :column="'date_finish'">{{ trans('admin.student-payment.columns.date_finish') }}</th>
                                        <th is='sortable'
                                            :column="'sum_aboniment'">{{ trans('admin.student-payment.columns.sum_aboniment') }}</th>
                                        <th is='sortable'
                                            :column="'type_aboniment'">{{ trans('admin.student-payment.columns.type_aboniment') }}</th>


                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="10">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a
                                                        href="#" class="text-primary"
                                                        @click="onBulkItemsClickedAll('/admin/student-payments')"
                                                        v-if="(clickedBulkItemsCount < pagination.state.total)"> <i
                                                            class="fa"
                                                            :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span
                                                        class="text-primary">|</span> <a
                                                        href="#" class="text-primary"
                                                        @click="onBulkItemsClickedAllUncheck()">{{ trans('admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3"
                                                        @click="bulkDelete('/admin/student-payments/bulk-destroy')">{{ trans('admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td>@{{ item.student_id }}</td>
                                        <td>@{{ item.date_pay | date }}</td>
                                        <td>@{{ item.date_finish | date }}</td>
                                        <td>@{{ item.sum_aboniment }}</td>
                                        <td>@{{ item.type_aboniment }}</td>


                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="row" v-if="pagination.state.total > 0">

                            <div class="col-sm-auto">
                                <pagination></pagination>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</student-payment-listing>
