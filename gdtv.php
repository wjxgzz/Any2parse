<?php

    /*0: {pk: 43, name: "广东卫视",…}
    1: {pk: 44, name: "广东珠江",…}
    2: {pk: 45, name: "广东新闻",…}
    3: {pk: 48, name: "广东公共",…}
    4: {pk: 47, name: "广东体育",…}
    5: {pk: 51, name: "南方卫视",…}
    6: {pk: 49, name: "经济科教",…}
    7: {pk: 53, name: "广东影视",…}
    8: {pk: 16, name: "广东综艺",…}
    9: {pk: 46, name: "广东国际",…}
    10: {pk: 54, name: "广东少儿",…}
    11: {pk: 66, name: "嘉佳卡通",…}
    12: {pk: 42, name: "南方购物",…}
    13: {pk: 15, name: "岭南戏曲",…}
    14: {pk: 67, name: "广东房产",…}
    15: {pk: 13, name: "现代教育",…}
    16: {pk: 74, name: "广东移动",…}
    17: {pk: 75, name: "GRTN文化频道",…}*/
    
    // gdtv.php?id=43

    $id = $_GET['id']; // 43 = 广东卫视
    $cache = new Cache(3600,"cache/");
    $playURL = $cache->get("gdtv_".$id."_cache");
    if(!$playURL)
    {
        
        $ts = time().'790';
        $headers = [
            "content-type: application/json",
            "referer: https://www.gdtv.cn/",
            "origin: https://www.gdtv.cn",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36",
            "x-itouchtv-ca-key: 89541443007807288657755311869534",
            "x-itouchtv-ca-timestamp: $ts",
            "x-itouchtv-client: WEB_PC",
            "x-itouchtv-device-id: WEB_".createNewGUID(),
        ];
        

        $bstrURL = "https://tcdn-api.itouchtv.cn/getParam";
        $sign = base64_encode(hash_hmac("SHA256","GET\n$bstrURL\n$ts\n","dfkcY1c3sfuw0Cii9DWjOUO3iQy2hqlDxyvDXd1oVMxwYAJSgeB6phO8eW1dfuwX",true));
        $headers[] = "x-itouchtv-ca-signature: $sign";
        
       

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
        
        // 进入wss取串
        
        $contextOptions = [
            'ssl' => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];
        $context = stream_context_create($contextOptions);
        $sock = stream_socket_client("ssl://tcdn-ws.itouchtv.cn:3800",$errno,$errstr,1,STREAM_CLIENT_CONNECT,$context);
        stream_set_timeout($sock,1);
        if (!$sock) {
            die("Socket ERROR: $errno/$errstr<br />\n");
        } else {
            $wssData = [
                'route'=>'getwsparam',
                'message'=>$node
            ];
            $wssData = json_encode($wssData);
            
            $key = genSecKey();
            $header = "GET /connect HTTP/1.1\r\n";
            $header.= "Host: tcdn-ws.itouchtv.cn:3800\r\n";
            $header.= "Connection: Upgrade\r\n";
            $header.= "Cache-Control: no-cache\r\n";
            $header.= "Upgrade: websocket\r\n";
            $header.= "Origin: https://www.gdtv.cn\r\n";
            $header.= "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36\r\n";
            $header.= "Sec-WebSocket-Version: 13\r\n";
            $header.= "Sec-WebSocket-Key: {$key}\r\n";
            $header.= "Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits\r\n";

            fwrite($sock,$header."\r\n");
            
            $handshake = stream_get_contents($sock);
            
            if(strstr($handshake,'Sec-Websocket-Accept'))
            {
                fwrite($sock, encode($wssData));
                $param = stream_get_contents($sock);
                $param = substr($param,4);
                $json =json_decode($param);
                $wsnode = $json->wsnode;

            }
            fclose($sock);
        }
        
        // wss 取串结束.

        // 先options告知服务器我要干嘛 // 
        // 不知道这里是否有有效时间问题，一次options成功后边都不需要提交 ？ 待测试 //
        
        $bstrURL = "https://gdtv-api.gdtv.cn/api/tv/v2/tvChannel/$id?tvChannelPk=$id&node=".base64_encode($wsnode);
        $sign = base64_encode(hash_hmac("SHA256","GET\n$bstrURL\n$ts\n","dfkcY1c3sfuw0Cii9DWjOUO3iQy2hqlDxyvDXd1oVMxwYAJSgeB6phO8eW1dfuwX",true));

        $opt_headers = [
            "access-control-request-headers: content-type,x-itouchtv-ca-key,x-itouchtv-ca-signature,x-itouchtv-ca-timestamp,x-itouchtv-client,x-itouchtv-device-id",
            "access-control-request-method: GET",
            "origin: https://www.gdtv.cn",
            "referer: https://www.gdtv.cn",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36",
            
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS");	 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,$opt_headers);
        $data = curl_exec($ch);
        curl_close($ch); 

        array_pop($headers);
        $headers[] = "x-itouchtv-ca-signature: $sign";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        $data = curl_exec($ch);
        
        curl_close($ch);
        $json = json_decode($data);
        $playURL = json_decode($json->playUrl)->hd;
        $cache->put("gdtv_".$id."_cache",$playURL);
    }
    // m3u8清单有referer校验。
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $playURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,["Referer: https://www.gdtv.cn"]);
    $data = curl_exec($ch);
    curl_close($ch);

    header("Content-Type: application/vnd.apple.mpegURL");
    header("Content-Disposition: filename=$id.m3u8");
    echo $data;

    
    function genSecKey()
    {
        return base64_encode(substr(md5(mt_rand(1,999)),0,16));
    }

    function encode($data)
    {
        // 本处代码偷懒了，仅限该解析代码使用。
        $len = strlen($data);
        $head[0] = 129; 
        $mask = array();
        for ($j = 0; $j < 4; $j ++)
        {
            $mask[] = mt_rand(1, 128);
        }
        $split = str_split(sprintf('%016b', $len), 8);
        $head[1] = 254;
        $head[2] = bindec($split[0]);
        $head[3] = bindec($split[1]);
        $head = array_merge($head, $mask);
        foreach ($head as $k => $v)
        {
            $head[$k] = chr($v);
        }
        $mask_data = '';
        for ($j = 0; $j < $len; $j ++)
        {
            $mask_data .= chr(ord($data[$j]) ^ $mask[$j % 4]);

        }
        return implode('', $head).$mask_data;

    }
    function createNewGUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    // 以下缓存类来自互联网，请确保cache目录存在以及读写权限 //
    class Cache {

        private $cache_path;
        private $cache_expire;
        public function __construct($exp_time=3600,$path="cache/"){
            $this->cache_expire=$exp_time;
            $this->cache_path=$path;
        }

        private function fileName($key){  return $this->cache_path.md5($key); }
        public function put($key, $data){

            $values = serialize($data);
            $filename = $this->fileName($key);    
            $file = fopen($filename, 'w');
            if ($file){

                fwrite($file, $values);
                fclose($file);
            }
            else return false;
        }

        public function get($key){

            $filename = $this->fileName($key);

            if (!file_exists($filename) || !is_readable($filename)){ return false; }

            if ( time() < (filemtime($filename) + $this->cache_expire) ) {

                $file = fopen($filename, "r");

                if ($file){

                    $data = fread($file, filesize($filename));
                    fclose($file);
                    return unserialize($data);
                }
                else return false;

            }
            else return false;
        }
    }




?>
