@extends('student.layout.default')
@section('body')
    <div class="student-dashboard">
        <div class="student-dashboard_info">
            <div class="student-dashboard_info-line1" style="color: red">
             Платіж відхилено. Спробуйте ще раз або зв'яжіться з підтримкою.
                <br>
                <br>

                <div>
                    <a href="/payments/imoje/test" style="color: blue">Спробувати ще</a>
                </div>
            </div>

        </div>
    </div>


@endsection
