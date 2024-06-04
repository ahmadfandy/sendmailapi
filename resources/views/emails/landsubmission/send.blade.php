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
<body width="100%" style="mso-line-height-rule: exactly; background-color: #ffffff;">
	<div style="width: 100%; background-color: #e6f0eb; text-align: center;">
        <table style="width:100%;max-width:1200px;;">
            @include('template.header')
        </table>
        <table style="width:100%;max-width:1200px;;background-color:#ffffff;align:center">
            <!-- table content -->
            <tbody>
                <tr>
                    <td style="text-align:center;padding: 0px 30px 0px 20px">
                        <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Bapak/Ibu {{ $data['user_name'] }}</h5>
                        <p style="text-align:left;color: #000000; font-size: 14px;">Tolong berikan persetujuan untuk Pengajuan Pembayaran {{ $data['doc_no'] }} Periode SPH : {{ $data['sph_trx_no'] }} dengan detail :</p>
                        <table class="remove" cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:1200px;;background-color:#ffffff;">
                            <tr>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">No.</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">Nama Pemilik</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;">Rincian Pengajuan</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;width: 25%;">NOP</th>
                                <th style="border: 1px solid #dddddd;text-align: left;padding: 2px;width: 25%;">Tanggal Pengajuan Pembayaran</th>
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
                                        <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['transaction_date'][0] }}</td>
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
                                            <td style="border: 1px solid #dddddd;text-align: left;padding: 2px;">{{ $data['transaction_date'][$i] }}</td>
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
                            <b>Terimakasih,</b><br>
                            {{ $data['sender_name'] }}
                        </p>
                        <br>
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ url('api') }}/{{ $data['link'] }}/A/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" style="height:50px;v-text-anchor:middle;width:150px;" arcsize="8%" stroke="f" fillcolor="#1ee0ac">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:18px;">Approve</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]-->
                        <a href="{{ url('api') }}/{{ $data['link'] }}/A/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" target="_blank" style="background-color:#1ee0ac;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:400;line-height:44px;text-align:center;text-decoration:none;padding: 0px 40px;margin: 10px">Approve</a>
                        <!--<![endif]-->

                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ url('api') }}/{{ $data['link'] }}/R/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" style="height:50px;v-text-anchor:middle;width:150px;" arcsize="8%" stroke="f" fillcolor="#f4bd0e">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:18px;">Request Info</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]-->
                        <a href="{{ url('api') }}/{{ $data['link'] }}/R/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" target="_blank" style="background-color:#f4bd0e;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:400;line-height:44px;text-align:center;text-decoration:none;padding: 0px 40px;margin: 10px">Request Info</a>
                        <!--<![endif]-->

                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ url('api') }}/{{ $data['link'] }}/C/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" style="height:50px;v-text-anchor:middle;width:150px;" arcsize="8%" stroke="f" fillcolor="#e85347">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:18px;">Reject</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]-->
                        <a href="{{ url('api') }}/{{ $data['link'] }}/C/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/{{ $data['level_no'] }}" target="_blank" style="background-color:#e85347;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:400;line-height:44px;text-align:center;text-decoration:none;padding: 0px 40px;margin: 10px">Reject</a>
                        <!--<![endif]-->
                        <br>
                        @php
                            $hasAttachment = false;
                        @endphp

                        @foreach($data['url_file'] as $key => $url_file)
                            @if($url_file !== '' && $data['file_name'][$key] !== '' && $url_file !== 'EMPTY' && $data['file_name'][$key] !== 'EMPTY')
                                @if(!$hasAttachment)
                                    @php
                                        $hasAttachment = true;
                                    @endphp
                                    <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b style="font-style:italic;">Untuk melihat lampiran, tolong klik tautan dibawah ini : </b><br>
                                @endif
                                <a href="{{ $url_file }}" target="_blank">{{ $data['file_name'][$key] }}</a><br>
                            @endif
                        @endforeach

                        @if($hasAttachment)
                            </p>
                        @endif

                        @php
                            $hasApproval = false;
                            $counter = 0;
                            
                            // Sort the data array based on approved date in descending order
                            array_multisort($data['approved_date'], SORT_DESC, $data['approve_list']);
                        @endphp

                        @foreach($data['approve_list'] as $key => $approve_list)
                            @if($approve_list !== '' && $approve_list !== 'EMPTY')
                                @if(!$hasApproval)
                                    @php
                                        $hasApproval = true;
                                    @endphp
                                    <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <span>Sudah disetujui oleh :</span><br>
                                @endif
                                {{ ++$counter }}. {{ $approve_list }} - {{ $data['approved_date'][$key] }}<br>
                            @endif
                        @endforeach

                        @if($hasApproval)
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