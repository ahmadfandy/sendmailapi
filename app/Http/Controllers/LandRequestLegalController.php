<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandRequestLegalMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandRequestLegalController extends Controller
{
    public function index(Request $request) 
    {
        $list_of_urls = explode(';', $request->url_file);

        $url_data = [];
        foreach ($list_of_urls as $url) {
            $url_data[] = $url;
        }

        $list_of_files = explode(';', $request->file_name);

        $file_data = [];
        foreach ($list_of_files as $file) {
            $file_data[] = $file;
        }

        $list_of_type = explode(';', $request->type);

        $type_data = [];
        foreach ($list_of_type as $type) {
            $type_data[] = $type;
        }

        $list_of_request_amt = explode(';', $request->request_amt);
        
        $request_amt_data = [];
        foreach ($list_of_request_amt as $amt) {
            $formatted_amt = number_format((float)$amt, 2, '.', ',');
            $request_amt_data[] = $formatted_amt;
        }

        // $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'type'          => $type_data,
            'request_amt'   => $request_amt_data,
            'url_file'      => $url_data,
            'file_name'     => $file_data,
            'doc_no'        => $request->doc_no,
            'email_addr'    => $request->email_addr,
            'user_name'     => $request->user_name,
            'sender_name'   => $request->sender_name,
            'descs'         => $request->descs,
            'link'          => 'landrequestlegal',
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
                $cacheFilePath = storage_path('app/mail_cache/send_lm_request_legal/' . date('Ymd') . '/' . $cacheFile);
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
                    Mail::to($email)->send(new LandRequestLegalMail($dataArray));
        
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
            'type'          => 'W',
            'request_type'  => 'H5',
            'module'        => 'LM',
        );

        
        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        
        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Land Request No. '.$doc_no ;
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
            $where3 = array(
                'doc_no'        => $doc_no,
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'type'          => 'W',
                'request_type'  => 'H5',
                'module'        => 'LM',
            );
            $query3 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where3)
            ->get();

            if(count($query3)==0){
                $msg = 'There is no Request to Land Request No. '.$doc_no ;
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
                    'doc_no'        => $doc_no, 
                    'status'        => $status,
                    'level_no'      => $level_no, 
                    'name'          => $name,
                    'bgcolor'       => $bgcolor,
                    'valuebt'       => $valuebt
                );
                return view('emails/landrequestlegal/action', $data);
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
        if($status == 'A') {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_request_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Land Request No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Land Request No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_request_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Land Request No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Land Request No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_request_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Land Request No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Land Request No. ".$doc_no;
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