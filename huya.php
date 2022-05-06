<?php
    // sv=2110211124&uuid算法代码 //

    $uid = '1461688433250';
    $rid = $_GET['id'];
    $cdnType = $_GET['cdn']; //用于手动指定CDN节点 AL/TX/BD/HW....
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

    $sCdnType = strtolower($json->data->stream->hls->multiLine[0]->cdnType);
    $sHlsUrl = "https://".(($cdnType=='')?$sCdnType:$cdnType).".hls.huya.com/src/";

    parse_str($sAntiCode,$params);
    $fm = base64_decode($params['fm']);
    $wsTime = $params['wsTime'];

    $msTime = time().'212';//13位时间戳;
    $seqid = big_integer_add($uid,$msTime);

    $t = 103;
    $ctype = 'tars_mobile';

    $i = md5($seqid.'|'.$ctype.'|'.$t);

    $wsSecret = md5(str_replace(['$0','$1','$2','$3'],[$uid,$sStreamName,$i,$wsTime],$fm));  // uid_streamname_hash_wstime
    
    $uuid = Kmod(substr($msTime,3,10).'212',4294967295);// % 4294967295;

    $sPlayUrl = $sHlsUrl.$sStreamName.'.m3u8?wsSecret='.$wsSecret.'&wsTime='.$wsTime.'&uuid='.$uuid.'&uid='.$uid.'&txyp=o%253Ad2%253B&fs=bgct&sphdcdn=al_7-tx_3-js_3-ws_7-bd_2-hw_2&sphdDC=huya&sphd=264_*-265_*&ctype='.$ctype.'&seqid='.$seqid.'&ver=1&t='.$t.'&sv=2110211124';

    header("location:".$sPlayUrl);

    function big_integer_add($num1,$num2) // 32位PHP大数加法
    {
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
    
    
    function Kmod($n1, $n2) // 32位PHP大数取余 
    {
        return intval(fmod(floatval($n1), $n2));
    }
?>
