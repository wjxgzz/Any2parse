<?php
    // 1905.com -- CCTV6直播
    // Patch@2024.11.04
    // 有的用就静静拿去用，天天往山上搬是几个意思？

    $salt = "2bcc2c6ab75dac016d20181bfcd1ee0697c78018"; // 盐
    $bstrURL = "https://profile.m1905.com/mvod/liveinfo.php";
    $sStreamName = 'LIVEI56PNI726KA7A';  /// CCTV6：LIVENLPG8RMKR5TW6  /// 1905： LIVENCOI8M4RGOOJ9  LIVE8J4LTCXPI7QJ5
    $ts = time();
    //$playid = substr($ts,-4).'12312345678';
    $params = [
        'cid'=> 999999,
        'expiretime'=> $ts+600,
        'nonce'=> $ts,
        'page'=> 'https://www.1905.com/',
        'playerid'=> '0', // 测试这里可以为 0 或者使用上边的算法也成
        'streamname'=> $sStreamName,
        'uuid'=> strtolower(createNewGUID())
    ];

    $sign = sha1(http_build_query($params).'.'.$salt);
    $params['appid'] = 'GEalPdWA';
    $headers = [
        'Authorization: '.$sign,
        'Content-Type: application/json',
        'Origin: https://www.1905.com'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    
    // StreamName 竟然不对应？.不知道在搞什么
    $playURL = 'https://hlslive.1905.com'.$json->data->path->hd->path.$json->data->sign->hd->sign;
    header('location:'.$playURL);

    function createNewGUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
?>
