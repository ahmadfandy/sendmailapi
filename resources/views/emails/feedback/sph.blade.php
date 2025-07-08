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
                        @include('template.header')
                    </table>
                    <table style="margin-left:200px;width:100%;max-width:800px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 0px 30px 0px 20px">
                                    <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Untuk Tim Pemetaan,</h5>
                                    <p style="text-align:left;color: #000000; font-size: 14px;">Surat Perlepasan HAK (SPH) telah disetujui dengan detail berikut :</p>
                                    <table cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:800px;margin:0 auto;font-size: 14px;background-color:#ffffff; color: #000000;">
                                        <tr>
                                            <td>Nomor Dokumen</td>
                                            <td> : </td>
                                            <td> {{ $data['doc_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Periode SPH</td>
                                            <td> : </td>
                                            <td> {{ $data['periode'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Kode SPH</td>
                                            <td> : </td>
                                            <td> {{ $data['code_sph'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>NOP</td>
                                            <td> : </td>
                                            <td> {{ $data['nop_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nama Pemilik</td>
                                            <td>:</td>
                                            <td>{{ $data['name_owner'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Luas Tanah yang disepakati</td>
                                            <td>:</td>
                                            <td>{{ $data['laf'] }} m&sup2;</td>
                                        </tr>
                                        <tr>
                                            <td>Luas Bangunan yang disepakati</td>
                                            <td>:</td>
                                            <td>{{ $data['baf'] }} m&sup2;</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Penandatanganan SPH</td>
                                            <td>:</td>
                                            <td>{{ $data['transaction_date'] }}</td>
                                        </tr>
                                    </table>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b>Terimakasih,</b><br>
                                        {{ $data['sender_name'] }}
                                    </p>
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