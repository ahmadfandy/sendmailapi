<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandApprovalHandoverShgbMail;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandApprovalHandoverShgbController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $list_of_handover_to = explode(';', $request->handover_to);
        $handover_to_data = [];
        foreach ($list_of_handover_to as $handover_to) {
            $handover_to_data[] = ($handover_to == 'L') ? 'LEGAL' : 'Eksternal';
        }

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

        $dataArrays = [
            'shgb_no' => explode(';', $request->shgb_no),
            'nop_no' => explode(';', $request->nop_no),
            'shgb_name' => explode(';', $request->shgb_name),
            'shgb_area' => explode(';', $request->shgb_area),
            'handover_to' => $handover_to_data,
        ];

        // Ensure all arrays are the same length
        $arrayLength = count($dataArrays['shgb_no']);
        foreach ($dataArrays as $key => $array) {
            if (count($array) !== $arrayLength) {
                throw new \Exception("Array length mismatch for key {$key}");
            }
        }

        $transaction_date = Carbon::createFromFormat('M j Y h:iA', $request->transaction_date)->format('d-m-Y');

        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $request->entity_cd)
        ->first();

        $dataArray = array(
            'user_id'           => $request->user_id,
            'level_no'          => $request->level_no,
            'approve_seq'       => $request->approve_seq,
            'entity_cd'         => $request->entity_cd,
            'doc_no'            => $request->doc_no,
            'email_addr'        => $request->email_addr,
            'entity_name'       => $query_get->entity_name,
            'user_name'         => $request->user_name,
            'url_link'          => $url_data,
            'file_name'         => $file_data,
            'sender_name'       => $request->sender_name,
            'shgb_no'           => $dataArrays['shgb_no'],
            'nop_no'            => $dataArrays['nop_no'],
            'shgb_name'         => $dataArrays['shgb_name'],
            'shgb_area'         => $dataArrays['shgb_area'],
            'handover_to'       => $dataArrays['handover_to'],
            'transaction_date'  => $transaction_date,
            'descs'             => $request->descs,
            'link'              => 'landapprovalhandovershgb',
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
                $cacheFilePath = storage_path('app/mail_cache/send_land_handovershgb/' . date('Ymd') . '/' . $cacheFile);
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
                    Mail::to($email)->send(new LandApprovalHandoverShgbMail($dataArray));
        
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

    public function changestatus($status='', $entity_cd='', $doc_no='', $level_no='', $approve_seq='')
    {
        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $entity_cd)
        ->first();
        
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'approve_seq'   => $approve_seq,
            'type'          => 'H',
            'module'        => 'LM',
        );

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Approval Land Hand Over SHGB Doc. No. '.$doc_no ;
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
        } else {
            $where3 = array(
                'doc_no'        => $doc_no,
                'status'        => 'P',
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'approve_seq'   => $approve_seq,
                'type'          => 'H',
                'module'        => 'LM',
            );
            $query3 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where3)
            ->get();

            if(count($query3)==0){
                $msg = 'There is no Request to Land Hand Over SHGB No. '.$doc_no ;
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
                return view('emails/landhandovershgb/action', $data);
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
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Approval Land Hand Over SHGB Doc. No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_shgb ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Approval Land Hand Over SHGB Doc. No. ".$doc_no;
                $notif = 'Fail to Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            }
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
