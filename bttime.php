<?php
    // 北京时间官网 BRTV 解密DEMO
    // 请求是加盐MD5,解密是 URL 反串后两次b64解码
    // 节目 GID 请自行获取添加.这里写死的,仅演示作用.

    $t = time();
    $id = "573ib1kp5nk92irinpumbo9krlb"; //电视台gid
    $type_id = "151";
    $salt = 'TtJSg@2g*$K4PjUH';
    $sign = substr(md5("{$id}{$type_id}{$t}{$salt}"),0,8);
    $bstrURL = "https://pc.api.btime.com/video/play?from=pc&id={$id}&type_id={$type_id}&timestamp={$t}&sign={$sign}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    $data = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($data);
    $playURL = base64_decode(base64_decode(strrev($json->data->video_stream[0]->stream_url)));
    header("location:". $playURL);

?>
