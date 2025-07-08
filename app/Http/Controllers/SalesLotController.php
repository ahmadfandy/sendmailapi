<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SalesLotMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesLotController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $formattedNumber = number_format($request->land_area, 2, '.', ',');

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'user_id'           => $request->user_id,
            'level_no'          => $request->level_no,
            'entity_cd'         => $request->entity_cd,
            'doc_no'            => $request->doc_no,
            'email_addr'        => $request->email_addr,
            'descs'             => $request->descs,
            'project_no'        => $request->project_no,
            'lot_no_hd'         => $request->lot_no_hd,
            'transaction_date'              => $transaction_date,
            'rentable_area'     => $request->rentable_area,
            'temp_no'           => $request->temp_no,
            'url_link'          => $request->url_link,
            'land_area'         => $formattedNumber,
            'entity_name'       => $request->entity_name,
            'prospect_no'       => $request->prospect_no,
            'lot_no'            => $request->lot_no,
            'lot_no_old'        => $request->lot_no_old,
            'user_name'         => $request->user_name,
            'sender_name'         => $request->sender_name,
            'rt_grp_name'       => $request->rt_grp_name,
            'link'              => 'saleslot',
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new SalesLotMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($entity_cd='', $project_no='', $doc_no='', $status='', $level_no='', $rt_grp_name='', $user_id='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'H',
            'module'        => 'SA',
        );

        $where3 = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'H',
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
            $msg = 'You Have Already Made a Request to Approval Sales Lot Approval No. '.$doc_no ;
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
            } else {
                $name   = 'Cancelation';
                $bgcolor = '#e85347';
                $valuebt  = 'Cancel';
            }
            $data = array(
                'entity_cd'     => $entity_cd, 
                'project_no'    => $project_no, 
                'doc_no'        => $doc_no, 
                'status'        => $status,
                'level_no'      => $level_no, 
                'rt_grp_name'   => $rt_grp_name, 
                'user_id'       => $user_id,
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/saleslot/action', $data);
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $project_no = $request->project_no;
        $doc_no = $request->doc_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $rt_grp_name = $request->rt_grp_name;
        $user_id = $request->user_id;
        $remarks = $request->remarks;
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $status);
            $sth->bindParam(5, $level_no);
            $sth->bindParam(6, $rt_grp_name);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Approval Sales Lot Approval No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Approval Sales Lot Approval No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $status);
            $sth->bindParam(5, $level_no);
            $sth->bindParam(6, $rt_grp_name);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Approval Sales Lot Approval No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Approval Sales Lot Approval No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lot ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $status);
            $sth->bindParam(5, $level_no);
            $sth->bindParam(6, $rt_grp_name);
            $sth->bindParam(7, $user_id);
            $sth->bindParam(8, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Approval Sales Lot Approval No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Approval Sales Lot Approval No. ".$doc_no;
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