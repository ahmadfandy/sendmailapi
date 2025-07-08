<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendPoSMail;
use App\Mail\FeedbackMail;
use App\Mail\StaffActionMail;
use App\Mail\StaffActionPoRMail;
use App\Mail\StaffActionPoSMail;
use Carbon\Carbon;
use PDO;
use DateTime;


class AutoSendController extends Controller
{
    public function index()
    {
        ini_set('memory_limit', '8192M');

        $query = DB::connection('SSI')
        ->table('mgr.cb_cash_request_appr')
        ->whereNull('sent_mail_date')
        ->where('status', 'P')
        ->where('audit_date', '>=', DB::raw("CONVERT(datetime, '2024-06-28', 120)"))
        ->orderBy('doc_no', 'desc')
        ->get();

        foreach ($query as $data) {
            $entity_cd = $data->entity_cd;
            $trx_type = $data->trx_type;
            $doc_no = $data->doc_no;
            $doc_date = $data->doc_date;
            $ref_no = $data->ref_no;
            $staff_id = $data->staff_id;
            $request_type = $data->request_type;
            $user_id = $data->user_id;
            $level_no = $data->level_no;
            $status = $data->status;
            $module = $data->module;
            $type = $data->TYPE;
            $dateTime = new DateTime($doc_date);
            $remarks = '0';

            if ($type == 'A' && $module == "CM") {
                $exec = 'mgr.xrl_send_mail_approval_cm_progress';
            } else if ($type == 'B' && $module == "CM") {
                $exec = 'mgr.xrl_send_mail_approval_cm_vo';
            } else if ($type == 'C' && $module == "CM") {
                $exec = 'mgr.xrl_send_mail_approval_cm_contract_done';
            } else if ($type == 'D' && $module == "CM") {
                $exec = 'mgr.xrl_send_mail_approval_cm_contract_close';
            }
            $whereUg = array(
                'user_name' => $user_id
            );

            $queryUg = DB::connection('SSI')
            ->table('mgr.security_groupings')
            ->where($whereUg)
            ->get();

            $user_group = $queryUg[0]->group_name;

            if ($level_no == 1) {
                $statussend = 'P';
                $downLevel = '0';
                var_dump($doc_no);
                var_dump($status);
                var_dump($level_no);
            } else if ($level_no > 1){
                $downLevel  = $level_no - 1;
                $statussend = 'A';
                $wherebefore = array(
                    'doc_no' => $doc_no,
                    'entity_cd' => $entity_cd,
                    'level_no'  => $downLevel
                );
    
                $querybefore = DB::connection('SSI')
                ->table('mgr.cb_cash_request_appr')
                ->where($wherebefore)
                ->get();
    
                $level_data = $querybefore[0]->status;

                if ($level_data == 'A'){
                    echo 'ready to send';
                }
            }
        }
    }
}
