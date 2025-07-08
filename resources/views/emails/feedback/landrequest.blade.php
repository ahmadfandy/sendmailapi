<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
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
        
    </style>
    
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff;color: #000000;">
	<div style="width: 100%; background-color: #e6f0eb; text-align: center;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#e6f0eb" style="margin-left: auto;margin-right: auto;" >
            <tr>
                <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:600px;margin:0 auto;">
                        @include('template.header')
                    </table>
                    <table style="margin-left:200px;width:100%;max-width:840px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 0px 30px 0px 20px">
                                    <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Tim Finance & Accounting, </h5>
                                    <p style="text-align:left;color: #000000; font-size: 14px;">Permintaan pembayaran telah disetujui dengan detail :</p>
                                    <table cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:800px;margin:0 auto;font-size: 14px;background-color:#ffffff; color: #000000;">
                                    <tr>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">Nomor Dokumen</th>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">Nama Pemilik</th>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">Rincian Pengajuan</th>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">NOP</th>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">Periode SPH</th>
                                        <th style="border: 1px solid #dddddd;text-align: center;padding: 8px;">Nominal Pengajuan</th>
                                    </tr>
                                    @if(isset($data['type']) && is_array($data['type']) && count($data['type']) > 0)
                                        <!-- Find and display the first merge -->
                                        @if(isset($data['type'][0]))
                                            <tr>
                                                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['doc_no'] }}</td>
                                                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['owner'][0] }}</td>
                                                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['type'][0] }}</td>
                                                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['nop_no'][0] }}</td>
                                                <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['sph_trx_no'][0] }}</td>
                                                <td style="border: 1px solid #dddddd;padding: 8px;text-align: right;">Rp. {{ $data['request_amt'][0] }}</td>
                                            </tr>  
                                        @endif

                                        <!-- Display other merges -->
                                        @for($i = 1; $i < count($data['type']); $i++)
                                            @if(isset($data['owner'][$i]))
                                                <tr>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['doc_no'] }}</td>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['owner'][$i] }}</td>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['type'][$i] }}</td>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['nop_no'][$i] }}</td>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;">{{ $data['sph_trx_no'][$i] }}</td>
                                                    <td style="border: 1px solid #dddddd;padding: 8px;text-align: right;">Rp. {{ $data['request_amt'][$i] }}</td>
                                                </tr>
                                            @endif
                                        @endfor
                                    @endif
                                    </table>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b>Terimakasih,</b><br>
                                        {{ $data['user_name'] }}
                                    </p>
                                    <br>
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
                                </td>
                            </tr>
                        </tbody>
                        @include('template.footer')
                    </table>
                </td>
            </tr>
        </table>
        </div>
</body>
</html>