<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SalesLotActivityProspNewMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesLotActivityProspnewController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $request->doc_no,
            'transaction_date'              => $transaction_date,
            'prospect_no'   => $request->prospect_no,
            'lot_no'        => $request->lot_no,
            'email_addr'    => $request->email_addr,
            'user_name'     => $request->user_name,
            'from'          => $request->from,
            'name'          => $request->name,
            'entity_name'   => $request->entity_name,
            'descs'         => $request->descs,
            'date_remarks'  => 'Date of Lot Prospect Activity',
            'link'          => 'saleslotactivityprospnew',
            'body'          => 'Please Approve '.$request->descs,
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new SalesLotActivityProspNewMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $status='', $level_no='', $user_id='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'M',
            'module'        => 'SA',
        );

        $where3 = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'M',
            'module'        => 'SA',
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
            $msg = 'You Have Already Made a Request to Sales Lot Activity Prosp No. '.$doc_no ;
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
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot_activity_prosp ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $doc_no);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $level_no);
                $sth->bindParam(6, $user_id);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Approved the Sales Lot Activity Prosp No. ".$doc_no;
                    $notif = 'Approved !';
                    $st = 'OK';
                    $image = "approved.png";
                } else {
                    $msg = "You Failed to Approve the Sales Lot Activity Prosp No ".$doc_no;
                    $notif = 'Fail to Approve !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else if($status == 'R'){
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot_activity_prosp ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $doc_no);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $level_no);
                $sth->bindParam(6, $user_id);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Made a Revise Request on Sales Lot Activity Prosp No. ".$doc_no;
                    $notif = 'Revised !';
                    $st = 'OK';
                    $image = "revise.png";
                } else {
                    $msg = "You Failed to Make a Revise Request on Sales Lot Activity Prosp No. ".$doc_no;
                    $notif = 'Fail to Revised !';
                    $st = 'OK';
                    $image = "reject.png";
                }
            } else {
                $pdo = DB::connection('SSI')->getPdo();
                $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot_activity_prosp ?, ?, ?, ?, ?, ?;");
                $sth->bindParam(1, $entity_cd);
                $sth->bindParam(2, $project_no);
                $sth->bindParam(3, $doc_no);
                $sth->bindParam(4, $status);
                $sth->bindParam(5, $level_no);
                $sth->bindParam(6, $user_id);
                $sth->execute();
                if ($sth == true) {
                    $msg = "You Have Successfully Cancelled the Sales Lot Activity Prosp No. ".$doc_no;
                    $notif = 'Cancelled !';
                    $st = 'OK';
                    $image = "reject.png";
                } else {
                    $msg = "You Failed to Cancel the Sales Lot Activity Prosp No. ".$doc_no;
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