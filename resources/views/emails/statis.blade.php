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
                                    <h5 style="text-align:left;margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Dear data['user_name'] </h5>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">Please Approve Revenue Sharing with details :</p>
                                    <table style="padding-left: 40px;width: 100%; text-align:left;">
                                        <tr>
                                            <td style="width: 120px;">Document No. </td>
                                            <td>:</td>
                                            <td>data['doc_no'] </td>
                                        </tr>
                                        <tr>
                                            <td>Customer Name</td>
                                            <td>:</td>
                                            <td>data['Name'] </td>
                                        </tr>
                                        <tr>
                                            <td>Currency Code</td>
                                            <td>:</td>
                                            <td>IDR </td>
                                        </tr>
                                        <tr>
                                            <td>Total Sales</td>
                                            <td>:</td>
                                            <td>000.000.000 </td>
                                        </tr>
                                        <tr>
                                            <td>Tarif (%)</td>
                                            <td>:</td>
                                            <td>0.00 </td>
                                        </tr>
                                        <tr>
                                            <td>Base Amount</td>
                                            <td>:</td>
                                            <td>000.000.000 </td>
                                        </tr>
                                        <tr>
                                            <td>Tax Amount</td>
                                            <td>:</td>
                                            <td>000.000.000 </td>
                                        </tr>
                                        <tr>
                                            <td>Total Amount</td>
                                            <td>:</td>
                                            <td>000.000.000 </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <a href="" style="background-color:#1ee0ac;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Approve</a>
                                    <a href="" style="background-color:#f4bd0e;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Request Info</a>
                                    <a href="" style="background-color:#e85347;border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px">Reject</a>
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