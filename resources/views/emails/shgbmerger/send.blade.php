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
                                    <p style="font-size: 16px; color: #000000; padding-top: 0px;">
                                        @empty($dataArray['entity_name'])
                                            PT. Suryacipta Swadaya
                                        @else
                                            {{ $dataArray['entity_name'] }}
                                        @endempty
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="margin-left:200px;width:100%;max-width:800px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 0px 30px 0px 20px">
                                    <h5 style="text-align:left;color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Dear Mr./Mrs. {{ $dataArray['user_name'] }}</h5>
                                    <p style="text-align:left;color: #000000; font-size: 14px;">Tolong berikan persetujuan untuk Proses Penggabungan SHGB dengan detail :</p>
                                    <table cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:800px;margin:0 auto;font-size: 14px;background-color:#ffffff; color: #000000;">
                                        <tr>
                                            <td>Nomor Dokumen </td>
                                            <td> : </td>
                                            <td> {{ $dataArray['doc_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nomor SHGB (Merge) </td>
                                            <td> : </td>
                                            <td> {{ $dataArray['merge_ref_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>NOP SHGB (Merge)</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['merge_nop'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Luas SHGB (Merge)</td>
                                            <td>:</td>
                                            <td>{{ $dataArray['merge_area'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Penggabungan SHGB</td>
                                            <td>:</td>
                                            <td>{{ $dataArray['transaction_date'] }}</td>
                                        </tr>
                                        
                                        @if(isset($dataArray['shgb_ref_no']) && is_array($dataArray['shgb_ref_no']) && count($dataArray['shgb_ref_no']) > 0)
                                            <!-- Find and display the first merge -->
                                            @if(isset($dataArray['shgb_ref_no'][0]))
                                                <tr>
                                                    <td>SHGB yang digabungkan</td>
                                                    <td>:</td>
                                                    <td>{{ $dataArray['shgb_ref_no'][0] }}</td>
                                                </tr>  
                                            @endif

                                            <!-- Display other merges -->
                                            @foreach(array_slice($dataArray['shgb_ref_no'], 1) as $merge)
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $merge }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        
                                    </table>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 14px;">
                                        <b>Terimakasih,</b><br>
                                        {{ $dataArray['sender_name'] }}
                                    </p>
                                    <br>
                                    <a href="{{ url('api') }}/{{ $dataArray['link'] }}/{{ $dataArray['entity_cd'] }}/{{ $dataArray['doc_no'] }}/A/{{ $dataArray['level_no'] }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #1ee0ac; border-radius: 4px; color: #ffffff;">Approve</a>
                                    <a href="{{ url('api') }}/{{ $dataArray['link'] }}/{{ $dataArray['entity_cd'] }}/{{ $dataArray['doc_no'] }}/R/{{ $dataArray['level_no'] }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #f4bd0e; border-radius: 4px; color: #ffffff;">Request Info</a>
                                    <a href="{{ url('api') }}/{{ $dataArray['link'] }}/{{ $dataArray['entity_cd'] }}/{{ $dataArray['doc_no'] }}/C/{{ $dataArray['level_no'] }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #e85347; border-radius: 4px; color: #ffffff;">Reject</a>
                                    <br>
                                    @php
                                        $hasAttachment = false;
                                    @endphp

                                    @foreach($dataArray['url_file'] as $key => $url_file)
                                        @if($url_file !== '' && $dataArray['file_name'][$key] !== '' && $url_file !== 'EMPTY' && $dataArray['file_name'][$key] !== 'EMPTY')
                                            @if(!$hasAttachment)
                                                @php
                                                    $hasAttachment = true;
                                                @endphp
                                                <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 14px;">
                                                    <b style="font-style:italic;">Untuk melihat lampiran, tolong klik tautan dibawah ini : </b><br>
                                            @endif
                                            <a href="{{ $url_file }}" target="_blank">{{ $dataArray['file_name'][$key] }}</a><br>
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