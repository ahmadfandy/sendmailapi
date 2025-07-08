<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\LandCancelNopMail;

class LandCancelNopController extends Controller
{
    public function mail(Request $request)
    {
        $cancel_nop_date = Carbon::createFromFormat('Y-m-d', $request->transaction_date)->format('d-m-Y');

        $list_of_urls = explode(';', $request->url_link);

        $url_data = [];
        foreach ($list_of_urls as $url) {
            $url_data[] = $url;
        }

        $list_of_files = explode(';', $request->file_name);

        $file_data = [];
        foreach ($list_of_files as $file) {
            $file_data[] = $file;
        }

        $dataArray = array(
            "user_id"           => $request->user_id,
            "level_no"          => $request->level_no,
            "entity_cd"         => $request->entity_cd,
            "doc_no"            => $request->doc_no,
            "descs"             => $request->descs,
            'url_link'          => $url_data,
            'file_name'         => $file_data,
            "user_name"         => $request->user_name,
            "sender_name"       => $request->sender_name,
            "nop_no"            => $request->nop_no,
            "sppt_name"         => $request->sppt_name,
            "owner_name"        => $request->owner_name,
            "cancell_remarks"   => $request->cancell_remarks,
            "transaction_date"  => $cancel_nop_date,
            "link"              => "landcancelnop",
            "subject"           => "Need Approval ".$request->descs
        );
    
        try {
            $emailAddresses = $request->email_addr;
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                $emails = is_array($emailAddresses) ? $emailAddresses : [$emailAddresses];
                
                foreach ($emails as $email) {
                    Mail::to($email)->send(new LandCancelNopMail($dataArray));
                }
                
                $sentTo = is_array($emailAddresses) ? implode(', ', $emailAddresses) : $emailAddresses;
                Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sentTo);
                return 'Email berhasil dikirim';
            } else {
                Log::channel('sendmailapproval')->warning('Tidak ada alamat email yang diberikan.');
                return "Tidak ada alamat email yang diberikan.";
            }
        } catch (\Exception $e) {
            Log::channel('sendmailapproval')->error('Gagal mengirim email: ' . $e->getMessage());
            return "Gagal mengirim email: " . $e->getMessage();
        }
    }

    public function changestatus($entity_cd ='', $doc_no ='', $status='', $level_no='')
    {
        $where = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'O',
            'module'        => 'LM',
            'trx_type'      => 'NC',
        );
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Land Approval NOP Cancel No. '.$doc_no ;
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
                'status'        => $status,
                'level_no'      => $level_no, 
                'name'          => $name,
                'bgcolor'       => $bgcolor,
                'valuebt'       => $valuebt
            );
        }
        return view('emails/landcancelnop/action')->with('data', $data);
    }

    public function update(Request $request)
    {
        $entity_cd  = $request->entity_cd;
        $doc_no     = $request->doc_no;
        $status     = $request->status;
        $level_no   = $request->level_no;
        $remarks    = $request->remarks;
        if ($status == 'A')
        {
            $statusdesc = "Approved";
            $image = "approved.png";
        } else if ($status == 'R')
        {
            $statusdesc = "Revised";
            $image = "revise.png";
        } else 
        {
            $statusdesc = "Cancelled";
            $image = "reject.png";
        }
        $pdo = DB::connection('SSI')->getPdo();
        $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_cancel_nop ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $entity_cd);
        $sth->bindParam(2, $doc_no);
        $sth->bindParam(3, $status);
        $sth->bindParam(4, $level_no);
        $sth->bindParam(5, $remarks);
        $sth->execute();
        if ($sth == true) {
            $msg = "You Have Successfully ".$statusdesc." the Land Approval NOP Cancel No. ".$doc_no;
            $notif = $statusdesc.' !';
            $st = 'OK';
            $image = $image;
        } else {
            $msg = "You Failed to ".$statusdesc." the Land Approval NOP Cancel No. ".$doc_no;
            $notif = 'Fail to '.$statusdesc.' !';
            $st = 'OK';
            $image = "reject.png";
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
