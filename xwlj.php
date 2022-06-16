<?php

    // file named 'xwlj.php'
    // to used {host}/xwlj.php?id=1
    $ts = isset($_GET['ts'])?$_GET['ts']:"";
    if($ts != '')
    {
        $bstrURL = "https://ali-live.xishuirm.com/live/{$ts}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$bstrURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Referer: http://www.xishuirm.com']);
        $data = curl_exec($ch);
        curl_close($ch);
        exit();
    }


    $bstrURL = "https://api-i.xishuirm.com/v1/consumer/app_login";

    $params = [
        "type"=>0,
        "phone"=>"你的手机号码",
        "password"=>"你的明文密码",
    ];
    $headers = [
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$bstrURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $auth = $json->data->Authorization; // 建议缓存，有效期7天

    $id = $_GET['id']; // 2=新闻   1= 经济

    $bstrURL = "http://api-cms.xishuirm.com/v1/mobile/channel/play_auth?stream=https:%2F%2Fali-live.xishuirm.com%2Flive%2Fapp{$id}.m3u8";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$bstrURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,['Authorization: '.$auth]);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $auth_key = $json->data->auth_key;

    $bstrURL = "https://ali-live.xishuirm.com/live/app{$id}.m3u8?auth_key={$auth_key}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$bstrURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,['Referer: http://www.xishuirm.com']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $data = curl_exec($ch);
    curl_close($ch);
    
    header("Content-Type: application/vnd.apple.mpegURL");
    header("Content-Disposition: filename=$id.m3u8");
    echo preg_replace('/(.*?.ts)/i',"xwlj.php?ts=$1",$data);


?>
