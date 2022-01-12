<?php
        // huya parse
        $rid = $_GET['id'];
        $cdnType = $_GET['cdn']; //用于指定CDN节点：al/tx/hw/bd/ws [阿里/腾讯/华为/百度/网宿] tx节点不定时抽疯，建议指定al节点
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
        // 计算尾巴
        parse_str($sAntiCode,$params);
        $fm = base64_decode($params['fm']);
        $wsTime = $params['wsTime'];
        $ctype = $params['ctype'];
        $seqid = time().'000'; // uid(0) + date.now() == 13位时间戳
        $t = $params['t'];
        $i = md5($seqid.'|'.$ctype.'|'.$t);

        $wsSecret = md5(str_replace(['$0','$1','$2','$3'],['0',$sStreamName,$i,$wsTime],$fm));  // uid_streamname_hash_wstime
        $sPlayUrl = $sHlsUrl.$sStreamName.'.m3u8?wsSecret='.$wsSecret.'&wsTime='.$wsTime.'&uid=0&fm='.urlencode(base64_encode($fm)).'&ctype='.$ctype.'&seqid='.$seqid.'&ver=1&t='.$t;
        
        header("location:$sPlayUrl");


?>
