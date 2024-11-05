<?php
    // 1905.com -- CCTV6直播
    // Patch@2024.11.06
    // 旧接口目前暂未删除 更新盐值.appId
    // 终结不再更新： 旧域名 m3u8/ts 已需要referer.转发流问题不再细说 // 山上demo挺多 自己写~~
    // 暂时有不需要referer的域名

    $salt = "689d471d9240010534b531f8409c9ac31e0e6521"; // 盐
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
    $params['appid'] = 'W0hUwz8D';
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
    
    // 替换暂时不需要referer的域名 //
    $playURL = 'https://hlslive2.ks-cdn.m1905.com'.$json->data->path->hd->uri.$json->data->sign->hd->hashuri;
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
