<?php
    // PPTV the new version code //
    $id = $_GET['id'];

    if(!is_numeric($id))
    {
        $bstrURL = "http://v.pptv.com/show/{$id}.html";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_URL,$bstrURL);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $data = curl_exec($ch);
        curl_close($ch);
        preg_match('/"cid":(\d+)/i',$data,$re);
        $id = $re[1];
    }
    $bstrURL = "https://web-play.pptv.com/webplay3-0-{$id}.xml?o=0&version=6&type=mhpptv&appid=pptv.web.h5&appplt=web&appver=4.1.16&cb=a";
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_URL,$bstrURL);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data = curl_exec($ch);
    curl_close($ch);
    $data = substr($data,2,strlen($data)-6);

    $json = json_decode($data);
    $dwLen = count($json->childNodes);
    $json = $json->childNodes[$dwLen-5];
    $rid = str_replace(".mp4",".m3u8", $json->rid);
    $host = $json->childNodes[0]->childNodes[0];
    $k = str_replace('&bppcataid=1','',urldecode($json->childNodes[5]->childNodes[0]));
    
    $playUrl = "http://{$host}/{$rid}?h5vod.ver=2.1.4&k={$k}&type=mhpptv&sv=4.1.16";
    header("location:{$playUrl}");
?>
