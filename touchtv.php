<?php
    //触电新闻v3接口
    //食用方法：
    //touchtv.php?pk={id} 
    //不携带pk参数为打印节目列表

    $pk = $_GET['pk'];
    $ts = time().'123';
    $headers = [
        "X-ITOUCHTV-Ca-Key:04039368653554864194910691389924",
        "X-ITOUCHTV-Ca-Timestamp:$ts",
        "X-ITOUCHTV-DEVICE-ID:IMEI_", // 偷鸡，内网测试稳定。
    ];
    $bstrURL = "https://tcdn-api.itouchtv.cn/getParam";
    $sign =base64_encode(hash_hmac("SHA256","GET\n$bstrURL\n$ts\n","qmiHeB9bKgowHqxRv0prc2cPN2EwXL1HOYu3DPiYCcaYxyxdFIyT5mAfBmr0UKPO",true));
    $headers[] = "X-ITOUCHTV-Ca-Signature:$sign";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $node = $json->node;

    array_pop($headers);
    $bstrURL = "https://api.itouchtv.cn/liveservice/v3/tvChannelList?node=$node";
    $sign = base64_encode(hash_hmac("SHA256","GET\n$bstrURL\n$ts\n","qmiHeB9bKgowHqxRv0prc2cPN2EwXL1HOYu3DPiYCcaYxyxdFIyT5mAfBmr0UKPO",true));
    $headers[] = "X-ITOUCHTV-Ca-Signature:$sign";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    $data = curl_exec($ch);
    curl_close($ch);

    if($pk == '')
    {
        $json = json_decode($data);
        foreach($json->tvChannelList as $out)
        {
            echo ($out->name.','.$out->pk.'<br />');
        }
    }
    else
    {
        preg_match('/pk":'.$pk.',.*?"url":"(.*?)"/i',$data,$result);
        $playURL = $result[1];
        header("location:$playURL");
    }
?>
