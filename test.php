<?php

 //连接本地的 Redis 服务
 $redis = new Redis();
 $redis->connect('hyperf-redis');

 echo "Connection to server successfully";
 //查看服务是否运行
echo "Server is running: " . $redis->ping();

