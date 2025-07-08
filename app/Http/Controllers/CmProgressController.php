<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NineVarMail;
use App\Mail\CmProgressMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CmProgressController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $new_doc_no = str_replace("/","-",$request->doc_no);
        $new_ref_no = str_replace("/","-",$request->ref_no);

        $formattedNumber = number_format($request->amount, 2, '.', ',');
        $formattedNumber_prev = number_format($request->prev_progress_amt, 2, '.', ',');

        $dataArray = array(
            'entity_cd'         => $request->entity_cd,
            'project_no'        => $request->project_no,
            'doc_no'            => $new_doc_no,
            'ref_no'            => $new_ref_no,
            "old_doc_no"        => $request->doc_no,
            'old_ref_no'        => $request->ref_no,
            'level_no'          => $request->level_no,
            'progress_no'       => $request->progress_no,
            'user_id'           => $request->user_id,
            'email_addr'        => $request->email_addr,
            'contract_no'       => $request->contract_no,
            'descs'             => $request->descs,
            'usergroup'         => $request->usergroup,
            'user_name'         => $request->user_name,
            'supervisor'        => $request->supervisor,
            'entity_name'       => $request->entity_name,
            "curr_progress"	    => $request->curr_progress,
            "prev_progress"	    => $request->prev_progress,
            "amount"		    => $formattedNumber,
            "prev_progress_amt" => $formattedNumber_prev,
            "PONumberOracle"    => $request->PONumberOracle,
            'link'              => 'cmprogress',
        );
        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new CmProgressMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $ref_no='', $status='', $level_no='', $usergroup='', $user_id='', $supervisor='')
    {

        $new_doc_no = str_replace("-","/",$doc_no);
        $new_ref_no = str_replace("-","/",$ref_no);

        $where2 = array(
            'entity_cd'     =>  $entity_cd,
            'doc_no'        =>  $new_doc_no,
            'ref_no'        =>  $new_ref_no,
            'status'        =>  array("A",'R', 'C'),
            'level_no'      =>  $level_no,
            'user_id'       =>  $user_id,
            'type'          =>  'A',
            'module'        =>  'CM'
        );

        $where3 = array(
            'entity_cd'     =>  $entity_cd,
            'doc_no'        =>  $new_doc_no,
            'ref_no'        =>  $new_ref_no,
            'status'        =>  'P',
            'level_no'      =>  $level_no,
            'user_id'       =>  $user_id,
            'type'          => 'A',
            'module'        => 'CM',
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
            $msg = 'You Have Already Made a Request to CM Progress No. '.$doc_no ;
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
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_cm_progress ?, ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no);
                $sth->bindParam(4, $new_ref_no);
                $sth->bindParam(5, $status);
                $sth->bindParam(6, $level_no);
                $sth->bindParam(7, $usergroup);
                $sth->bindParam(8, $user_id);
                $sth->bindParam(9, $supervisor);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Approved the CM Progress No. ".$doc_no;
                    $notif = 'Approved !';
                    $st = 'OK';
                    $image = "approved.png";
                } else {
                    $msg = "You Failed to Approve the CM Progress No ".$doc_no;
                    $notif = 'Fail to Approve !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else if($status == 'R'){
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_cm_progress ?, ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no);
                $sth->bindParam(4, $new_ref_no);
                $sth->bindParam(5, $status);
                $sth->bindParam(6, $level_no);
                $sth->bindParam(7, $usergroup);
                $sth->bindParam(8, $user_id);
                $sth->bindParam(9, $supervisor);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Made a Revise Request on CM Progress No. ".$doc_no;
                    $notif = 'Revised !';
                    $st = 'OK';
                    $image = "revise.png";
                } else {
                    $msg = "You Failed to Make a Revise Request on CM Progress No. ".$doc_no;
                    $notif = 'Fail to Revised !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else {
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_cm_progress ?, ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no);
                $sth->bindParam(4, $new_ref_no);
                $sth->bindParam(5, $status);
                $sth->bindParam(6, $level_no);
                $sth->bindParam(7, $usergroup);
                $sth->bindParam(8, $user_id);
                $sth->bindParam(9, $supervisor);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Cancelled the CM Progress No. ".$doc_no;
                    $notif = 'Cancelled !';
                    $st = 'OK';
                    $image = "reject.png";
                } else {
                    $msg = "You Failed to Cancel the CM Progress No. ".$doc_no;
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