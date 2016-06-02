<?php
/**
 * Linajia.class.php
 *链家地产二手房抓取规则
 *@author zz(zhanzhan02@163.com)
 *@version 1
 *@since 2015-04-24
 */
header("Content-type: text/html; charset=utf-8");
ini_set("memory_limit","8000M");
ini_set('max_execution_time', '0');
include '../common/function.php';
Class Lianjia{
	
	/*
	 * 获取列表页
	*/
	public function house_list($url){
		$html=file_get_contents($url);
		preg_match("/<ul\s*id=\"house-lst[\x{0000}-\x{ffff}]*?<\/ul>/u", $html, $out);
		preg_match_all("/data\-id=\"(\w*?)\"/", $out[0], $ids);
		//preg_match_all("/title=\"[\x{0000}-\x{ffff}]*?\"/u", $out[0], $titles);
		
		foreach ($ids[1] as $k=>$v){
			$house_info[$k]['source_url'] = "http://bj.lianjia.com/ershoufang/".$v.".shtml";
			$house_info[$k]['source'] = 1;
			//$this->house_info[$k]['house_title'] = str_replace(array('title=', '"'), '', $titles[0][$k]);			
		}
		return $house_info;
	}
		
	/*
	 * 获取详情
	*/
	public function house_detail($source_url){
		$html = file_get_contents($source_url);
		$house_info['source_url'] = $source_url;
		$house_info['source'] = 1;
		preg_match("/class\=\"agent\-del\sleft\"><a\shref\=\"([\x{0000}-\x{ffff}]+?)\"\sclass=\"laishanghai\"/u",$html,$dianpuurl);
		
		$dianpuhtml = file_get_contents($dianpuurl[1]);
		
		preg_match("/<title>[\x{0000}-\x{ffff}]+?认证服务电话(\d{11})-链家网<\/title>/u",$dianpuhtml,$mphone);
		
		$house_info['owner_phone'] = $mphone[1];
// 		<title>刘曜铭房屋交易经纪人店铺_刘曜铭用户评价信息以及链家官方认证服务电话13370123768-链家网</title>
		$house_info['company'] = "链家官网";
		//标题
		preg_match("/<h1\s*class=\"title\-box\s*left([\x{0000}-\x{ffff}]+?)<\/h1>/u", $html, $title);
		$title = strip_tags($title[0]);
		$title = str_replace(array("\t", "\r", " "), "", $title);
		$title = SBC_DBC($title);
		$house_info['house_title'] =$title;
		preg_match("/<div\s*class=\"info\-box\s*left([\x{0000}-\x{ffff}]+?)<\/div>/u", $html, $detail);
		
		$info = strip_tags($detail[0]);
		$info = str_replace(array("\t", "\r", " "), "", $info);
		$info = SBC_DBC($info);
//		dump($this->house_info);die;
		//价格
		preg_match("/(\d+\.?\d*)万/", $info, $price);
		$house_info['house_price']=$price[1];

		//总面积
		preg_match("/(\d+\.?\d*)㎡/", $info, $totalarea);
		$house_info['house_totalarea']=$totalarea[1];
		
		preg_match("/(\d+?)室/", $info, $room);
		preg_match("/(\d+?)厅/", $info, $hall);
		//室
		$house_info['house_room']=$room[1];
		//厅
		$house_info['house_hall']=$hall[1];
		
		//朝向
		preg_match("/朝向:([\x{0000}-\x{ffff}]+?)楼层/u", $info, $toward);
		$house_info['house_toward']=$toward[1];
		
		//楼层
		preg_match("/(高|中|低)楼层/", $info, $floor);
		preg_match("/共(\d+?)层/", $info, $topfloor);
		$house_info['house_floor']=$floor[1];
		$house_info['house_topfloor']=$topfloor[1];
		
		//建造年份
		preg_match("/(\d+)年/", $info, $year);
		$house_info['house_built_year']=$year[1];
		
		preg_match("/小区:([\x{0000}-\x{ffff}]+?)\(([\x{0000}-\x{ffff}]+?)\)/u", $info, $cb);
		$ccc = explode('&nbsp;', $cb[2]);
		$house_info['borough_name']=$cb[1];
		$house_info['cityarea2_id'] =$ccc[1];
		$house_info['cityarea_id'] =$ccc[0];
		preg_match("/<span\s*class=\"ft-num[\x{0000}-\x{ffff}]*?<\/span>/u", $html, $contact);
		$contact = strip_tags($contact[0]);
		$house_info['service_phone'] = str_replace("转", ",", $contact);
		
		preg_match("/<div\s*class=\"p\-del\s*right\">[\x{0000}-\x{ffff}]*?<\/div>/u", $html, $jjr);
		preg_match("/<p[\x{0000}-\x{ffff}]*?p>/u", $jjr[0], $name);
		$house_info['owner_name'] = trimall(strip_tags($name[0]));
		
		preg_match("/<div\s*id=\"detail-album[\x{0000}-\x{ffff}]*?<div\s*id=\"view\-top\-people/u", $html, $p_tags);
		preg_match_all("/original=\"(\S*?)\"/", $p_tags[0], $ps);
		$house_info['house_pic_layout'] = $ps[1][0];
		unset($ps[1][0]);
		$pics = array_merge($ps[1]);
		$pics = array_unique($pics);
		$house_info['house_pic_unit']= implode("|", $pics);
		
		return $house_info;
	}
}