<?php

namespace App\Http\Controllers;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends Controller{

    public function sendFeedback(Request $request)
    {
        $data = $request->all();
        if ($request->method() == 'POST') {
            $validator = Validator::make($data, [
                'name' => 'required',
                'email' => 'required|email:rfc,dns',
                'phone' => 'required',
                'comments' => 'required',

            ]);


            if ($validator->fails()) {
                $errors = $validator->errors();
                return redirect('/feedback')
                    ->withErrors($validator)
                    ->withInput();
            }
            Feedback::create($validator->validated());

            return redirect('feedback/thx');

        }
        return view('feedback.feedback');
    }
    public function ThxMassage(Request $request){
        return view('/feedback.ThxMassage');
    }

}
