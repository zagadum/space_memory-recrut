<student-calendar-listing class="admin-table " :data="{{ $data->toJson() }}" :url="'{{ url('admin/student-payments') }}'" inline-template>

    <div class="row">
        <div class="col">
            <div class="card">

                <div class="card-body" v-cloak>
                    <div class="card-block">
                        <div class="card-header">
                            <p>{{ trans('admin.student-payment.title') }}</p>

                            <div class="card-header_btns">
                                <button type="button" class="btn btn-create-item btn-spinner"     @click="$modal.show('paymentModalStudent',{'resource_link': '/admin/students/{{$student->id}}/payment' })"  >{{ trans('admin.btn.pay_offline') }}
                                </button>

                                <a target="_blank" class="btn btn-create-item btn-spinner" href="/admin/students/{{$student->id}}/payment/print" role="button">{{ trans('admin.student.actions.print') }}<img src="{{asset('images/print.svg')}}"></a>
                            </div>
                        </div>


                        <div style="overflow-x: auto">
                            <div style="min-width: 100%">
                                <table class="table table-hover table-listing">
                                    <thead>
                                    <tr>
                                        <th is='sortable' :column="'student_id'">{{ trans('admin.student-payment.columns.student_id') }}</th>
                                        <th is='sortable' :column="'date_pay'">{{ trans('admin.student-payment.columns.date_pay') }}</th>
                                        <th is='sortable' :column="'date_finish'">{{ trans('admin.student-payment.columns.date_finish') }}</th>
                                        <th is='sortable' :column="'sum_aboniment'">{{ trans('admin.student-payment.columns.sum_aboniment') }}</th>
                                        <th is='sortable' :column="'type_aboniment'">{{ trans('admin.student-payment.columns.type_aboniment') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td>@{{ item.surname }} @{{ item.lastname }}</td>
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
</student-calendar-listing>
