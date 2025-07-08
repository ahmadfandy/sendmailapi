<?php

namespace App\Http\Controllers;

require 'vendor/autoload.php';
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SendGrid\Mail\To;
use SendGrid\Mail\Cc;
use SendGrid\Mail\Bcc;
use SendGrid\Mail\From;
use SendGrid\Mail\Content;
use SendGrid\Mail\Mail;
use SendGrid\Mail\Personalization;
use SendGrid\Mail\Subject;
use SendGrid\Mail\Header;
use SendGrid\Mail\CustomArg;
use SendGrid\Mail\SendAt;
use SendGrid\Mail\Attachment;
use SendGrid\Mail\Asm;
use SendGrid\Mail\MailSettings;
use SendGrid\Mail\BccSettings;
use SendGrid\Mail\SandBoxMode;
use SendGrid\Mail\BypassListManagement;
use SendGrid\Mail\Footer;
use SendGrid\Mail\SpamCheck;
use SendGrid\Mail\TrackingSettings;
use SendGrid\Mail\ClickTracking;
use SendGrid\Mail\OpenTracking;
use SendGrid\Mail\SubscriptionTracking;
use SendGrid\Mail\Ganalytics;
use SendGrid\Mail\ReplyTo;
use Exception;

class InvoiceIfcaController extends Controller
{
    public function index(Request $request)
    {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $company_cd = $request->company_cd;
        $id_customer = $request->id;
        $number = $request->number;
        $ref_id = $company_cd.'/'.$number;
        $invoice_date = $request->invoice_date;
        $due_date = $request->due_date;
        $cust_name = $request->name;
        $cust_email = $request->email;
        $cust_phone = $request->phone;
        $item_name = $request->item_name;
        $quantity = $request->quantity;
        $price = $request->price;
        $description = $request->description;
        $discount = $request->discount;
        $tax = $request->tax;
        $total = $request->total;
        $signature_text_header = $request->signature_text_header;
        $signature_text_footer = $request->signature_text_footer;
        $terms_condition = $request->terms_condition;
        $notes = $request->notes;
        

        $datasql = DB::table('company')->where('company_cd', $company_cd)->get();

        
        foreach ($datasql as $datainvoice) {
            $item_additional =  "additional_info :{

            }";

            $add2 =  "additional_info :{

            }";

            $items[] = [
                "name" => $item_name,
                "description" => $description,
                "quantity" => $quantity,
                "price" => $price,
                "discount" => $discount,
                "tax" => $tax,
                $item_additional
            ];

            $postData = [ 
                "invoice_date"  => $invoice_date,
                "due_date"  => $due_date,
                "number" => $ref_id,
                "customer" => array(
                    "id" => $id_customer,
                    "name" => $cust_name,
                    "email" => $cust_email,
                    "phone" => $cust_phone
                ),
                "items" => $items,
                "total" => $total,
                "signature_text_header" => $signature_text_header,
                "signature_text_footer" => $signature_text_footer,
                "terms_condition" => $terms_condition,
                "notes" => $notes,
                "send" => array(
                    "email" => false,
                    "whatsapp" => false,
                    "sms" => false
                ),
                $add2
            ];

            $datajson = json_encode($postData);

