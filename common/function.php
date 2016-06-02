<?php
/**
 * function.php
 * 公共函数
 * @param unknown_type $url
 */


/**
 * 设置抓取房主的代理，模拟请求
 * User-Agent Fangzhur/3.0.4(Iphone;IOS8.3;Scale/2.00)
 */
function getFzSnoopy($url,$Parameters){

	$snoopy = new Snoopy;
	$snoopy->proxy_host="58.251.132.181";
	$snoopy->proxy_port = "8888";
	// $snoopy->agent="Fangzhur/3.0.4 (iPhone; iOS 8.3; Scale/2.00)";
	$snoopy->_submit_type = "application/x-www-form-urlencoded"; //设定submit类型
	$snoopy->rawheaders['COOKIE']="AUTH_ID=169769; AUTH_MEMBER_NAME=18611088268; AUTH_MEMBER_STRING=V1dXUFJTZmpValhZawNfUUUCBVZdU19UbFZRO1sNOQNZV0YCUlNWAmJU; db_name=fangzhu; msg_rentsell=1";
	$snoopy->submit($url,$Parameters);
	return $snoopy->results;
}
/**
 *
 * 设置动态代理，防止被屏蔽。
 */
function getSnoopy($url){

	//	if(empty($url) || !isset($url)){
	//		echo 'nothing';
	//	}
	$snoopy = new Snoopy();
	//    $snoopy->cookies["PHPSESSID"] = 'fc106b1918bd522cc863f36890e6fff7'; // 伪装sessionid
// 	$snoopy->agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4"; // 伪装浏览器
	$snoopy->referer = "http://www.zjs.cn"; // 伪装来源页地址 http_referer
	$snoopy->rawheaders["Pragma"] = "no-cache"; // cache 的http头信息
	$snoopy->rawheaders["X_FORWARDED_FOR"] = "122.96.59.104"; //伪装ip118.244.149.153:80    42.121.33.160:8080   
	$snoopy_content=($snoopy->fetch($url)->results);
	return $snoopy_content;
}

/**
 *
 * 设置动态代理，防止被屏蔽。
 * 获取xml、json方式
 * @param unknown_type $url
 */
function getXmlJsonSnoopy($url){

	//	if(empty($url) || !isset($url)){
	//		echo 'nothing';
	//	}
	$snoopy = new Snoopy();
	$snoopy->cookies["PHPSESSID"] = 'fc106b1918bd522cc863f36890e6fff7'; // 伪装sessionid
	$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)"; // 伪装浏览器
	$snoopy->referer = "http://www.only4.cn"; // 伪装来源页地址 http_referer
	$snoopy->rawheaders["Pragma"] = "no-cache"; // cache 的http头信息
	$snoopy->rawheaders["X_FORWARDED_FOR"] = "118.244.149.153"; //伪装ip
	$snoopy_content=$snoopy->fetch($url)->results;
	return $snoopy_content;
}


/**

/**
 * 去除字符串中所有空格、tab、回车等
 */
function trimall($str)//删除空格
{
	$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
	return str_replace($qian,$hou,$str);
}

function gbk_to_utf8($text){
	return mb_convert_encoding($text, "UTF-8","GBK");
}

function gb2312_to_utf8($text){
	return mb_convert_encoding($text, "UTF-8","GB2312");
}


/*全角转换半角
 * @author jsx
 * 2015.6.19
 */
function SBC_DBC($info) {
	$DBC = Array(
			'０' , '１' , '２' , '３' , '４' ,
			'５' , '６' , '７' , '８' , '９' ,
			'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
			'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
			'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
			'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
			'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
			'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
			'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
			'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
			'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
			'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
			'ｙ' , 'ｚ' , '－' , '　' , '：' ,
			'．' , '，' , '／' , '％' , '＃' ,
			'！' , '＠' , '＆' , '（' , '）' ,
			'＜' , '＞' , '＂' , '＇' , '？' ,
			'［' , '］' , '｛' , '｝' , '＼' ,
			'｜' , '＋' , '＝' , '＿' , '＾' ,
			'￥' , '￣' , '｀'
	);
	$SBC = Array( // 半角
			'0', '1', '2', '3', '4',
			'5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E',
			'F', 'G', 'H', 'I', 'J',
			'K', 'L', 'M', 'N', 'O',
			'P', 'Q', 'R', 'S', 'T',
			'U', 'V', 'W', 'X', 'Y',
			'Z', 'a', 'b', 'c', 'd',
			'e', 'f', 'g', 'h', 'i',
			'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's',
			't', 'u', 'v', 'w', 'x',
			'y', 'z', '-', ' ', ':',
			'.', ',', '/', '%', '#',
			'!', '@', '&', '(', ')',
			'<', '>', '"', '\'','?',
			'[', ']', '{', '}', '\\',
			'|', '+', '=', '_', '^',
			'$', '~', '`'
	);
	return str_replace($DBC, $SBC, $info);
}


/*
 *判断一些房源是否下架，跳转到的页面
 * */
function get_jump_url($url) {
    $url = str_replace(' ','',$url);
    do {//do.while循环：先执行一次，判断后再是否循环
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $header = curl_exec($curl);
        curl_close($curl);
        preg_match('|Location:\s(.*?)\s|',$header,$tdl);
        if(strpos($header,"Location:")){
            $url=$tdl ? $tdl[1] :  null ;
        }else{
            return $url.'';
            break;
        }
    }
    while(strpos($header,"Location:"));
}































