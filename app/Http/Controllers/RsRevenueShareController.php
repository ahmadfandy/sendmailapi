<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RsRevenueMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RsRevenueShareController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $total_sales = number_format($request->total_sales, 2, '.', ',');
        $tariff_percent = number_format($request->tariff_percent, 2, '.', ',');
        $tariff_amt = number_format($request->tariff_amt, 2, '.', ',');
        $tax_amt = number_format($request->tax_amt, 2, '.', ',');
        $net_amt = number_format($request->net_amt, 2, '.', ',');

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $request->doc_no,
            'trx_type'      => $request->trx_type,
            'doc_date'      => $request->doc_date,
            'ref_no'        => $request->ref_no,
            'level_no'      => $request->level_no,
            'user_id'       => $request->user_id,
            'email_addr'    => $request->email_addr,
            'descs'         => $request->descs,
            'debtor_name'   => $request->debtor_name,
            'pgs_doc_no'    => $request->pgs_doc_no,
            'total_sales'   => $total_sales,
            'transaction_date'   => $transaction_date,
            'tariff_percent'=> $tariff_percent,
            'tariff_amt'    => $tariff_amt,
            'tax_amt'       => $tax_amt,
            'net_amt'       => $net_amt,
            'file_name'     => $request->file_name,
            'url_link'      => $request->url_link,
            'user_name'     => $request->user_name,
            'sender_name'   => $request->sender_name,
            'usergroup'     => $request->usergroup,
            'supervisor'    => $request->supervisor,
            'link'          => 'revenueshare'
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            if(isset($sendToEmail) && !empty($sendToEmail) && filter_var($sendToEmail, FILTER_VALIDATE_EMAIL))
            {
                Mail::to($sendToEmail)->send(new RsRevenueMail($dataArray));
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sendToEmail);
                return "Email berhasil dikirim";
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }
    
    public function changestatus($entity_cd='', $project_no='', $doc_no='', $trx_type='', $doc_date='', $ref_no='', $status='', $level_no='', $usergroup='', $user_id='', $supervisor='')
    {

        $date = strtotime($doc_date);
        $tanggal = date('Y-m-d', $date);
        $hasil = $tanggal.'T00:00:00.000';

        $where2 = array(
            'entity_cd'     =>  $entity_cd,
            'doc_no'        =>  $doc_no,
            'trx_type'      =>  $trx_type,
            'doc_date'      =>  $hasil,
            'ref_no'        =>  $ref_no,
            'status'        =>  array("A",'R', 'C'),
            'level_no'      =>  $level_no,
            'user_id'       =>  $user_id,
            'type'          =>  'A',
            'module'        =>  'RS'
        );

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to RS Revenue Share No. '.$doc_no ;
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
                'project_no'     => $project_no, 
                'doc_no'        => $doc_no, 
                'trx_type'        => $trx_type, 
                'doc_date'        => $doc_date, 
                'ref_no'    => $ref_no, 
                'status'        => $status,
                'level_no'      => $level_no,
                'usergroup'          => $usergroup,
                'user_id'          => $user_id,
                'supervisor'          => $supervisor,
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/rsrevenue/action', $data);
    }

    public function update(Request $request)
    {
        $date = strtotime($request->doc_date);
        $tanggal = date('d-m-Y', $date);

        $entity_cd = $request->entity_cd;
        $project_no = $request->project_no;
        $doc_no = $request->doc_no;
        $trx_type = $request->trx_type;
        $ref_no = $request->ref_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $usergroup = $request->usergroup;
        $user_id = $request->user_id;
        $supervisor = $request->supervisor;
        $remarks = $request->remarks;
        
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_rs_revenue_share ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $trx_type);
            $sth->bindParam(5, $tanggal);
            $sth->bindParam(6, $ref_no);
            $sth->bindParam(7, $status);
            $sth->bindParam(8, $level_no);
            $sth->bindParam(9, $usergroup);
            $sth->bindParam(10, $user_id);
            $sth->bindParam(11, $supervisor);
            $sth->bindParam(12, $remarks);
            $sth->execute();
            if ($sth == true) 
            {
                $msg = "You Have Successfully Approved the RS Revenue Share No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the RS Revenue Share No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_rs_revenue_share ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $trx_type);
            $sth->bindParam(5, $tanggal);
            $sth->bindParam(6, $ref_no);
            $sth->bindParam(7, $status);
            $sth->bindParam(8, $level_no);
            $sth->bindParam(9, $usergroup);
            $sth->bindParam(10, $user_id);
            $sth->bindParam(11, $supervisor);
            $sth->bindParam(12, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on RS Revenue Share No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on RS Revenue Share No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_rs_revenue_share ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $project_no);
            $sth->bindParam(3, $doc_no);
            $sth->bindParam(4, $trx_type);
            $sth->bindParam(5, $tanggal);
            $sth->bindParam(6, $ref_no);
            $sth->bindParam(7, $status);
            $sth->bindParam(8, $level_no);
            $sth->bindParam(9, $usergroup);
            $sth->bindParam(10, $user_id);
            $sth->bindParam(11, $supervisor);
            $sth->bindParam(12, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the RS Revenue Share No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the RS Revenue Share No. ".$doc_no;
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