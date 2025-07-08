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
                                <p>Please Give a Remarks to <?php echo $doc_no?> <?php echo $name?> : </p>
                                    <form id="frmEditor" class="form-horizontal" method="POST" action="{{url('/api/contractterminatelot/update')}}" enctype="multipart/form-data">
                                    @csrf
                                        <input type="text" id="entity_cd" name="entity_cd" value="<?php echo $entity_cd?>" hidden>
                                        <input type="text" id="project_no" name="project_no" value="<?php echo $project_no?>" hidden>
                                        <input type="text" id="doc_no" name="doc_no" value="<?php echo $doc_no?>" hidden>
                                        <input type="text" id="doc_date" name="doc_date" value="<?php echo $doc_date?>" hidden>
                                        <input type="text" id="status" name="status" value="<?php echo $status?>" hidden>
                                        <input type="text" id="level_no" name="level_no" value="<?php echo $level_no?>" hidden>
                                        <input type="text" id="user_id" name="user_id" value="<?php echo $user_id?>" hidden>
                                        <div class="form-group">
                                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                                        </div>                                          
                                        <input type="submit" class="btn" style="background-color:<?php echo $bgcolor?>;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0px 40px;margin: 10px" value=<?php echo $valuebt?>>
                                    </form>
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