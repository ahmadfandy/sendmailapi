<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandApprovalHandoverLegalMail;
use App\Mail\UserEmail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandApprovalHandoverLegalController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        if ($request->handover_to == 'L')
        {
            $handover_to = 'LEGAL';
        } else {
            $handover_to = 'Eksternal';
        }

        $list_of_shgb_area = explode(';', $request->shgb_area);

        $shgb_area_data = [];
        foreach ($list_of_shgb_area as $shgb_area) {
            $shgb_area_data[] = number_format((float)$shgb_area, 2, '.', ',');
        }

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->transaction_date)->format('d-m-Y');

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

        $list_of_approve = explode('; ',  $request->approve_exist);
        $approve_data = [];
        foreach ($list_of_approve as $approve) {
            $approve_data[] = $approve;
        }

        $list_of_approve_date = explode('; ',  $request->approved_date);
        $approve_date_data = [];
        foreach ($list_of_approve_date as $approve_date) {
            $approve_date_data[] = $approve_date;
        }

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

        $list_of_shgb_name = explode(';', $request->shgb_name);

        $shgb_name_data = [];
        foreach ($list_of_shgb_name as $shgb_name) {
            $shgb_name_data[] = $shgb_name;
        }

        $query_get = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->select('entity_name')
        ->where('entity_cd', $request->entity_cd)
        ->first();

        $dataArray = array(
            'user_id'           => $request->user_id,
            'level_no'          => $request->level_no,
            'entity_cd'         => $request->entity_cd,
            'doc_no'            => $request->doc_no,
            'email_addr'        => $request->email_addr,
            'user_name'         => $request->user_name,
            'sender_name'       => $request->sender_name,
            'entity_name'       => $query_get->entity_name,
            'handover_to'       => $request->handover_to,
            'customer_name'     => $request->customer_name,
            'url_file'          => $url_data,
            'file_name'         => $file_data,
            'shgb_no'           => $shgb_no_data,
            'nop_no'            => $nop_no_data,
            'shgb_name'         => $shgb_name_data,
            'shgb_area'         => $shgb_area_data,
            'transaction_date'  => $transaction_date,
            'descs'             => $request->descs,
            'remarks'           => $request->remarks,
            'approve_list'      => $approve_data,
            'approved_date'     => $approve_date_data,
            'link'              => 'landapprovalhandoverlegal',
        );

        try {
            $sendToEmail = strtolower($request->email_addr);
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            $level_no = $request->level_no;
            $approve_seq = $request->approve_seq;
            if (!empty($sendToEmail)) {
                $email = $sendToEmail;

                // Check if the email has been sent before for this document
                $cacheFile = 'email_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $level_no . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_handover_legal/' . date('Ymd') . '/' . $cacheFile);
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
                    Mail::to($email)->send(new LandApprovalHandoverLegalMail($dataArray));

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
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function changestatus($status='', $entity_cd='', $doc_no='', $level_no='')
    {
        $where2 = array(
            'doc_no'        => $doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'M',
            'module'        => 'LM',
        );

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if(count($query)>0){
            $msg = 'You Have Already Made a Request to Approval Land Hand Over Legal Doc. No. '.$doc_no ;
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
                'status'        => 'P',
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'type'          => 'M',
                'module'        => 'LM',
            );
            $query3 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where3)
            ->get();

            if(count($query3)==0){

                $query_get = DB::connection('SSI')
                ->table('mgr.cf_entity')
                ->select('entity_name')
                ->where('entity_cd', $entity_cd)
                ->first();

                $msg = 'There is no Request to Land Hand Over Legal No. '.$doc_no ;
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

                $query_get = DB::connection('SSI')
                ->table('mgr.cf_entity')
                ->select('entity_name')
                ->where('entity_cd', $entity_cd)
                ->first();

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
                return view('emails/landhandoverlegal/action', $data);
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
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Approved the Approval Land Hand Over Legal Doc. No. ".$doc_no;
                $notif = 'Approved !';
                $st = 'OK';
                $image = "approved.png";
            } else {
                $msg = "You Failed to Approve the Approval Land Hand Over Legal Doc. No ".$doc_no;
                $notif = 'Fail to Approve !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else if($status == 'R'){
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Made a Revise Request on Approval Land Hand Over Legal Doc. No. ".$doc_no;
                $notif = 'Revised !';
                $st = 'OK';
                $image = "revise.png";
            } else {
                $msg = "You Failed to Make a Revise Request on Approval Land Hand Over Legal Doc. No. ".$doc_no;
                $notif = 'Fail to Revised !';
                $st = 'OK';
                $image = "reject.png";
            }
        } else {
            $pdo = DB::connection('SSI')->getPdo();
            $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_handover_legal ?, ?, ?, ?, ?;");
            $sth->bindParam(1, $entity_cd);
            $sth->bindParam(2, $doc_no);
            $sth->bindParam(3, $status);
            $sth->bindParam(4, $level_no);
            $sth->bindParam(5, $remarks);
            $sth->execute();
            if ($sth == true) {
                $msg = "You Have Successfully Cancelled the Approval Land Hand Over Legal Doc. No. ".$doc_no;
                $notif = 'Cancelled !';
                $st = 'OK';
                $image = "reject.png";
            } else {
                $msg = "You Failed to Cancel the Approval Land Hand Over Legal Doc. No. ".$doc_no;
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
