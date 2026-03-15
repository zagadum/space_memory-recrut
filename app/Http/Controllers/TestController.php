<?php

namespace App\Http\Controllers;
use App\Mail\RestoreMail;
use App\Models\TrainingHistoryTask;
use App\Models\TrainingMemoryTask;
use App\Services\Game\HomeWorkService;
use App\Services\Game\PlayerService;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;

class TestController extends Controller {



    function SentMail() {

      //  $bonusAll['bonus_week']= PlayerService::GetBonusWeek(812,0);
        //$bonusTask=HomeWorkService::GetTotalDoneWeek(812,1);
     //   dd($bonusTask);
      //  die('STPOP');
        $resetInsert['email']='zagadum@ukr.net';
        $resetInsert['realname']='zagadum@ukr.net';
        $resetInsert['token']='test';
            $objMail = new \stdClass();
            $objMail->username=$resetInsert['realname'];
            $objMail->email=$resetInsert['email'];
            $objMail->restore_url='https://'.$_SERVER['SERVER_NAME'].'/reset-password/'.$resetInsert['token'];
            $objMail->token=$resetInsert['token'];

            Mail::to($resetInsert['email'])->send(new RestoreMail($objMail));



    }


    function pdf(){
        //$this->fetchAndStoreHistoryData();
        die('OK');

        $pdf = new Mpdf();

        // Add content to the PDF
        $pdf->WriteHTML('<h1>Hello, World!</h1>');
        $pdf->WriteHTML('<table>');
        $pdf->WriteHTML('<tr><td>R1</td><td>0</td></tr>');
        $pdf->WriteHTML('</table>');
        return $pdf->Output();
        // Save the PDF to a file
        //$pdf->Output('path/to/save/pdf/document.pdf', 'F');

        // Alternatively, you can output the PDF directly to the browser
        // $pdf->Output();

        // Return a response
        //return response()->download('path/to/save/pdf/document.pdf');
        //}
    }



}
