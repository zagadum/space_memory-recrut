@extends('layouts.admin')
@section('content')
    <div class="page-wrapper">

        <div class="page-body">
            <div style="padding: 10pt">

                    <div class="card">
                        <div class="table-responsive">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Зворотній зв'язок</h3>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table card-table table-vcenter text-nowrap datatable" style=" word-wrap:break-word;    ">
                                            <thead>
                                            <tr>
                                                <!-- <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Select all invoices"></th>-->
                                                <th class="w-1">No.</th>
                                                <th>Автор</th>
                                                <th>Email</th>
                                                <th>Телефон</th>
                                                <th style="width: 50%">Коментар</th>


                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($feedBack as $fedItems )
                                                <tr id="tr_feed_back_{{$fedItems['id']}}">
                                                    <!--<td><input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Select invoice"></td>-->
                                                    <td><span class="text-muted">{{$fedItems['id']}}</span></td>
                                                    <td>
                                                        {{$fedItems['name']}}
                                                    </td>
                                                    <td>
                                                        {{$fedItems['email']}}
                                                    </td>
                                                    <td>
                                                        {{$fedItems['phone']}}
                                                    </td>
                                                    <td style="word-wrap: break-word;">
                                                        <span class="badge  me-1" style="background-color: #f7e807 !important"></span>
                                                        {{$fedItems['comments']}}
                                                    </td>


                                                    <td class="text-end">
                                                        <a href="javascript:void(0)" class="btn btn-danger" onclick="return DeleteFeedBack(  {{$fedItems['id']}})" title="Публіковати" >Спам</a>
                                                        <a href="#" class="btn btn-white" onclick="return ModerateFeedBack(  {{$fedItems['id']}},1)">Розглянуто</a>
                                                        <a href="javascript:void(0)" class="btn btn-remove btn-icon" title="Видалити" onclick="return DeleteFeedBack(  {{$fedItems['id']}})"><img  src="/img/admin/remove.svg" /> </a>


                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


            </div> </div> </div>

@endsection

@section('javascript')
    <script>
        function ModerateFeedBack(id,set_status) {
            if (confirm('Ви впевнені? ')) {
                $.ajax({
                    type: "POST",
                    url: "/admin/feedback/moderate/" + id,
                    data: {"set_status": set_status},
                    cache: false,
                    success: function (data) {
                        if (data.success) {
                            $('#tr_feed_back_'+id).hide();
                        }
                    }
                });
            }
            return false;
        }
        function DeleteFeedBack(id) {
            if (confirm('Ви впевнені в видаленні? ')) {
                $.ajax({
                    type: "POST",
                    url: "/admin/feedback/remove/" + id,
                    data: {"id": id},
                    cache: false,
                    success: function (data) {
                        if (data.success) {
                            $('#tr_feed_back_'+id).hide();

                        }
                    }
                });
            }
            return false;
        }

    </script>
@endsection

