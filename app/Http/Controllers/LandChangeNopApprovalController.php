<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LandChangeNopMail;
use App\Mail\UserEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LandChangeNopApprovalController extends Controller
{
    public function Mail(Request $request) {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $dataArray = array(
            'user_id'       => $request->user_id,
            'level_no'      => $request->level_no,
            'entity_cd'     => $request->entity_cd,
            'entity_name'   => $request->entity_name,
            'doc_no'        => $request->doc_no,
            'email_addr'    => $request->email_addr,
            'user_name'     => $request->user_name,
            'shgb_no'       => $request->shgb_no,
            'shgb_no_bpn'   => $request->shgb_no_bpn,
            'old_nop'       => $request->old_nop,
            'new_nop'       => $request->new_nop,
            'remarks'       => $request->remarks,
            'sender_name'   => $request->sender_name,
            'descs'         => $request->descs,
            'link'          => 'approvestatusLandChangeNop',
            'date_remarks'  => 'Tanggal Perubahan NOP',
            'body'          => 'Please Approve '.$request->descs,
        );

        try {
            $emailAddresses = strtolower($request->email_addr);
            $approve_seq = $request->approve_seq;
            $entity_cd = $request->entity_cd;
            $doc_no = $request->doc_no;
            $level_no = $request->level_no;

            if (!empty($emailAddresses)) {
                $email = $emailAddresses; // Since $emailAddresses is always a single email address (string)

                // Check if the email has been sent before for this document
                $cacheFile = 'email_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $level_no . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_lm_change_nop/' . date('Ymd') . '/' . $cacheFile);
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
                    Mail::to($email)->send(new LandChangeNopMail($dataArray));

                    // Mark email as sent
                    file_put_contents($cacheFilePath, 'sent');

                    // Log the success
                    Log::channel('sendmailapproval')->info('Email Status Land Change NOP. '.$doc_no.' Entity ' . $entity_cd.' berhasil dikirim ke: ' . $email);
                    return 'Email berhasil dikirim ke: ' . $email;
                } else {
                    // Email was already sent
                    Log::channel('sendmailapproval')->info('Email Status Land Change NOP. '.$doc_no.' Entity ' . $entity_cd.' already sent to: ' . $email);
                    return 'Email has already been sent to: ' . $email;
                }
            } else {
                // No email address provided
                Log::channel('sendmail')->warning("No email address provided for document " . $doc_no);
                return "No email address provided";
            }
        } catch (\Exception $e) {
                // Tangani kesalahan jika pengiriman email gagal
                Log::error('Gagal mengirim email: ' . $e->getMessage());
            }
    }

    public function changestatus($entity_cd='', $doc_no='', $status='',$level_no='',$user_id='')
    {
        $msg = " ";
        $msg1 = " ";
        $notif = " ";
        $st = " ";
        $image = " ";

        $whereent = array(
            'entity_cd'     => $entity_cd,
        );

        $queryent = DB::connection('SSI')
        ->table('mgr.cf_entity')
        ->where($whereent)
        ->get();

        $entityName = $queryent->first()->entity_name;

        $where = array(
            'doc_no'        => $doc_no,
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'C',
            'module'        => 'LM',
            'request_type'  => 'Z4'
        );

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->where($where)
        ->whereIn('status', ["A", "R", "C"])
        ->get();

        if (count($query) > 0) {
            $msg = 'You Have Already Made a Request to Status Land Change NOP. '.$doc_no ;
            $notif = 'Restricted!';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = [
                "Pesan" => $msg,
                "entityName" => $entityName,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            ];
            return view("emails.landchangenop.after", $msg1);
        } else {
            $where2 = [
                'doc_no'        => $doc_no,
                'status'        => 'P',
                'entity_cd'     => $entity_cd,
                'level_no'      => $level_no,
                'type'          => 'C',
                'module'        => 'LM',
                'request_type'  => 'Z4'
            ];

            $query2 = DB::connection('SSI')
            ->table('mgr.cb_cash_request_appr')
            ->where($where2)
            ->get();

            if (count($query2) == 0) {
                $msg = 'There is no Status Land Change NOP. ' . $doc_no;
                $notif = 'Restricted!';
                $st  = 'OK';
                $image = "double_approve.png";
                $msg1 = [
                    "Pesan" => $msg,
                    "entityName" => $entityName,
                    "St" => $st,
                    "notif" => $notif,
                    "image" => $image
                ];
                return view("emails.landchangenop.after", $msg1);
            } else {
                $name   = " ";
                $bgcolor = " ";
                $valuebt  = " ";
                if ($status == 'A') {
                    $name   = 'Approval';
                    $bgcolor = '#40de1d';
                    $valuebt  = 'Approve';
                } elseif ($status == 'R') {
                    $name   = 'Revision';
                    $bgcolor = '#f4bd0e';
                    $valuebt  = 'Revise';
                } else {
                    $name   = 'Cancelation';
                    $bgcolor = '#e85347';
                    $valuebt  = 'Cancel';
                }
                $data = [
                    'entity_cd'     => $entity_cd, 
                    'doc_no'        => $doc_no, 
                    'status'        => $status,
                    "entityName"    => $entityName,
                    'level_no'      => $level_no, 
                    'user_id'       => $user_id,
                    'name'          => $name,
                    'bgcolor'       => $bgcolor,
                    'valuebt'       => $valuebt
                ];
                return view('emails/landchangenop/action', $data);
            }
        }
    }

    public function update(Request $request)
    {
        $entity_cd = $request->entity_cd;
        $doc_no = $request->doc_no;
        $status = $request->status;
        $level_no = $request->level_no;
        $user_id = $request->user_id;
        $remarks = $request->remarks;
        $entity_name = $request->entity_name;

        $msg = " ";
        $msg1 = " ";
        $notif = " ";
        $st = " ";
        $image = " ";

        if ($status == "A") {
            $descstatus = "Approved";
            $imagestatus = "approved.png";
        } else if ($status == "R") {
            $descstatus = "Revised";
            $imagestatus = "revise.png";
        } else {
            $descstatus = "Cancelled";
            $imagestatus = "reject.png";
        }

        $pdo = DB::connection('SSI')->getPdo();
        $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.xrl_send_mail_approval_land_change_nop ?, ?, ?, ?;");
        $sth->bindParam(1, $entity_cd);
        $sth->bindParam(2, $doc_no);
        $sth->bindParam(3, $status);
        $sth->bindParam(4, $level_no);
        $sth->execute();
        if ($sth == true) {
            $msg = "You Have Successfully Approved the Status Land Change NOP. ".$doc_no;
            $notif = 'Approved !';
            $st = 'OK';
            $image = "approved.png";
        } else {
            $msg = "You Failed to Approve the Status Land Change NOP. ".$doc_no;
            $notif = 'Fail to Approve !';
            $st = 'OK';
            $image = "reject.png";
        }
        $msg1 = array(
            "Pesan"         => $msg,
            "entityName"   => $entity_name,
            "St"            => $st,
            "notif"         => $notif,
            "image"         => $image
        );
        return view("emails.landchangenop.after", $msg1);
    }
}