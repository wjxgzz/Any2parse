<?php
    // 清风DJ 奇奇怪怪的解析解码
    /* 花里胡哨的原JS代码
    function DeCode() {
this.substring_s = function(a, b) {
    return b > 0 ? a.substring(0, b) : null;
},
this.substring_b = function(a, b) {
    return a.length - b >= 0 && a.length >= 0 && a.length - b <= a.length ? a.substring(a.length - b, a.length) : null;
},
this.decode_b = function(a, b) {
    var c, d, e, f, g, h, i, j, k = "";
    for (c = 0; c < b.length; c++) {
        k += b.charCodeAt(c).toString(); //  O0000OO0OO00O 逐字符取CODE // O = 79  0 = 48    79484848487979487979484879
    }
	// d = 5 //d = Math.floor(k.length / 5)
	// e = 8 + 7 + 8 + 4 + 9
	// e = parseInt(k.charAt(d) + k.charAt(2 * d) + k.charAt(3 * d) + k.charAt(4 * d) + k.charAt(5 * d))
	// f = 8
	// g = 2147483647
	// h = 72115018
	// a = "a9ab"
	// k+=h = 7948484848797948797948487972115018
    for (d = Math.floor(k.length / 5), e = parseInt(k.charAt(d) + k.charAt(2 * d) + k.charAt(3 * d) + k.charAt(4 * d) + k.charAt(5 * d)), f = Math.round(b.length / 2), g = Math.pow(2, 31) - 1, h = parseInt(a.substring(a.length - 8, a.length), 16), a = a.substring(0, a.length - 8), k += h; k.length > 10;) {
        k = (parseInt(k.substring(0, 10)) + parseInt(k.substring(10, k.length))).toString();
    }
	// k = 948503
	// k = 36*948503+8 = 34146116 % 2147483647 = 34146116
	// 
    for (k = (e * k + f) % g, i = "", j = "", c = 0; c < a.length; c += 2) {
        i = parseInt(parseInt(a.substring(c, c + 2), 16) ^ Math.floor(255 * (k / g))),
        j += String.fromCharCode(i),
        k = (e * k + f) % g;
    }
    return j; // e0   你花里胡哨的就为了把 e0 加密 ？？ 
},
this.substring_c = function(a, b, c) {
    return a.length >= 0 ? a.substr(b, c) : null;
},
this.string_len = function(a) {
    return a.length;
},
this.decoded = function(a, b) {
    var h, i, j, k, l, m, n, o, p, c = b,
    d = this.string_len(c),
    e = d,
    f = new Array(),
    g = new Array();
    for (l = 1; d >= l; l++) {
        f[l] = this.substring_c(c, l - 1, 1).charCodeAt(0),
        g[e] = f[l],
        e -= 1;
    }
    for (h = "", i = a, m = this.substring_s(i, 2), i = this.substring_b(i, this.string_len(i) - 2), l = 0; l < this.string_len(i); l += 4) {
        j = this.substring_c(i, l, 4),
        "" != j && (b = this.substring_s(j, 1), k = (parseInt(this.substring_b(j, 3)) - 100) / 3, m == this.decode_b("a9ab044c634a", "O0000OO0OO00O") ? (n = 2 * parseInt(b.charCodeAt(0)), o = parseInt(f[k]), p = n - o, h += String.fromCharCode(p)) : (n = 2 * parseInt(b.charCodeAt(0)), o = parseInt(g[k]), p = n - o, h += String.fromCharCode(p)));
    }
    return h;
};
}
function playurl(pj, pj1) {
var x = new DeCode();
playurl = x.decoded(pj, pj1);
return playurl;
}

    */
    // --------------------------------------- 分割线 ------------------------------------
    // vvvdj decoded
    $bstrURL = "https://www.vvvdj.com/play/224863.html";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bstrURL);	 	 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    $data = curl_exec($ch);
    curl_close($ch);
    preg_match("/playurl=x.O000O0OO0O0OO\('(.*?)','(.*?)'\);/i",$data,$ma);
    

    $a = $ma[1];
    $b = $ma[2];
    
    $d = strlen($b);
    $e = $d;
    $f = [];
    $g = [];
    for($l = 1;$l<=$d;$l++)
    {
        $f[$l] = ord(substr($b,$l-1,1));
    }
    $g = array_reverse($f);

    $m = substr($a,0,2);
    $i = substr($a,2);
    
    for($l=0;$l<strlen($i);$l+=4)
    {
        $j = substr($i,$l,4);
        
        if($j != '')
        {
            $b = substr($j,0,1);
            $k = (intval(substr($j,1,3))-100)/3;
            
            if($m == 'e0')
            {
                $n = 2 * ord($b);
                $o = intval($f[$k]);
                $p = $n-$o;
                $h .= chr($p);
            }
            else
            {
                $n = 2 * ord($b);
                $o = intval($g[$k]);
                $p = $n - $o;
                $h.= chr($p);
            }
        } 

    }

    header("location: https:".$h);
    

?>
