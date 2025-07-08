<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\ShgbMergerMail;

class ShgbMergerController extends Controller
{
    public function mail(Request $request)
    {
        $list_of_urls = explode(';', $request->url_file);
        $list_of_files = explode(';', $request->file_name);
        $list_of_shgb_ref_no = explode(';', $request->shgb_ref_no);

        $url_data = [];
        $file_data = [];
        $shgb_ref_no_data = [];

        foreach ($list_of_urls as $url) {
            $url_data[] = $url;
        }

        foreach ($list_of_files as $file) {
            $file_data[] = $file;
        }

        foreach ($list_of_shgb_ref_no as $ref_no) {
            $shgb_ref_no_data[] = $ref_no;
        }

        $transaction_date = Carbon::createFromFormat('M  j Y h:iA', $request->transaction_date)->format('d-m-Y');

        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $request->entity_cd)
        ->first();

        $dataArray = array(
            "user_id"       => $request->user_id,
            "level_no"      => $request->level_no,
            "entity_cd"     => $request->entity_cd,
            "doc_no"        => $request->doc_no,
            "descs"         => $request->descs,
            "merge_ref_no"  => $request->merge_ref_no,
            "merge_nop"     => $request->merge_nop,
            'entity_name'   => $query_get->entity_name,
            "merge_area"    => $request->merge_area,
            'transaction_date'=> $transaction_date,
            "shgb_ref_no"   => $shgb_ref_no_data,
            "url_file"      => $url_data,
            "file_name"     => $file_data,
            "sender_name"   => $request->sender_name,
            "user_name"     => $request->user_name,
            "link"          => "shgbmerger",
            "subject"       => "Need Approval ".$request->descs
        );
    
        try {
            $emailAddresses = $request->email_addr;
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            $level_no = $request->level_no;
            $approve_seq = $request->approve_seq;


            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                $email = $emailAddresses;

                // Check if the email has been sent before for this document
                $cacheFile = 'email_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $level_no . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_shgb_merger/' . date('Ymd') . '/' . $cacheFile);
                $cacheDirectory = dirname($cacheFilePath);

                // Ensure the directory exists
                if (!file_exists($cacheDirectory)) {
                    mkdir($cacheDirectory, 0755, true);
                }

                $lockFile = $cacheFilePath . '.lock';
                $lockHandle = fopen($lockFile, 'w');
                if (!flock($lockHandle, LOCK_EX)) {
                    // Failed to acquire lock, handle appropriately
                    fclose($lockHandle);
                    throw new Exception('Failed to acquire lock');
                }

                if (!file_exists($cacheFilePath)) {
                    // Send email
                    Mail::to($email)->send(new ShgbMergerMail($dataArray));
        
                    // Mark email as sent
                    file_put_contents($cacheFilePath, 'sent');
        
                    // Log the success
                    Log::channel('sendmailapproval')->info('Email doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $email);
                    return 'Email berhasil dikirim';
                } else {
                    // Email was already sent
                    Log::channel('sendmailapproval')->info('Email doc_no '.$doc_no.' Entity ' . $entity_cd.' already sent to: ' . $email);
                    return 'Email has already been sent to: ' . $email;
                }
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
        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $entity_cd)
        ->first();


        $where = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'J',
            'module'        => 'LM',
        );
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Land Approval SHGB Merge No. '.$doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image,
                "entity_name"   => $query_get->entity_name
            );
            return view("emails.after", $msg1);
        } else {
            $where3 = array(
                'doc_no'        => $doc_no,
                'status'        => 'P',
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'type'          => 'J',
                'module'        => 'LM',
            );
            $query3 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where3)
            ->get();

            if(count($query3)==0){
                $msg = 'There is no Request to Land Approval SHGB Merge No. '.$doc_no ;
                $notif = 'Restricted !';
                $st  = 'OK';
                $image = "double_approve.png";
                $msg1 = array(
                    "Pesan" => $msg,
                    "St" => $st,
                    "notif" => $notif,
                    "image" => $image,
                    "entity_name"   => $query_get->entity_name
                );
                return view("emails.after_end.after", $msg1);
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
                    'valuebt'       => $valuebt,
                    'entity_name'   => $query_get->entity_name
                );
                return view('emails/shgbmerger/action', $data);
            }
        }
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
        $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_sft_merge_shgb ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $entity_cd);
        $sth->bindParam(2, $doc_no);
        $sth->bindParam(3, $status);
        $sth->bindParam(4, $level_no);
        $sth->bindParam(5, $remarks);
        $sth->execute();
        if ($sth == true) {
            $msg = "You Have Successfully ".$statusdesc." the Land Approval SHGB Merge No. ".$doc_no;
            $notif = $statusdesc.' !';
            $st = 'OK';
            $image = $image;
        } else {
            $msg = "You Failed to ".$statusdesc." the Land Approval SHGB Merge No ".$doc_no;
            $notif = 'Fail to '.$statusdesc.' !';
            $st = 'OK';
            $image = "reject.png";
        }
        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $request->entity_cd)
        ->first();

        $msg1 = array(
            "Pesan" => $msg,
            "St" => $st,
            "image" => $image,
            "notif" => $notif,
            'entity_name'   => $query_get->entity_name
        );
        return view("emails.after_end.after", $msg1);
    }
}
