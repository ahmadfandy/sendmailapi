<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    @include('template.style')

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #e6f0eb;">
	<div style="width: 100%; background-color: #e6f0eb;">
        <table width="50%" border="0" cellpadding="0" cellspacing="0" bgcolor="#e6f0eb" style="margin-left: auto;margin-right: auto;" >
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        @include('template.header')
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;font-size: 14px; color: #000000;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 30px 30px 20px">
                                    <h5 style="margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Dear {{ $data['user_name'] }}</h5>
                                    <p style="margin-bottom: 15px; color: #000000; font-size: 14px;">Please approve No Progress {{ $data['old_doc_no'] }} with the current progress percentage of {{ $data['curr_progress'] }}% with a total progress of IDR {{ $data['amount'] }} from PO No. {{ $data['PONumberOracle']}}.</p>

                                    <p style="margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        Klik tombol PDF untuk melihat dokumen 
                                        <!-- <a href="{{ url('public/pdf/PR_BTID.pdf') }}" target="_blank" style="background-color:transparent;border-radius:4px;color:#007bf;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">PDF</a> -->
                                        <a href="{{ url('public/pdf/PR_BTID.pdf') }}" style="background-color:grey;border-radius:4px;color:#007bf;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">OPEN PDF</a>.</p>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['project_no'] }}/{{ $data['doc_no'] }}/{{ $data['ref_no'] }}/A/{{ $data['level_no'] }}/{{ $data['usergroup'] }}/{{ $data['user_id'] }}/{{ $data['supervisor'] }}/{{ $data['reason'] }}" style="background-color:#1ee0ac;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Approve</a>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['project_no'] }}/{{ $data['doc_no'] }}/{{ $data['ref_no'] }}/R/{{ $data['level_no'] }}/{{ $data['usergroup'] }}/{{ $data['user_id'] }}/{{ $data['supervisor'] }}" style="background-color:#f4bd0e;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Request Info</a>
                                    <a href="{{ url('api') }}/{{ $data['link'] }}/{{ $data['entity_cd'] }}/{{ $data['project_no'] }}/{{ $data['doc_no'] }}/{{ $data['ref_no'] }}/C/{{ $data['level_no'] }}/{{ $data['usergroup'] }}/{{ $data['user_id'] }}/{{ $data['supervisor'] }}" style="background-color:#e85347;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Reject</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        @include('template.footer')
                    </table>
               </td>
            </tr>
        </table>
    </div>
</body>
</html>