<?php
    // BRTV
    // BRTV.php?id=bjws

    $ids = [
        'bjws'=>'573ib1kp5nk92irinpumbo9krlb', //北京卫视
        'bjwy'=>'54db6gi5vfj8r8q1e6r89imd64s', //BRTV文艺
        'bjkj'=>'53bn9rlalq08lmb8nf8iadoph0b', //BRTV科教
        'bjys'=>'50mqo8t4n4e8gtarqr3orj9l93v', //BRTV影视
        'bjcj'=>'50e335k9dq488lb7jo44olp71f5', //BRTV财经
        'bjsh'=>'50j015rjrei9vmp3h8upblr41jf', //BRTV i生活
        'bjqn'=>'53grctge7jb8aeamggnot6fve1o', //BRTV青年
        'bjxw'=>'53gpt1ephlp86eor6ahtkg5b2hf', //BRTV新闻
        'kkdh'=>'55skfjq618b9kcq9tfjr5qllb7r', //卡酷少儿
    ];

    $id = strtolower(isset($_GET['id'])?$_GET['id']:'bjws');
    $id = $ids[$id];
    $ts = time();
    $salt = 'shi!@#$%^&*[xian!@#]*';
    $apiURL = "https://app.api.btime.com/video/play?";
    $params = 'browse_mode=1&carrier=中国电信&channel=btimeapp&gid='.$id.'&id='.$id.'&location_citycode=local_440000&manuscript=0&net=WIFI&os=AND&os_type=Android&os_ver=23&protocol=2&push_id=f8f7f0cbef2001e7e5378f45b0c3162b&push_switch=1&sid=&src=lx_android&timestamp='.$ts.'&token=95d8929924a34879151ad50ddce3c3eb&type_id=&ver=70106';	
    $sign = substr(md5($params.$salt),3,7);
    $apiURL = $apiURL.$params.'&sign='.$sign;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: okhttp/3.9.1']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    $data = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($data);
    $playURL = $json->data->video_stream[0]->stream_url;
    header('location:'.$playURL);

?>
