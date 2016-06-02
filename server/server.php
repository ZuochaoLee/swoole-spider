<?php 
include 'Lianjia.class.php';
include 'Wiwj.class.php';
/**
 * 创建Server对象
 * 监听 127.0.0.1:9501端口
 */
 
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'reactor_num' => 4,
    'worker_num' => 8,    //worker process num
    'backlog' => 128,   //listen backlog
    'max_request' => 50,
    'dispatch_mode'=>3,
    'buffer_output_size' => 32 * 1024 *1024, //必须为数字
    'open_eof_split' => true,
    'package_eof' => "\r\n",
));

/**
 * 监听连接进入事件
 */
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

/**
 * 监听数据发送事件
 * 接受客户端数据，调用相应抓取规则，处理，返回数据到客户端
 */
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    echo $data."\n";
    $info=json_decode($data,1);
    switch ($info['source']){
        case 1:
           $lianjia=new Lianjia();
           $res= $lianjia->house_list($info['url']);
           break;
        case 4:
           $wiwj=new Wiwj();
           $res= $wiwj->house_list($info['url']);
           break;
        default:
           $res= array('status'=>-1,'info'=>"错误！！");
           break;
    }
    $serv->send($fd, json_encode($res)."\r\n");
    echo $serv->worker_id."\n";
});

/**
 * 监听连接关闭事件
 */
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();
?>