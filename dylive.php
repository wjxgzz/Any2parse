<?php
    // xxx要的douyin免签免验证接口 .需要长id
    
    $id = $_GET['id']; // 7120247098448153352
    $bstrURL = "https://webcast.amemv.com/webcast/room/reflow/info/?type_id=0&live_id=1&room_id={$id}&sec_user_id=&app_id=1128";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data);
    $playURL = $json->data->room->stream_url->hls_pull_url_map->FULL_HD1;
    header('location:'.$playURL);

?>
