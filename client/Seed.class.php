<?php
class Seed{
    public function run($cli,$source){
        switch ($source){
            case 1:
                $URL="http://bj.lianjia.com/ershoufang/";
                $html = file_get_contents($URL);
                preg_match('/区域：([\x{0000}-\x{ffff}]+?)筛选/u',$html,$message);
                preg_match_all('/option\-list([\x{0000}-\x{ffff}]+?)<\/dl>/u',$message[1],$condition);
                $condition = $condition[1];
                preg_match('/不限([\x{0000}-\x{ffff}]+?)<\/div>/u',$condition[0],$dis);
                preg_match_all('/<a\shref=\"\/ershoufang\/([\x{0000}-\x{ffff}]+?)\//u',$dis[1],$dis);
                foreach ($dis[1] as $v){
                    $url=$URL.$v;
                    $html = file_get_contents($url);
                    preg_match('/区域：([\x{0000}-\x{ffff}]+?)筛选/u',$html,$message);
                    preg_match_all('/sub\-option\-list([\x{0000}-\x{ffff}]+?)<\/dd>/u',$message[1],$condition);
                    $condition = $condition[1];
                    preg_match('/不限([\x{0000}-\x{ffff}]+?)<\/div>/u',$condition[0],$sdis);
                    preg_match_all('/<a\shref=\"\/ershoufang\/([\x{0000}-\x{ffff}]+?)\//u',$sdis[1],$sdis);
                    foreach ($sdis[1] as $vv){
                        $surl=$URL.$vv;
                        $html = file_get_contents($surl);
                        preg_match('/totalPage\":(\d+)/u',$html,$page);
                        $page=$page[1];
                        if(!empty($page)&&$page!=""){
                            for($i=1;$i<=$page;$i++){
                                $urlarr=array();
                                $urlarr['source']=$source;
                                $urlarr['url']=$surl."/pg".$i."/";
                                $this->send($cli, $urlarr);
                            }
                        }
                    }
                }
                break;
            case 4:
                
                break;
            default:
                break;
        }
    }
    protected function send($cli,$urlarr){
        $cli->send(json_encode($urlarr)."\r\n");
    }
}
