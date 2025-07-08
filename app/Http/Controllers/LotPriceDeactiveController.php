<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LotPriceDeactiveMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotPriceDeactiveController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $plan_descs = str_replace('&','"',$request->plan_descs);

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'doc_no'        => $request->doc_no,
            'plan_descs'    => $plan_descs,
            'ref_no'        => $request->ref_no,
            'lot_no'        => $request->lot_no,
            'transaction_date'              => $transaction_date,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'user_name'     => $request->user_name,
            'sender_name'     => $request->sender_name,
            'payment_code'  => $request->payment_code,
            'link'          => 'lotpricedeactive',
            'body'          => 'Please Approve '.$request->descs.', Payment '.$request->ref_no. ' because '.$plan_descs,
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new LotPriceDeactiveMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($status='', $entity_cd='', $doc_no='', $level_no='', $payment_code='', $lot_no='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'C',
            'module'        => 'SA',
        );

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Approval Sales Lot Price Deactive No. '.$doc_no ;
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
                'doc_no'        => $doc_no, 
                'payment_cd'    => $payment_code, 
                'status'        => $status,
                'level_no'      => $level_no,
                'lot_no'        => $lot_no,
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/lotpricedeactive/action', $data);
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $doc_no = $request->doc_no;
        $payment_cd = $request->payment_cd;
        $status = $request->status;
        $level_no = $request->level_no;
        $lot_no = $request->lot_no;
        $remarks = $request->remarks;
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lotprice_deactive ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $payment_cd);
            $sth->bindParam(4, $lot_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $remarks);
            $sth->execute();
            if ($sth == true) 
            {
                $msg = "You Have Successfully Approved the Approval Sales Lot Price Deactive No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Approval Sales Lot Price Deactive No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lotprice_deactive ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $payment_cd);
            $sth->bindParam(4, $lot_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Approval Sales Lot Price Deactive No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Approval Sales Lot Price Deactive No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_sales_lotprice_deactive ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $payment_cd);
            $sth->bindParam(4, $lot_no);
            $sth->bindParam(5, $status);
            $sth->bindParam(6, $level_no);
            $sth->bindParam(7, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Approval Sales Lot Price Deactive No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Approval Sales Lot Price Deactive No. ".$doc_no;
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