<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandApprovalExtShgbMail;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandApprovalExtShgbController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $list_of_shgb_no = explode(';', $request->shgb_no);

        $shgb_no_data = [];
        foreach ($list_of_shgb_no as $shgb_no) {
            $shgb_no_data[] = $shgb_no;
        }

        $list_of_nop_no = explode(';', $request->nop_no);

        $nop_no_data = [];
        foreach ($list_of_nop_no as $nop_no) {
            $nop_no_data[] = $nop_no;
        }

        $list_of_nib_no = explode(';', $request->nib_no);

        $nib_no_data = [];
        foreach ($list_of_nib_no as $nib_no) {
            $nib_no_data[] = $nib_no;
        }

        $list_of_shgb_date = explode(';', $request->shgb_date);

        $shgb_date_data = [];
        foreach ($list_of_shgb_date as $shgb_date) {
            $shgb_date_data[] = Carbon::createFromFormat('M  j Y h:iA', $shgb_date)->format('d-m-Y');
        }

        $list_of_shgb_expired = explode(';', $request->shgb_expired);

        $shgb_expired_data = [];
        foreach ($list_of_shgb_expired as $shgb_expired) {
            $shgb_expired_data[] = Carbon::createFromFormat('M  j Y h:iA', $shgb_expired)->format('d-m-Y');
        }

        $list_of_shgb_area = explode(';', $request->shgb_area);

        $shgb_area_data = [];
        foreach ($list_of_shgb_area as $shgb_area) {
            $shgb_area_data[] = number_format((float)$shgb_area, 2, '.', ',');
        }

        $list_of_nib_no_ext = explode(';', $request->nib_no_extension);

        $nib_no_ext_data = [];
        foreach ($list_of_nib_no_ext as $nib_no_ext) {
            $nib_no_ext_data[] = $nib_no_ext;
        }

        $list_of_shgb_date_ext = explode(';', $request->shgb_date_extension);

        $shgb_date_ext_data = [];
        foreach ($list_of_shgb_date_ext as $shgb_date_ext) {
            $shgb_date_ext_data[] = Carbon::createFromFormat('M  j Y h:iA', $shgb_date_ext)->format('d-m-Y');
        }

        $list_of_shgb_expired_ext = explode(';', $request->shgb_expired_extension);

        $shgb_expired_ext_data = [];
        foreach ($list_of_shgb_expired_ext as $shgb_expired_ext) {
            $shgb_expired_ext_data[] = Carbon::createFromFormat('M  j Y h:iA', $shgb_expired_ext)->format('d-m-Y');
        }

        $list_of_urls = explode(";", trim(str_replace(' ', '%20', $request->url_link)));

        $url_data = [];
        foreach ($list_of_urls as $url) {
            $url_data[] = trim($url); // Trim any extra spaces around the URL
        }

        $list_of_files = explode(';', $request->file_name);

        $files_data = [];
        foreach ($list_of_files as $file_name) {
            $files_data[] = $file_name;
        }

        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $request->entity_cd)
        ->first();

        $dataArray = array(
            'user_id'               => $request->user_id,
            'level_no'              => $request->level_no,
            'entity_cd'             => $request->entity_cd,
            "doc_no"		        => $request->doc_no,
            "shgb_no"		        => $shgb_no_data,
            "nop_no"		        => $nop_no_data,
            "nib_no"		        => $nib_no_data,
            "shgb_date"		        => $shgb_date_data,
            "shgb_expired"		    => $shgb_expired_data,
            "shgb_area"		        => $shgb_area_data,
            "nib_no_extension"		=> $nib_no_ext_data,
            "shgb_date_ext"		    => $shgb_date_ext_data,
            "shgb_expired_ext"		=> $shgb_expired_ext_data,
            "url_link"              => $url_data,
            "file_name"             => $files_data,
            "email_addr"            => $request->email_addr,
            "user_name"             => $request->user_name,
            "sender_name"           => $request->sender_name,
            'entity_name'           => $query_get->entity_name,
            "descs"                 => $request->descs,
            "link"                  => "landapprovalextshgb",
        );

        try {
            $emailAddresses = $request->email_addr;
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            $level_no = $request->level_no;
            $approve_seq = $request->approve_seq;
        
            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                $email = $emailAddresses; // Since $emailAddresses is always a single email address (string)

                // Check if the email has been sent before for this document
                $cacheFile = 'email_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $level_no . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_land_ext_shgb/' . date('Ymd') . '/' . $cacheFile);
                $cacheDirectory = dirname($cacheFilePath);

                // Ensure the directory exists
                if (!file_exists($cacheDirectory)) {
                    mkdir($cacheDirectory, 0755, true);
                }

                // Acquire an exclusive lock
                $lockFile = $cacheFilePath . '.lock';
                $lockHandle = fopen($lockFile, 'w');
                if (!flock($lockHandle, LOCK_EX)) {
                    // Failed to acquire lock, handle appropriately
                    fclose($lockHandle);
                    throw new Exception('Failed to acquire lock');
                }

                if (!file_exists($cacheFilePath)) {
                    // Send email
                    Mail::to($email)->send(new LandApprovalExtShgbMail($dataArray));
        
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

    public function changestatus($status='', $entity_cd='', $doc_no='', $level_no='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'U',
            'module'        => 'LM',
            'trx_type'      => 'EX',
            'request_type'  => 'H4'
        );

        
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Approval Land Extension SHGB Doc. No. '.$doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
            return view("emails.after_end.after", $msg1);
        }  else {
            $where3 = array(
                'doc_no'        => $doc_no,
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'type'          => 'U',
                'module'        => 'LM',
                'trx_type'      => 'EX',
                'request_type'  => 'H4'
            );
            $query3 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where3)
            ->get();

            if(count($query3)==0){
                $msg = 'There is no Request to Land Extension SHGB Doc No. '.$doc_no ;
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
                return view('emails/landextshgb/action', $data);
            }
        }
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $doc_no = $request->doc_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $remarks = $request->remarks;
        if ($status == 'A') {
            $descstatus = "Approved";
            $notifdesc = "Approved !";
            $imagestatus = "approved.png";
        } else if ($status == 'R') {
            $descstatus = "Made a Revise Request on";
            $notifdesc = "Revised !";
            $imagestatus = "revise.png";
        } else {
            $descstatus = "Cancelled";
            $notifdesc = "Cancelled !";
            $imagestatus = "reject.png";
        }

        $pdo = DB::connection('SSI')->getPdo();
        $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_extension_shgb ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $entity_cd);
        $sth->bindParam(2, $doc_no);
        $sth->bindParam(3, $status);
        $sth->bindParam(4, $level_no);
        $sth->bindParam(5, $remarks);
        $sth->execute();
        if ($sth == true) {
            $msg = "You Have Successfully ".$descstatus." the Approval Land Extension SHGB Doc. No. ".$doc_no;
            $notif = $descstatus;
            $st = 'OK';
            $image = $imagestatus;
        } else {
            $msg = "You Failed to ".$descstatus." the Approval Land Extension SHGB Doc. No ".$doc_no;
            $notif = 'Fail to '.$descstatus;
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
