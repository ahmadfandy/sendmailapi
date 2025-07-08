<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\FeedbackLandRequestMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedbackLandRequestController extends Controller
{
    public function Mail(Request $request)
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

        $list_of_owner = explode(';', $request->owner);

        $owner_data = [];
        foreach ($list_of_owner as $owner) {
            $owner_data[] = $owner;
        }

        $list_of_nop_no = explode(';', $request->nop_no);

        $nop_no_data = [];
        foreach ($list_of_nop_no as $nop_no) {
            $nop_no_data[] = $nop_no;
        }

        $list_of_sph_trx_no = explode(';', $request->sph_trx_no);
        
        $sph_trx_no_data = [];
        foreach ($list_of_sph_trx_no as $sph_trx_no) {
            $sph_trx_no_data[] = $sph_trx_no;
        }

        $list_of_request_amt = explode(';', $request->request_amt);
        
        $request_amt_data = [];
        foreach ($list_of_request_amt as $amt) {
            $formatted_amt = number_format((float)$amt, 2, '.', ',');
            $request_amt_data[] = $formatted_amt;
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

        $dataArray = array(
            'doc_no'            => $request->doc_no,
            'descs_send'        => $request->descs_send,
            'user_name'         => $request->user_name,
            'staff_act_send'    => $request->staff_act_send,
            'entity_name'       => $request->entity_name,
            'url_file'          => $url_data,
            'file_name'         => $file_data,
            'type'              => $type_data,
            'owner'             => $owner_data,
            'nop_no'            => $nop_no_data,
            'sph_trx_no'        => $sph_trx_no_data,
            'request_amt'       => $request_amt_data,
            'approve_list'      => $approve_data,
            'approved_date'     => $approve_date_data,
            'descs'             => $request->descs, 
        );

        // var_dump($dataArray);
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
                $mail = new FeedbackLandRequestMail($dataArray);
                foreach ($cc_emails as $cc_email) {
                    $mail->cc(trim($cc_email));
                }
        
                $emailSent = false;
        
                // Check if the email has been sent before for this document
                $cacheFile = 'email_feedback_sent_' . $approve_seq . '_' . $entity_cd . '_' . $doc_no . '_' . $status . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/feedbacklandrequest/' . date('Ymd'). '/' . $cacheFile);
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
