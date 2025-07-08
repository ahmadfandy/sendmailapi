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
        table {
            margin: 50 auto;
        }
    </style>
    
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff;color: #000000;">
	<div style="width: 100%; background-color: #e6f0eb; text-align: center;">
        <table width="80%" border="0" cellpadding="0" cellspacing="0" bgcolor="#e6f0eb" style="margin-left: auto;margin-right: auto;" >
            <tr>
                <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:600px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px;color: #000000 !important;">
                                    <img width = "120" src="{{ url('/public/images/header.png') }}" alt="logo">
                                    <p style="font-size: 16px; color: #000000; padding-top: 0px;">{{ $data['entity_name'] }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="margin-left:200px;width:100%;max-width:800px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 0px 30px 0px 20px">
                                    <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Bapak/Ibu {{ $data['user_name'] }}</h5>
                                    <p style="text-align:left;color: #000000; font-size: 14px;">Tolong berikan persetujuan untuk proses Perubahan NOP dengan detail :</p>
                                    <table cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:800px;margin:0 auto;font-size: 14px;background-color:#ffffff; color: #000000;">
                                        <tr>
                                            <td>Nomor SHGB Internal</td>
                                            <td> : </td>
                                            <td> {{ $data['shgb_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nomor SHGB BPN</td>
                                            <td> : </td>
                                            <td> {{ $data['shgb_no_bpn'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>NOP Awal</td>
                                            <td>:</td>
                                            <td>{{ $data['old_nop'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>NOP Baru</td>
                                            <td>:</td>
                                            <td>{{ $data['new_nop'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Keterangan</td>
                                            <td>:</td>
                                            <td>{{ $data['remarks'] }}</td>
                                        </tr>
                                    </table>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b>Terimakasih,</b><br>
                                        {{ $data['sender_name'] }}
                                    </p>
                                    <br>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/A/{{ $data['level_no'] }}/{{ $data['user_id'] }}" style="background-color:#1ee0ac;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Approve</a>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/R/{{ $data['level_no'] }}/{{ $data['user_id'] }}" style="background-color:#f4bd0e;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Request Info</a>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['doc_no'] }}/C/{{ $data['level_no'] }}/{{ $data['user_id'] }}" style="background-color:#e85347;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Reject</a>
                                    <br>
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