            // send data to paper
            if ($company_cd == 'IFCAINV') {
                $link    = 'https://open-api.stag.paper.id/api/v1/store-invoice';
            } else {
                $link = 'https://open-api.sandbox.paper.id/api/v1/store-invoice';
            }
            $client_id = $datainvoice->client_id;
            $client_secret = $datainvoice->client_secret;
            // $sandbox = 'https://open-api.sandbox.paper.id/api/v1/store-invoice';
            // $staging    = 'https://open-api.stag.paper.id/api/v1/store-invoice';
            $new_price = number_format( $price , 0 , '.' , ',' );
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $datajson,
                CURLOPT_HTTPHEADER => array(
                    'client_id: '.$client_id,
                    'client_secret: '.$client_secret,
                    'Content-Type: application/json'
                ),
            ));

            $responsePaper = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $responseData = json_decode($responsePaper, true);
            if ($statusCode == 200 || $statusCode == 201) {
                $success = $responseData['data'];
                $data_input = array(
                    'company_cd'        => $company_cd,
                    'id_customer'       => $id_customer,
                    'number'            => $number,
                    'ref_id'            => $ref_id,
                    'invoice_date'      => Carbon::createFromFormat('d-m-Y', $invoice_date)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'due_date'          => Carbon::createFromFormat('d-m-Y', $due_date)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'cust_name'         => $cust_name,
                    'cust_email'        => $cust_email,
                    'cust_phone'        => $cust_phone,
                    'item_name'         => $item_name,
                    'item_qty'          => $quantity,
                    'item_price'        => $price,
                    'item_desc'         => $description,
                    'discount'          => $discount,
                    'tax'               => $tax,
                    'total'             => $total,
                    'text_header'       => $signature_text_header,
                    'text_footer'       => $signature_text_footer,
                    'terms_condition'   => $terms_condition,
                    'notes'             => $notes,
                    'send_email'        => 'N',
                    'method'            => '',
                    'url_link'          => $success['payper_url'],
                    'messages'          => '',
                    'audit_date'        => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'status'            => 'Unpaid'
                );

                $insert = DB::table('trx_ifcapaper')->insert($data_input);
                if ($insert == '1'){
                    $mail = new Mail();
                    $from = new From("it@ifca.co.id", "INVOICE IFCA");
                    $tos = [
                        new To(
                            $cust_email,
                            $cust_name,
                            [
                                "subject"   =>  "Invoice no $number",
                                'name' => $cust_name,
                                'number' => $number,
                                'invoice_date'  => Carbon::createFromFormat('d-m-Y', $invoice_date)->setTimezone('Asia/Jakarta')->format('d-m-Y'),
                                'due_date'  => Carbon::createFromFormat('d-m-Y', $due_date)->setTimezone('Asia/Jakarta')->format('d-m-Y'),
                                'price'     => $new_price,
                                'description'   => $description,
                                "payper_url"    => $success['payper_url']
                            ]
                        ),
                    ];
                    $mail = new Mail(
                        $from,
                        $tos
                    );
                    // $mail->setSubject(new Subject("Invoice no $number"));
                    // $mail->addContent(new Content("text/html", "<p>Dear $cust_name, </p><br><p>Terdapat invoice dengan nomor invoice $number berjumlah $price dengan deskripsi tagihan adalah $description.</p><br><p>Klik URL berikut untuk melakukan pembayaran : $payper_url</p>"));
                    $mail->setTemplateId("d-77749d0f756344c49b7fe6ea91dd7fdf");
                    $apiKey = env('SENDGRID_API_KEY');
                    $sg = new \SendGrid($apiKey);
                    $request_body = $mail; 
                    try {
                        $responseSendGrid = $sg->client->suppression()->bounces()->_($cust_email)->get();
                        $status_code = $responseSendGrid->statusCode();
                        $arrayfill = $responseSendGrid->body();
                        if (str_contains($arrayfill, 'reason')) {

                        } else {
                            $responseSendGrid = $sg->send($request_body);
                            $where = array(
                                'company_cd'    => $company_cd,
                                'id_customer'   => $id_customer,
                                'ref_id'        => $ref_id,
                                'cust_email'    => $cust_email,
                                'item_name'     => $item_name
                            );

                            $dataUpdate = array(
                                'send_email'    => 'Y',
                            );
    
                            DB::table('trx_ifcapaper')->where($where)->update($dataUpdate);
                            
                            $ch = curl_init();

                            $url = "https://omnichannel.qiscus.com/whatsapp/v1/gcvvz-friqvdcj9yeqcui/2491/messages";
                            $headers = array(
                                'Qiscus-App-Id: gcvvz-friqvdcj9yeqcui',
                                'Qiscus-Secret-Key: 1a3eb536c1e5e262234ddf374ebcfbae',
                                'Content-Type: application/json'
                            );

                            $data = array(
                                "to" => $cust_phone,
                                "type" => "template",
                                "template" => array(
                                    "namespace" => "58f7341c_b303_4305_be72_175a427fbf44",
                                    "name" => "billing_invoice",
                                    "language" => array(
                                        "policy" => "deterministic",
                                        "code" => "id"
                                    ),
                                    "components" => array(
                                        array(
                                            "type" => "body",
                                            "parameters" => array(
                                                array(
                                                    "type" => "text",
                                                    "text" => $cust_name
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => "PT. IFCA Property365 Indonesia"
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $number
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $invoice_date
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $new_price
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $item_name
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $due_date
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => $success['payper_url']
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => "admin@ifca.co.id"
                                                ),
                                                array(
                                                    "type" => "text",
                                                    "text" => "021-8282455"
                                                )
                                            )
                                        )
                                    )
                                )
                            );

                            $options = array(
                                CURLOPT_URL => $url,
                                CURLOPT_POST => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HTTPHEADER => $headers,
                                CURLOPT_POSTFIELDS => json_encode($data)
                            );

                            curl_setopt_array($ch, $options);

                            $responseQiscus = curl_exec($ch);
                            if ($responseQiscus === false) {
                                echo "cURL Error: " . curl_error($ch);
                            }

                            curl_close($ch);

                            $callback['Error'] = false;
                            $callback['message'] = $responseData;
                            echo json_encode($callback);

                        }
                    } catch (Exception $ex) {
                        echo 'Caught exception: '.  $ex->getMessage();
                    }
                } else {
                    return response()->json([
                        "Error" => true,
                        "Pesan" => $insert
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $callback['Error'] = true;
                $callback['status_code'] = $responseData["error"];
                $callback['message'] = $responseData["error"]['message'];
                echo json_encode($callback);
            }
        }
    }

    public function invoice_payment(Request $request)
    {
        try {
            $data = $request->all();
            $id = $data['invoice']['id'];
            $ref_id = $data['invoice']['number'];
            $status = $data['invoice']['status'];
            $message = $data['message'];
            $method = $data['payment_info']['method'];

            $where=array(
                'ref_id' => $ref_id,
            );

            $get = DB::table('trx_ifcapaper')->where($where)->get();
            $data = count($get);
            $dataUpdate = array(
                'status'    => $status,
                'messages'   => $message,
                'method'    => $method,
                'paid_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            );
            if ($data == 1){
                
                $update = DB::table('trx_ifcapaper')->where($where)->update($dataUpdate);

                if ($update == 1) {
                    return response()->json([
                        "Error" => false,
                        "Pesan" => "Data has been updated successfully",
                        "Result" => $data
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        "Error" => true,
                        "Pesan" => "Data has been not updated",
                        "Result" => $data
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $insert = DB::table('trx_ifcapaper')->insert($dataUpdate);
                if ($insert == 1){
                    return response()->json([
                        "Error" => false,
                        "Pesan" => "Data has been saved successfully",
                        "Result" => $data
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        "Error" => true,
                        "Pesan" => "Data has been not saved",
                        "Result" => $data
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                "Error" => true,
                "Pesan" => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
