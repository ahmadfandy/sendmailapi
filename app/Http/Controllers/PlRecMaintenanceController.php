<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailSendApproval;
use App\Mail\EmailPl;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlRecMaintenanceController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'doc_no'        => $request->doc_no,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'user_name'     => $request->user_name,
            'project_no'    => $request->project_no,
            'link'          => 'plrecmaintenance',
            'body'          => 'Please Approve '.$request->descs,
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new EmailPl($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }
    public function changestatus($entity_cd='', $project_no='', $doc_no='', $status='',$level_no='',$user_id='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'C',
            'module'        => 'PL',
        );

        $where3 = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'C',
            'module'        => 'PL',
        );
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        $query3 = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where3)
        ->get();
        if(count($query)>0){
            $msg = 'You Have Already Made a Request to PL Rec Maintenance No. '.$doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
        } else {
            if($status == 'A') {
                $sqlsendemail = "mgr.xrl_send_mail_approval_pl_rec_maintenace '" . $entity_cd . "', '" . $project_no . "', '" . $doc_no . "', '" . $status . "', '" . $level_no . "', '" . $user_id . "'";
                $snd = DB::connection('SSI')->insert($sqlsendemail);
                if ($snd == '1') {
                    $msg = "You Have Successfully Approved the PL Rec Maintenance No. ".$doc_no;
                    $notif = 'Approved !';
                    $st = 'OK';
                    $image = "approved.png";
                } else {
                    $msg = "You Failed to Approve the PL Rec Maintenance No ".$doc_no;
                    $notif = 'Fail to Approve !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else if($status == 'R'){
                $sqlsendemail = "mgr.xrl_send_mail_approval_pl_rec_maintenace '" . $entity_cd . "', '" . $project_no . "', '" . $doc_no . "', '" . $status . "', '" . $level_no . "', '" . $user_id . "'";
                $snd = DB::connection('SSI')->insert($sqlsendemail);
                if ($snd == '1') {
                    $msg = "You Have Successfully Made a Revise Request on PL Rec Maintenance No. ".$doc_no;
                    $notif = 'Revised !';
                    $st = 'OK';
                    $image = "revise.png";
                } else {
                    $msg = "You Failed to Make a Revise Request on PL Rec Maintenance No. ".$doc_no;
                    $notif = 'Fail to Revised !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else {
                $sqlsendemail = "mgr.xrl_send_mail_approval_pl_rec_maintenace '" . $entity_cd . "', '" . $project_no . "', '" . $doc_no . "', '" . $status . "', '" . $level_no . "', '" . $user_id . "'";
                $snd = DB::connection('SSI')->insert($sqlsendemail);
                if ($snd == '1') {
                    $msg = "You Have Successfully Cancelled the PL Rec Maintenance No. ".$doc_no;
                    $notif = 'Cancelled !';
                    $st = 'OK';
                    $image = "reject.png";
                } else {
                    $msg = "You Failed to Cancel the PL Rec Maintenance No. ".$doc_no;
                    $notif = 'Fail to Cancelled !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            }
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "image" => $image,
                "notif" => $notif
            );
        }
        return view("emails.after", $msg1);
    }
}