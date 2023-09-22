<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
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

        $dataArray = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $request->doc_no,
            'new_doc_no'        => $new_doc_no1,
            'level_no'      => $request->level_no,
            'rt_grp_name'   => $request->rt_grp_name,
            'user_id'       => $request->user_id,
            'commence_date'       => $request->commence_date,
            'expiry_date'       => $request->expiry_date,
            'entity_name'       => $request->entity_name,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'user_name'     => $request->user_name,
            'link'          => 'contractrenew',
        );
        $sendToEmail = strtolower($request->email_addr);
        if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
        {
            Mail::to($sendToEmail)
                ->send(new ContractRenewMail($dataArray));
            $callback['Error'] = true;
            $callback['Pesan'] = 'sendToEmail';
            echo json_encode($callback);
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $status='', $level_no='', $user_id='', $grp_name='')
    {
        $new_doc_no = str_replace("_sla","/", $doc_no);
        $new_doc_no1 = str_replace("_ash","-", $new_doc_no);

        $new_level_no = $level_no + 1;

        $where2 = array(
            'doc_no'        => $new_doc_no1,
            'status'        => array("A",'R', 'C'),
            'level_no'      => $new_level_no,
            'entity_cd'     => $entity_cd,
            'type'          => 'R',
            'module'        => 'TM',
        );

        $where3 = array(
            'doc_no'        => $new_doc_no1,
            'status'        => "P",
            'level_no'      => $new_level_no,
            'entity_cd'     => $entity_cd,
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
        } else {
            if($status == 'A') {
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no1);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $new_level_no);
                $sth->bindParam(6, $grp_name);
                $sth->bindParam(7, $user_id);
                $sth->bindParam(8, 0);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Approved the Contract Renewal No. ".$new_doc_no1;
                    $notif = 'Approved !';
                    $st = 'OK';
                    $image = "approved.png";
                } else {
                    $msg = "You Failed to Approve the Contract Renewal No ".$new_doc_no1;
                    $notif = 'Fail to Approve !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else if($status == 'R'){
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no1);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $new_level_no);
                $sth->bindParam(6, $grp_name);
                $sth->bindParam(7, $user_id);
                $sth->bindParam(8, 0);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Made a Revise Request on Contract Renewal No. ".$new_doc_no1;
                    $notif = 'Revised !';
                    $st = 'OK';
                    $image = "revise.png";
                } else {
                    $msg = "You Failed to Make a Revise Request on Contract Renewal No. ".$new_doc_no1;
                    $notif = 'Fail to Revised !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else {
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_tm_contract_renewal ?, ?, ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $new_doc_no1);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $new_level_no);
                $sth->bindParam(6, $grp_name);
                $sth->bindParam(7, $user_id);
                $sth->bindParam(8, 0);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Cancelled the Contract Renewal No. ".$new_doc_no1;
                    $notif = 'Cancelled !';
                    $st = 'OK';
                    $image = "reject.png";
                } else {
                    $msg = "You Failed to Cancel the Contract Renewal No. ".$new_doc_no1;
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