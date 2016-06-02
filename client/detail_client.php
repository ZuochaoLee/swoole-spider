<?php
        
        $client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        
        $client->set(array(
            'open_eof_split' => true,
            'package_eof' => "\r\n",
        ));
        
        /**
        * 注册连接成功回调
        * 负责发布种子
        */
        $client->on("connect", function($cli) {
            $redis=new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->auth('zhuge1116');
            $redis->select(2);
            swoole_timer_tick(200000, function ($timer_id) {
                while ($redis->lSize('url')){
                    $data=array();
                    $data['source_url']=$redis->rPop('url');
                    $data['source']=$redis->get(md5($data['source_url']));
                    $cli->send(json_encode($data)."\r\n");
                }
            });
        });
    
        /**
        * 注册数据接收回调
        * 对服务器返回数据入库处理
        */
        $client->on("receive", function($cli, $data){
            //echo $data."\n"; 
            $m = new MongoClient();
            $db = $m->spider;       //选择一个数据库
            $collection = $db->house_sell_gov;//选择一个集合
            $document=json_decode($data);
            $collection->insert($document);
        });
    
        /**
        * 注册连接失败回调
        * 终端中断监控
        */
        $client->on("error", function($cli){
            echo "Connect failed\n";
        });
    
        /**
        * 注册连接关闭回调
        */
        $client->on("close", function($cli){
            echo "Connection close\n";
        });
    
        //发起连接
        $client->connect('127.0.0.1', 9502,0.1);

