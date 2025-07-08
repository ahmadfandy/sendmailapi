<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="application/pdf">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    
    <link href="https://fonts.googleapis.com/css?family=Vollkorn:400,600" rel="stylesheet" type="text/css">
    <style>
        html, body {
            width: 100%;
            color: #000000 !important;
        }

        /* Normal font size for table */
        .remove {
            font-size: 14px; /* adjust as needed */
        }

        /* Media query for phone view */
        @media only screen and (max-width: 800px) {
            table.remove td, table.remove th {
                font-size: 1px !important;
            }
        }
    </style>
    
</head>
<body width="100%" style="mso-line-height-rule: exactly; background-color: #ffffff;color: #000000 !important;">
	<div style="width: 100%; background-color: #e6f0eb; text-align: center;">
        <table style="width:100%;max-width:1200px;;">
            @include('template.header')
        </table>
        <table style="width:100%;max-width:1200px;;background-color:#ffffff;align:center">
            <!-- table content -->
            <tbody>
                <tr>
                    <td style="text-align:center;padding: 0px 30px 0px 20px">
                        <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Tim Accounting</h5>
                        <p style="text-align:left;color: #000000; font-size: 14px;">Pengajuan Pembayaran {{ $data['doc_no'] }} Periode SPH : {{ $data['sph_trx_no'] }} telah disetujui dengan detail :</p>
                        <table class="remove" cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:1200px;font-size: 14px;background-color:#ffffff;color: #000000 !important;">
                            <tr>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">No.</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">Nama Pemilik</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">Rincian Pengajuan</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;width: 25%;">NOP</th>
                                <th style="border: 1px solid #dddddd;text-align: right;padding: 2px;width: 20%;">Nominal Pengajuan</th>
                            </tr>
                            @if(isset($data['type']) && is_array($data['type']) && count($data['type']) > 0)
                            <!-- Find and display the first merge -->
                                @if(isset($data['type'][0]))
                                    <tr>
                                        <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">1</td>
                                        <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['owner'][0] }}</td>
                                        <td class="text" style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['type'][0] }}</td>
                                        <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['nop_no'][0] }}</td>
                                        <td style="border: 1px solid #dddddd;text-align: right;padding: 2px;">Rp. {{ $data['request_amt'][0] }}</td>
                                    </tr>  
                                @endif

                                <!-- Display other merges -->
                                @for($i = 1; $i < count($data['type']); $i++)
                                    @if(isset($data['owner'][$i], $data['type'][$i], $data['nop_no'][$i], $data['sph_trx_no'][$i], $data['request_amt'][$i]))
                                        <tr>
                                            <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $i+1 }}</td>
                                            <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['owner'][$i] }}</td>
                                            <td class="text" style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['type'][$i] }}</td>
                                            <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['nop_no'][$i] }}</td>
                                            <td style="border: 1px solid #dddddd;text-align: right;padding: 2px;">Rp. {{ $data['request_amt'][$i] }}</td>
                                        </tr>
                                    @endif
                                @endfor
                            <tr>
                                <th></th>
                                <th id="total" colspan="3">Total Pengajuan : </th>
                                <th style="border: 1px solid #dddddd;text-align: right;padding: 2px;">Rp. {{ $data['sum_amt'] }}</th>
                            </tr>
                            @endif
                        </table>
                        <br>
                        <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">
                            <b>Thank you,</b><br>
                            {{ $data['sender_name'] }}
                            </p>
                        @php
                            $hasAttachment = false;
                        @endphp

                            @if($data['url_file2'] !== '' && $data['file_name2'] !== '' && $data['url_file2'] !== 'EMPTY' && $data['file_name2'] !== 'EMPTY')
                                @if(!$hasAttachment)
                                    @php
                                        $hasAttachment = true;
                                    @endphp
                                    <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b style="font-style:italic;">To view the attachment, please click the links below:</b><br>
                                @endif
                                <a href="{{ $data['url_file2'] }}" target="_blank">{{ $data['file_name2'] }}</a><br>
                            @endif

                        @if($hasAttachment)
                            </p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width:100%;max-width:1200px;;">
            @include('template.footer')
        </table>
    </div>
</body>

</html>