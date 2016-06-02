<?php
        include 'Seed.class.php';
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
            $urls=array(1);
            foreach ($urls as $v){
                $seed=new Seed();
                $seed->run($cli,$v);
            }
        });
    
        /**
        * 注册数据接收回调
        * 对服务器返回数据做入队处理
        */
        $client->on("receive", function($cli, $data){
            echo $data;
            $redis=new Redis();
            $redis->connect('123.56.103.209', 6379);
            $redis->auth('zhuge1116');
            $redis->select(2);
            $data=json_decode($data,1);
            foreach ($data as $v){
                if(!$redis->exists(md5($v['source_url']))){
                    $redis->lPush('url', $v['source_url']);
                    $redis->set(md5($v['source_url']), $v['source']);
                }
            }    
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
        $client->connect('123.57.76.91',8282,0.1);

