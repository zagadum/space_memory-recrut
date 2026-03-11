@extends('student.layout.default')
@section('body')
    <div class="student-dashboard">
        <div class="student-dashboard_info">
            <div class="student-dashboard_info-line1" style="color: #FFF9F0">
                <div id="pay_id" style="color: #FFF9F0">TEST</div>

                </div>
            </div>


        </div>


@endsection
@section('bottom-scripts')
    <script
        src="https://sandbox.paywall.imoje.pl/js/widget.min.js"
        id="imoje-widget__script"
        data-service-id="{{$fields['serviceId']}}"
        data-merchant-id="{{$fields['merchantId']}}"

        data-amount="{{$fields['amount']}}"
        data-currency="{{$fields['currency']}}"
        data-order-id="{{$fields['orderId']}}"

        data-customer-first-name="{{$fields['customerFirstName']}}"
        data-customer-last-name="{{$fields['customerLastName']}}"
        data-customer-email="{{$fields['customerEmail']}}"
{{--        data-customer-phone="{{$fields['customerPhone']}}"--}}
{{--        data-element-id="pay_id"--}}
        data-locale="{{$fields['locale']}}"
        data-signature="{{$signature}}">
    </script>

@endsection
