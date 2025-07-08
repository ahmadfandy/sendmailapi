<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailSend;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmailSendController extends Controller
{
    public function sendingMail()
    {
        $data = [];
        $criteria = array(
            'status' => 'P', 
        );
        try { 
            $data = DB::connection('SSI')
            ->table('mgr.ar_debtor')
            ->where($criteria)
            ->get();
            if(count($data)>0){
                foreach($data as $key => $user)
                {
                    $dataArray = [
                        'full_name'     => $user->name,
                        'debtor_acct'     => $user->debtor_acct,
                        'token'         => md5( rand(0,1000) ),
                        'email_mgr'     => 'ahmad.ariffandy@gmail.com'
                    ];
                    $insert = array(
                        'debtor_acct'   => $user->debtor_acct,
                        'hit'           => 0,
                        'audit_date'    => date('Y-m-d H:i:s'),
                    );
                    $query = DB::connection('mysql')
                    ->table('debtor_hit')
                    ->insert($insert);
                    if ($query != "1") {
                        $msg = $query;
                        $st = 'Fail';
                    } else {
                        $sendToEmail = strtolower($dataArray['email_mgr']);
                        if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
                        {
                            Mail::to($sendToEmail)->send(new EmailSend($dataArray));
                        }

                        return view('emails.send');
                    }
                }
            }else{
                return view('emails.fail');
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = "Get failed: " . $ex->getMessage();
            $st  = 'Fail';
        }
        echo json_encode($msg);
    }

    public function changestatus($status='', $debtor_acct='')
    {
        $where2 = array(
            'debtor_acct'   => $debtor_acct,
            'hit'           => '1'
        );
        $query = DB::connection('mysql')
            ->table('debtor_hit')
            ->where($where2)
            ->get();
            if(count($query)>0){
                $msg = 'Debtor sudah pernah di Approve atau Reject sebelumnya';
                $st  = 'OK';
                $image = "restricted.png";
                $msg1 = array(
                    "Pesan" => $msg,
                    "St" => $st,
                    "image" => $image
                );
            } else {
                $where = array(
                    'debtor_acct'   => $debtor_acct,
                );
                if($status == 'A') {
                    $status = array(
                        'status' => 'A',
                    );
                    $query = DB::connection('SSI')
                    ->table('mgr.ar_debtor')
                    ->where($where)
                    ->update($status);
        
                    if ($query != "1") {
                        $msg = $query;
                        $st = 'Fail';
                    } else {
                        $hit = array(
                            'hit'   => '1',
                            'audit_date'    => date('Y-m-d H:i:s'),
                        );
                        $query2 = DB::connection('mysql')
                        ->table('debtor_hit')
                        ->where($where)
                        ->update($hit);
                        if ($query != "1") {
                            $msg = $query;
                            $st = 'Fail';
                        } else {
                            $msg = "Anda berhasil melakukan Approval pada permintaan ini";
                            $st = 'OK';
                            $image = "approved.png";
                        }
                    }
                } else {
                    $status = array(
                        'status' => 'R',
                    );
                    $query = DB::connection('SSI')
                    ->table('mgr.ar_debtor')
                    ->where($where)
                    ->update($status);
        
                    if ($query != "1") {
                        $msg = $query;
                        $st = 'Fail';
                    } else {
                        $hit = array(
                            'hit'   => '1',
                            'audit_date'    => date('Y-m-d H:i:s'),
                        );
                        $query2 = DB::connection('mysql')
                        ->table('debtor_hit')
                        ->where($where)
                        ->update($hit);
                        if ($query != "1") {
                            $msg = $query;
                            $st = 'Fail';
                        } else {
                            $msg = "Anda berhasil melakukan Reject pada permintaan ini";
                            $st = 'OK';
                            $image = "reject.png";
                        }
                    }
                }
                $msg1 = array(
                    "Pesan" => $msg,
                    "St" => $st,
                    "image" => $image,
                    "tanggal"   => date('Y-m-d H:i:s')
                );
                return view("emails.after", $msg1);

            }
            return view("emails.after", $msg1);
    }

    public function getdate()
    {
        $where2 = array(
            'doc_no'        => 'PF23060002',
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => '023',
            'level_no'      => '1',
        );
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();
        if(count($query)>0){
            var_dump($query);
        } else {
            var_dump($query);
        }
        
        
    }
}
