<?php
    //长效key计算
    //DateTime: 2021.12 --- 失效时间: 他什么时候改key什么时候失效
    //看潮洲

    $ts = dechex(time()+3600*24*365);
    $sign = md5("ukcgq412312212d8ag123asdfsds/czbtv2019/czggpd$ts");
    $bstrURL = "rtmp://pili-live-rtmp.czbtv.sobeylive.com/czbtv2019/czggpd?sign=$sign&t=$ts";
    echo $bstrURL."<br />";
    $sign = md5("ukcgq412312212d8ag123asdfsds/czbtv2019/czzhpd$ts");
    $bstrURL = "rtmp://pili-live-rtmp.czbtv.sobeylive.com/czbtv2019/czzhpd?sign=$sign&t=$ts";
    echo $bstrURL."<br />";
    
    //揭阳手机台
    
    $sign = md5("stjy202008050212323221saeaebasdf123/stjy2020/zhpd$ts");
    $bstrURL = "rtmp://rtmp-stjy.sobeylive.com/stjy2020/zhpd?sign=$sign&t=$ts";
    echo $bstrURL."<br />";

    
    $sign = md5("stjy202008050212323221saeaebasdf123/stjy2020/ggpd$ts");
    $bstrURL = "rtmp://rtmp-stjy.sobeylive.com/stjy2020/ggpd?sign=$sign&t=$ts";
    echo $bstrURL."<br />";
?>
