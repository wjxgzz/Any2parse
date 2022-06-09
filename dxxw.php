<?php
    // 大象新闻


    $id = isset($_GET['id'])?$_GET['id']:'145';

    $bstrURL = "https://pubmod.hntv.tv/program/getAuth/util/getPublicKeyAndRandom"; // pubkey
    $ts = time();
    $sign = hash('sha256', '6ca114a836ac7d73'.$ts);

    $headers = [
        'tenant_id: 1',
        'timestamp: ' .$ts,
        'sign: '.$sign,
        'User-Agent: okhttp/3.12.0'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($data);

    $rnd = $json->random;
    $pubkey = $json->publicKey;
    $pubkey = "-----BEGIN PUBLIC KEY-----\n".wordwrap($pubkey,64,"\n",true)."\n-----END PUBLIC KEY-----";

    $bstrURL = "https://pubmod.hntv.tv/program/getAuth/clientlive/getClientLiveByClassId";
    $data = "classId=11";
    openssl_public_encrypt($data,$enc_data,$pubkey);

    $data = [
        "version"=> "version.0.0.1",
        "random"=> $rnd,
        "data"=> base64_encode($enc_data)
    ];

    $headers[] = 'Content-Type: application/json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST,TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $playURL = "";
    foreach($json as $lst)
    {
        if($lst->cid == $id)
        {
            $playURL = $lst->video_streams[0];
            break;
        }
    }

    header("location:".$playURL);


    /// 频道编码 ///
    // 145 河南卫视
    // 141 都市频道
    // 146 民生频道
    // 147 法治频道
    // 148 电视剧频道
    // 149 新闻频道
    // 150 欢腾购物
    // 151 公共频道
    // 152 河南乡村频道
    // 153 河南国际
    // 154 梨园频道
    // 155 文物宝库
    // 156 武术频道
    // 157 睛彩中原
    // 163 移动戏曲频道

?>
