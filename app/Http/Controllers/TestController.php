<?php

namespace App\Http\Controllers;

use App\Mail\RestoreMail;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Throwable;

class TestController extends Controller
{
    public function SentMail(Request $request): JsonResponse
    {
        // Backward-compatible alias for existing /test/mail route
        return $this->debugMail($request);
    }

    public function debugMail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email:rfc,dns'],
            'username' => ['nullable', 'string', 'max:120'],
            'token' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'in:verification,restore'],
        ]);

        $email = $validated['email'] ?? 'zagadum@ukr.net';
        $username = $validated['username'] ?? $email;
        $token = $validated['token'] ?? 'debug-token';
        $type = $validated['type'] ?? 'verification';

        $objMail = new \stdClass();
        $objMail->username = $username;
        $objMail->email = $email;
        $objMail->restore_url = $request->getSchemeAndHttpHost() . '/reset-password/' . $token;
        $objMail->token = $token;

        try {
            if ($type === 'restore') {
                Mail::to($email)->send(new RestoreMail($objMail));
            } else {
                Mail::to($email)->send(new VerificationCodeMail($token));
            }

            Log::info('Debug mail sent', [
                'type' => $type,
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Debug mail sent',
                'type' => $type,
                'email' => $email,
            ]);
        } catch (Throwable $e) {
            Log::error('Debug mail failed', [
                'type' => $type,
                'email' => $email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Debug mail failed',
                'error' => $e->getMessage(),
            ], 500);
        }
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
