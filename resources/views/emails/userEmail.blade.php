@include('template.header')
	
	<!-- FEATURED CATEGORY STARTS -->
    <table align="center" bgcolor="#eeeeee" class="mcbc-Body-bgcolor" border="0" cellpadding="0" cellspacing="0" width="100%" mc:repeatable="r">
        <tbody>

            <tr>
                <td align="center">
                    <!--SECTION TABLE-700-->
                    <table align="center" bgcolor="#f5f5f5" border="0" cellpadding="0" cellspacing="0" class="mcbc-Light-bgcolor" width="700">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                        <tbody>
										<tr>
										<td height="60" class="auto-hide"></td>
										</tr>
                                            <!-- ROW-ONE STARTS -->
                                            <tr>
                                                <td>
                                                    <!--TABLE MIDDLE-->
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="46%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                        <tbody>
                                                        <tr>
														        <td style="padding-right:50px;" class="auto-pad">
														            <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style=" border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="left" class="heading" style="color:#333333; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:23px; line-height:26px; font-weight:400; text-transform:capitalize; letter-spacing:1px;">
                                                                                    Dear Manager
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:14px; font-weight:400; line-height:24px;">
                                                                                    Berikut ini adalah permintaan persetujuan Approve
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:14px; font-weight:400; line-height:24px;">
                                                                                    <p>Nama : {{ $data['full_name'] }}  </p>
                                                                                    <p>Debtor Account : {{ $data['debtor_acct'] }}  </p>
                                                                                </td>
                                                                            </tr>

                                                                            

                                                                            <tr>
                                                                                <td height="20"></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:14px; font-weight:400; line-height:24px;">
                                                                                    <p>Mohon segera di approve untuk proses data di Finance </p>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>
											
                                                                            <!--BUTTON START-->
                                                                            <tr>
                                                                                <td align="left" class="button-width">

                                                                                    <table align="left" bgcolor="#1ec6bc" border="0" cellpadding="0" cellspacing="0" class="button mcbc-All-Button-bgcolor" style="border-radius:3px;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td align="center" valign="middle" class="MsoNormal" style="color:#ffffff; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:14px;  font-weight:700; padding:8px 12px 8px 12px; text-transform:uppercase; letter-spacing:1px;" mc:edit="FEATURED6">
                                                                                                <a href="http://dev.ifca.co.id:8080/sendmailapi/changestatus/A/{{ $data['debtor_acct'] }}" style="text-decoration:none; color:#ffffff; text-transform:uppercase;">Approve</a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table align="right" bgcolor="#e60b0b" border="0" cellpadding="0" cellspacing="0" class="button mcbc-All-Button-bgcolor" style="border-radius:3px;margin-right:60px;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td align="center" bgcolor="#e60b0b"  valign="middle" class="MsoNormal" style="color:#ffffff; font-family:'Segoe UI', Arial, Verdana, Trebuchet MS, sans-serif; font-size:14px;  font-weight:700; padding:8px 12px 8px 12px; text-transform:uppercase; letter-spacing:1px;" mc:edit="FEATURED6">
                                                                                                <a href="http://dev.ifca.co.id:8080/sendmailapi/changestatus/R/{{ $data['debtor_acct'] }}" style="text-decoration:none; color:#ffffff; text-transform:uppercase;">Reject</a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>

                                                                                </td>
                                                                            </tr>
                                                                            <!--BUTTON END-->
											                            </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
											<tr>
										        <td height="60"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--SECTION TABLE-700 END-->


                </td>
            </tr>
            
        </tbody>
    </table>
    <!-- FEATURED CATEGORY ENDS -->
	
    @include('template.footer')