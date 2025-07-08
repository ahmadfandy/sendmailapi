<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\FeedbackSphMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedbackLandSphController extends Controller
{
    public function Mail(Request $request)
    {
        $list_of_owner = explode(';', $request->name_owner);

        $owner_data = [];
        foreach ($list_of_owner as $owner) {
            $owner_data[] = $owner;
        }

        $list_of_nop_no = explode(';', $request->nop_no);

        $nop_no_data = [];
        foreach ($list_of_nop_no as $nop_no) {
            $nop_no_data[] = $nop_no;
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

        $laf = number_format($request->laf, 2, '.', ',');
        $baf = number_format($request->baf, 2, '.', ',');

        $transaction_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->transaction_date)->format('d-m-Y');


        $dataArray = array(
            'doc_no'            => $request->doc_no,
            'reason'            => $request->reason,
            'approve_seq'       => $request->approve_seq,
            'descs_send'        => $request->descs_send,
            'subject'           => $request->subject,
            'user_name'         => $request->user_name,
            'sender_name'       => $request->staff_act_send,
            'entity_name'       => $request->entity_name,
            'email_addr'        => $request->email_addr,
            'user_id'           => $request->user_id,
            'level_no'          => $request->level_no,
            'entity_cd'         => $request->entity_cd,
            'transaction_date'  => $transaction_date,
            'name_owner'        => $request->name_owner,
            'nop_no'            => $request->nop_no,
            'periode'           => $request->periode,
            'code_sph'          => $request->code_sph,
            'laf'               => $laf,
            'baf'               => $baf,
            'approve_list'      => $approve_data,
            'approved_date'     => $approve_date_data,
            'descs'             => $request->descs,
        );

        try {
            $emailAddresses = strtolower($request->email_addr);
            $email_cc = $request->email_cc;
            $doc_no = $request->doc_no;
            $entity_cd = $request->entity_cd;
            $status = $request->status;
            $approve_seq = $request->approve_seq;
            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                // Explode the email addresses string into an array
                $emails = explode(';', $emailAddresses);
        
                // Initialize CC emails array
                $cc_emails = explode(';', $email_cc);
        
                // Set up the email object
                $mail = new FeedbackSphMail($dataArray);
                foreach ($cc_emails as $cc_email) {
                    $mail->cc(trim($cc_email));
                }
        
                $emailSent = false;
        
                // Check if the email has been sent before for this document
                $cacheFile = 'email_feedback_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $status . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/feedbacksph/' . date('Ymd'). '/' . $cacheFile);
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
                    Mail::to($emails)->send($mail);
        
                    // Mark email as sent
                    file_put_contents($cacheFilePath, 'sent');
                    $sentTo = implode(', ', $emails);
                    $ccList = implode(', ', $cc_emails);
        
                    Log::channel('sendmailapprovalfeedback')->info('Email Feedback doc_no ' . $doc_no . ' Entity ' . $entity_cd . ' berhasil dikirim ke: ' . $sentTo);
                    return "Email berhasil dikirim";
                    $emailSent = true;
                } else {
                    // Email was already sent
                    return 'Email has already been sent';
                }
            } else {
                Log::channel('sendmailapprovalfeedback')->warning('Tidak ada alamat email yang diberikan.');
                return "Tidak ada alamat email yang diberikan.";
            }
        } catch (\Exception $e) {
            Log::channel('sendmailapprovalfeedback')->error('Gagal mengirim email: ' . $e->getMessage());
            return "Gagal mengirim email: " . $e->getMessage();
        }
        
    }
}
