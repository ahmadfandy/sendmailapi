<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BlastMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlastController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $email_addr =  'ahmad.ariffandy@gmail.com';

        var_dump($email_addr);

        $sendToEmail = strtolower($email_addr);
        if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
        {
            Mail::to($sendToEmail)
                ->send(new BlastMail($dataArray));
            $callback['Error'] = false;
            $callback['Pesan'] = 'sendToEmail';
            echo json_encode($callback);
        }
    }
}