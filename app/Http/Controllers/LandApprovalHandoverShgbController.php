<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandApprovalHandoverShgbMail;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;

class LandApprovalHandoverShgbController extends Controller
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
            'user_name'     => $request->user_name,
            'sender_name'   => $request->sender_name,
            'shgb_no'       => $request->shgb_no,
            'nop_no'        => $request->nop_no,
            'shgb_name'     => $request->shgb_name,
            'shgb_area'     => $request->shgb_area,
            'handover_to'   => $request->handover_to,
            'descs'         => $request->descs,
            'link'          => 'landapprovalhandovershgb',
        );

        var_dump($dataArray);

        // try {
        //     $sendToEmail = strtolower($request->email_addr);
        //     if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
        //     {
        //         Mail::to($sendToEmail)->send(new LandApprovalHandoverShgbMail($dataArray));
        //         Log::channel('sendmail')->info('Email berhasil dikirim ke: ' . $sendToEmail);
        //         return "Email berhasil dikirim";
        //     }
        // } catch (\Exception $e) {
        //     // Tangani kesalahan jika pengiriman email gagal
        //     Log::error('Gagal mengirim email: ' . $e->getMessage());
        // }
    }

    public function changestatus($status='', $entity_cd='', $doc_no='', $level_no='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'H',
            'module'        => 'LM',
        );

        $where3 = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'H',
            'module'        => 'LM',
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
            $msg = 'You Have Already Made a Request to Approval Land Hand Over SHGB Doc. No. '.$doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
            return view("emails.after", $msg1);
        } else {
            if ($status == 'A') {
                $name   = 'Approval';
                $bgcolor = '#40de1d';
                $valuebt  = 'Approve';
            }else if ($status == 'R') {
                $name   = 'Revision';
                $bgcolor = '#f4bd0e';
                $valuebt  = 'Revise';
            } else if ($status == 'C'){
                $name   = 'Cancelation';
                $bgcolor = '#e85347';
                $valuebt  = 'Cancel';
            }
            $data = array(
                'entity_cd'     => $entity_cd, 
                'doc_no'        => $doc_no, 
                'status'        => $status,
                'level_no'      => $level_no, 
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/landapprovalhandovershgb/action', $data);
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $doc_no = $request->doc_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $remarks = $request->remarks;
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Approval Land Hand Over SHGB Doc. No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
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
        return view("emails.after", $msg1);
    }
}