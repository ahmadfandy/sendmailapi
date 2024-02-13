<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContractRenewMail;
use Illuminate\Support\Facades\DB;

class ContractRenewController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $new_doc_no = str_replace("/","_sla",$request->doc_no);
        $new_doc_no1 = str_replace("-","_ash",$new_doc_no);
        $contract_sum = number_format($request->contract_sum, 2, '.', ',');

        $dataArray = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $request->doc_no,
            'new_doc_no'    => $new_doc_no1,
            'ref_no'        => $request->ref_no,
            'level_no'      => $request->level_no,
            'renew_no'      => $request->renew_no,
            'tenant_name'      => $request->tenant_name,
            'lot_no'      => $request->lot_no,
            'contract_sum'      => $contract_sum,
            'rt_grp_name'   => $request->rt_grp_name,
            'user_id'       => $request->user_id,
            'commence_date' => $request->commence_date,
            'expiry_date'   => $request->expiry_date,
            'entity_name'   => $request->entity_name,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'user_name'     => $request->user_name,
            'sender_name'     => $request->sender_name,
            'link'          => 'contractrenew',
        );
        try {
            $sendToEmail = strtolower($request->email_addr);
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new ContractRenewMail($dataArray));
                Log::channel('sendmail')->info('Email berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $ref_no='',$status='', $level_no='', $user_id='', $grp_name='', $renew_no)
    {    
        $new_doc_no = str_replace("_sla","/",$doc_no);
        $new_doc_no1 = str_replace("_ash","-",$new_doc_no);

        $where2 = array(
            'doc_no'        => $new_doc_no1,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'R',
            'module'        => 'TM',
        );

        $where3 = array(
            'doc_no'        => $new_doc_no1,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'R',
            'module'        => 'TM',
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
            $msg = 'You Have Already Made a Request to Contract Renewal No. '.$new_doc_no1 ;
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
                'project_no'     => $project_no, 
                'doc_no'        => $new_doc_no1, 
                'status'        => $status,
                'level_no'      => $level_no,
                'ref_no'      => $ref_no,
                'renew_no'      => $renew_no, 
                'user_id'      => $user_id, 
                'grp_name'      => $grp_name, 
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/contractrenew/action', $data);
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $project_no = $request->project_no;
        $doc_no = $request->doc_no;
        $new_doc_no = str_replace(" ","",$doc_no);
        $ref_no = $request->ref_no;
        $renew_no = $request->renew_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $user_id = $request->user_id;
        $grp_name = $request->grp_name;
        $remarks = $request->remarks;
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $new_doc_no);
            $sth->bindParam(4, $ref_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $grp_name);
            $sth->bindParam(9, $remarks);
            $sth->bindParam(10, $renew_no);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Contract Renewal No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Contract Renewal No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $new_doc_no);
            $sth->bindParam(4, $ref_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $grp_name);
            $sth->bindParam(9, $remarks);
            $sth->bindParam(10, $renew_no);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Contract Renewal No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Contract Renewal No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $new_doc_no);
            $sth->bindParam(4, $ref_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $grp_name);
            $sth->bindParam(9, $remarks);
            $sth->bindParam(10, $renew_no);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Contract Renewal No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Contract Renewal No. ".$doc_no;
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