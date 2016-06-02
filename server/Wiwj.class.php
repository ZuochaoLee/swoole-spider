<?php
/**
 * Wiwj.class.php
 *我爱我家地产二手房抓取规则
 *@author jsx
 *@version 1
 *@since 2015-6-19
 *说明：我爱我家源码中有乱码，不能直接用正则匹配，先使用explode函数拆分
 */
header("Content-type: text/html; charset=utf-8");
ini_set("memory_limit","4000M");
ini_set('max_execution_time', '0');
//include_once '../common/function.php';

class Wiwj
{	
	
	
    //设置头，被屏蔽了
    private $opts = array(
        'http'=>array(
            'method'=>"GET",
            'header'=>"User-Agent: Mozilla/5.0\n"
        )
    );
	/*
	 * 获取列表页
	*/
	public function house_list($url){
		$html = file_get_contents($url);
		$body = explode('<ul class="list-body">', $html);
		preg_match("/[\x{0000}-\x{ffff}]*?<\/section>/u", $body[1], $list);
		
		preg_match_all("/<a\s*href=\"(\/exchange[\/\w]+)\"\s*target=\"_blank\">/u", $list[0], $hrefs);
		$house_info = array();
		
		foreach($hrefs[1] as $k=>$v){
			if(($k & 1) == 0)
				$house_info[$k]['source_url'] = 'http://bj.5i5j.com'.$v;
			    $house_info[$k]['source'] = 4;
		}
		$house_info = array_merge($house_info);
		return $house_info;
	}
		
	/*
	 * 获取详情
	*/
	public function house_detail($source_url){
		$context = stream_context_create($this->opts);
			
		$html = file_get_contents($source_url,false,$context);
		$house_info['source_url'] = $source_url;
		$house_info['source'] = 4;
		$ul = explode('<body>', $html);
		// 			print_r($ul);die;
		//标题
		$left = explode('(', $ul[0]);
		$right = explode(')', $left[1]);
		$house_info['house_title'] = $right[0];
		//获取包含房源信息的网页源码
		preg_match("/[\x{0000}-\x{ffff}]*?<\/html>/u", $ul[1], $detail);
		$html = $detail[0];
		preg_match("/<ul\s*class=\"house\-info\">[\x{0000}-\x{ffff}]*?mr\-tb/u", $html, $info);
		$info = trimall(strip_tags($info[0]));
		
		//价钱
		preg_match("/(\d+\.?\d*)万元/", $info, $price);
		$house_info['house_price'] = $price[1];
			
		//面积
		preg_match("/(\d+\.?\d*)平米/", $info, $totalarea);
		$house_info['house_totalarea'] = $totalarea[1];
			
		//朝向
		preg_match("/朝向：(.*?)楼层/u", $info, $toward);
		$house_info['house_toward'] = $toward[1];
			
		//室厅卫
		preg_match("/(\d+)室(\d+)厅(\d+)卫/", $info, $rht);
		$house_info['house_room'] = $rht[1];
		$house_info['house_hall'] = $rht[2];
		$house_info['house_toilet'] = $rht[3];
			
		//楼层
		preg_match("/(中|上|下)部\/(\d+)层/", $info, $floor);
		$t = array("上"=>"高", "中"=>"中", "下"=>"低");
		$house_info['house_floor']=$t[$floor[1]];
		$house_info['house_topfloor']=$floor[2];
			
		preg_match("/(\d+?)年/", $info, $year);
		$house_info['house_built_year']=$year[1];
			
		preg_match("/小区：(.*?)\(/u", $info, $borough);
		$house_info['borough_name']=$borough[1];
			
		preg_match("/<ul\s*class=\"pic\-list\"[\x{0000}-\x{ffff}]*?<\/ul>/u", $html, $pic);
		preg_match_all("/lazyload=\"(.+?)\"/u", $pic[0], $jpgs);

		$house_info['house_pic_layout'] = $jpgs[1][0];
			
		for($j=1; $j<count($jpgs[1]); $j++){
			$ppp[] = $jpgs[1][$j];
		}
		$house_info['house_pic_unit'] = implode("|", $ppp);
			
		//城区和商圈
		preg_match("/<div\s*class=\"xq\-intro\-info\">[\x{0000}-\x{ffff}]*?<\/ul>/u", $html, $city);
		preg_match_all("/<li[\x{0000}-\x{ffff}]*?li>/u", $city[0], $c);
		$c = str_replace('所在版块：', '', trimall(strip_tags($c[0][2])));
		$cc = explode('-', $c);
			
		$house_info['cityarea_id'] = $cc[0];
		$house_info['cityarea2_id'] = $cc[1];
			
		//联系人
		preg_match("/<dl\s*class=\"house\-broker\-info\">[\x{0000}-\x{ffff}]*?<\/dd>/u", $html, $broker);
		preg_match_all("/<p[\x{0000}-\x{ffff}]*?p>/u", $broker[0], $connect);
		$house_info['owner_name'] = strip_tags($connect[0][0]);
		$house_info['owner_phone'] = strip_tags($connect[0][1]);
			
		//描述，取第一个的
		preg_match("/<div\s*class=\"new\-broker\-3[\x{0000}-\x{ffff}]*?<\/dd>/u", $html, $desc);
		$house_info['house_desc'] = trimall(HTMLSpecialChars(strip_tags($desc[0])));
		$house_info['source'] = 4;
		$house_info['company_name'] = '我爱我家';
		$house_info['created'] = time();
		$house_info['updated'] = time();
		return $house_info;
	}
}