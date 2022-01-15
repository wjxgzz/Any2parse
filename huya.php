<?php

    // huya parse @update 2022.01.15 // 偷鸡版本,暂时能用,什么时候跟移动端同步更新uid算法以后再说.
    $rid = $_GET['id'];
    $cdnType = $_GET['cdn']; //用于指定CDN节点：al/tx/hw/bd/ws [阿里/腾讯/华为/百度/网宿] 

    //$uid = 1234567890123; // 这里测试UID可以固定不变，但需要自己抓包自己的UID,注册的为固定
    // 匿名获取游客UID
    $bstrURL = "https://udblgn.huya.com/web/anonymousLogin";
    $postdata = '{"appId":5002,"byPass":3,"context":"","version":"2.4","data":{}}';
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$bstrURL);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($ch, CURLOPT_HTTPHEADER,["Content-Type: application/json"]);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);

    $uid = $json->data->uid; 

    $bstrURL = "https://mp.huya.com/cache.php?m=Live&do=profileRoom&roomid=$rid";

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$bstrURL);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $bStreamLst = $json->data->stream->baseSteamInfoList[0];  // 获取stream信息
    // 组装播放地址
    $sStreamName = $bStreamLst->sStreamName;
    $sAntiCode = $bStreamLst->sHlsAntiCode;
    
    //data.stream.hls.multiLine 当前使用线路第一个
    $sCdnType = strtolower($json->data->stream->hls->multiLine[0]->cdnType);
    $sHlsUrl = "http://".(($cdnType=='')?$sCdnType:$cdnType).".hls.huya.com/src/";

    parse_str($sAntiCode,$params);
    $fm = base64_decode($params['fm']);
    $wsTime = $params['wsTime'];
    $msTime = strval(time()).strval(intval(1000000 + mt_rand()/mt_getrandmax()*(9999999-1000000))); // 17位时间戳 32位PHP应用

    //$seqid = $uid + time()*10000000+1234567; // uid(1234567890123) + date.now() == 17位时间戳 // 64位偷鸡
    $seqid = big_integer_add($uid,$msTime); // 32位PHP环境用这行
    $t = $params['t'];
    $ctype = $params['ctype'];
    $i = md5($seqid.'|'.$ctype.'|'.$t);

    $wsSecret = md5(str_replace(['$0','$1','$2','$3'],[$uid,$sStreamName,$i,$wsTime],$fm));  // uid_streamname_hash_wstime
    
    $sPlayUrl = $sHlsUrl.$sStreamName.'.m3u8?wsSecret='.$wsSecret.'&wsTime='.$wsTime.'&uuid=&uid='.$uid.'&txyp=o%253Ad2%253B&fs=bgct&sphdcdn=al_7-tx_3-js_3-ws_7-bd_2-hw_2&sphdDC=huya&sphd=264_*-265_*&ctype='.$ctype.'&seqid='.$seqid.'&ver=1&t='.$t;
    
    header("location:$sPlayUrl");

    // 32位PHP 大数计算法，64位不需要
    function big_integer_add($num1,$num2){
        $str1 = strval($num1);
        $str2 = strval($num2);
        $len2 = strlen($str1);
        $len3 = strlen($str2);
        $len = $len2>$len3?$len2:$len3;
        $result = '';
        $flag = 0;
        while($len--){
            $m = 0;
            $n = 0;
            if($len2>0)
                $m = $str1[--$len2];
            if($len3>0)
                $n = $str2[--$len3];
            $tmp = $m+$n+$flag;
            $flag = $tmp/10;
            $result = ($tmp%10).$result;
        }
        return $result;
    }

?>
