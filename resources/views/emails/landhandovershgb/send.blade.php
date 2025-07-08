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
            <tbody>
                <tr>
                    <td style="text-align: center; padding-bottom:25px;color: #000000 !important;">
                        <img width = "120" src="{{ url('/public/images/header.png') }}" alt="logo">
                        <p style="font-size: 16px; color: #000000; padding-top: 0px;">
                            @empty($data['entity_name'])
                                PT. Suryacipta Swadaya
                            @else
                                {{ $data['entity_name'] }}
                            @endempty
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width:100%;max-width:1200px;;background-color:#ffffff;align:center">
            <!-- table content -->
            <tbody>
                <tr>
                    <td style="text-align:center;padding: 0px 30px 0px 20px">
                        <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Bapak/Ibu {{ $data['user_name'] }}</h5>
                        <p style="text-align:left;color: #000000; font-size: 14px;">Tolong berikan persetujuan untuk Proses Penyerahan SHGB & SPPT : </p>
                        <table class="remove" cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:1200px;background-color:#ffffff;">
                            <tr>
                                <th style="border: 1px solid #000; text-align: center;">No. SHGB</th>
                                <th style="border: 1px solid #000; text-align: center;">No. SPPT</th>
                                <th style="border: 1px solid #000; text-align: center;">Nama</th>
                                <th style="border: 1px solid #000; text-align: center;">Luas SHGB</th>
                                <th style="border: 1px solid #000; text-align: center;">Diserahkan pada</th>
                                <th style="border: 1px solid #000; text-align: center;">Tanggal Serah Terima</th>
                            </tr>
                            @if(isset($data['shgb_no']) && is_array($data['shgb_no']) && count($data['shgb_no']) > 0)
                                @for($i = 0; $i < count($data['shgb_no']); $i++)
                                    @if(isset($data['shgb_no'][$i], $data['nop_no'][$i], $data['shgb_name'][$i], $data['shgb_area'][$i], $data['handover_to'][$i], $data['transaction_date']))
                                        <tr>
                                            <td style="border: 1px solid #000;padding: 5px;">{{ $data['shgb_no'][$i] }} </td>
                                            <td style="border: 1px solid #000;padding: 5px;">{{ $data['nop_no'][$i] }} </td>
                                            <td style="border: 1px solid #000;padding: 5px;">{{ $data['shgb_name'][$i] }} </td>
                                            <td style="border: 1px solid #000; text-align: right;padding: 5px;">{{ $data['shgb_area'][$i] }} </td>
                                            <td style="border: 1px solid #000;padding: 5px;">{{ $data['handover_to'][$i] }} </td>
                                            <td style="border: 1px solid #000;padding: 5px;">{{ $data['transaction_date'] }} </td>
                                        </tr>
                                    @endif
                                @endfor
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

                        @foreach($data['url_link'] as $key => $url_link)
                            @if($url_link !== '' && $data['file_name'][$key] !== '' && $url_link !== 'EMPTY' && $data['file_name'][$key] !== 'EMPTY')
                                @if(!$hasAttachment)
                                    @php
                                        $hasAttachment = true;
                                    @endphp
                                    <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b style="font-style:italic;">Untuk melihat lampiran, tolong klik tautan dibawah ini : </b><br>
                                @endif
                                <a href="{{ $url_link }}" target="_blank">{{ $data['file_name'][$key] }}</a><br>
                            @endif
                        @endforeach

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