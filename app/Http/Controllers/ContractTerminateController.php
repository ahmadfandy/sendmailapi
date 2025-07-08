<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContractTerminateMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractTerminateController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $transaction_date = Carbon::createFromFormat('M  j Y h:iA', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'doc_no'        => $request->doc_no,
            'doc_date'        => $request->doc_date,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'transaction_date'              => $transaction_date,
            'entity_name'   => $request->entity_name,
            'tenant_name'   => $request->tenant_name,
            'sender_name'   => $request->sender_name,
            'remarks'       => $request->remarks_web,
            'project_no'    => $request->project_no,
            'user_name'     => $request->user_name,
            'rt_grp_name'   => $request->rt_grp_name,
            'link'          => 'contractterminate',
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new ContractTerminateMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $status='', $level_no='', $user_id='', $doc_date='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'T',
            'module'        => 'TM',
        );

        $where3 = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'T',
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
            $msg = 'You Have Already Made a Request to Contract Terminate No. '.$doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );return view("emails.after", $msg1);
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
            $new_doc_no = str_replace("_sla","/",$doc_no);
            $new_doc_no1 = str_replace("_ash","-",$new_doc_no);
            $data = array(
                'entity_cd'     => $entity_cd, 
                'project_no'     => $project_no, 
                'doc_no'        => $new_doc_no1, 
                'doc_date'        => $doc_date, 
                'status'        => $status,
                'level_no'      => $level_no, 
                'user_id'      => $user_id, 
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/contractterminate/action', $data);
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $project_no = $request->project_no;
        $doc_no = $request->doc_no;
        $doc_date = $request->doc_date;
        $change_date = date("d-m-Y", strtotime($doc_date));
        $status = $request->status;
        $level_no = $request->level_no;
        $user_id = $request->user_id;
        $remarks = $request->remarks;
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_terminate ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $change_date);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Contract Terminate No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Contract Terminate No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_terminate ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $change_date);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Contract Terminate No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Contract Terminate No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_terminate ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $change_date);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Contract Terminate No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Contract Terminate No. ".$doc_no;
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