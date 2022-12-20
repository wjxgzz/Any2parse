<?php
    // 上海百视通 sh5gtv
    /*
    节目ID表：

    https://kylinapi.bbtv.cn/bestvapi/tvs?client=X18L20Z0O5CGZySIjGDwEw%3D%3D

    临时生成的ID X18L20Z0O5CGZySIjGDwEw%3D%3D 下边可以替用

    */
    // 写在前面：
    //   其实这个接口意义不是很大，有些节目可能深夜获取不到播放链接，并且那几个收费VIP节目也是取不到串
    //   更建议大家直接使用CDN+尾巴
    //
    // 这里送你们一条新的.仅供参考.IP自己找
    // http://1d1a78501cb6e992d693048cb28f398c.v.smtcdns.net/tencent-upcloud-live.bestvcdn.com.cn/live/program/live/shsshd/4000000/mnf.m3u8
    //
    
    function getDeviceId()
    {
        $uuid = createNewGUID();
        $chnId = "9141a545-4c6d-378b-7182-afda7736dd9b";
        return md5($uuid.$chnId."release"."cn.bc5g.shbestv");
    }

    function createNewGUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    // 客户端ID 获取一次长久有效，所以这里不建议动态获取。
    // 可以手动跑一下 echo getClientID(); 得到的ID 替换到下边的代码中使用。
    function getClientID()
    {
        $bstrURL = "https://kylinapi.bbtv.cn/bestvapi/getClientID?";
        $params = "channelid=9141a545-4c6d-378b-7182-afda7736dd9b&deviceid=".getDeviceId()."&mac=&timestamp=".time()."&version=1.0.MP.197";
        $bstrURL .= $params."&signature=".hash_hmac("md5",$params,"48c4e480c8e023859a773901d6457b83");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $bstrURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($data);
        return $json->clientID;
    }

    $playApiKey = "557f1d838112de4fc349b8558781fe17";
    $id = $_GET['id']; // 欢笑剧场id = 47
    $bstrURL = "https://kylinapi.bbtv.cn/bestvapi/tv/now/{$id}?client=".getClientID(); // 这里的ClientID可以长期使用，建议看上边
    //$bstrURL = "https://kylinapi.bbtv.cn/bestvapi/tv/now/{$id}?client=X18L20Z0O5CGZySIjGDwEw%3D%3D"; // ClientID可用。
    $ts = time();
    // 目前 没有强制的Header校验，可要可不要。
    $headers = [
        'timestamp: '.$ts,
        'sign: '.md5($ts.$playApiKey)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $bstrURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $playURL = $json->playUrl;
    header("location:{$playURL}");

    
?>
