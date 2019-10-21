<?php 
require __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

// todo 换成自己的配置
$connection = new AMQPStreamConnection('192.168.33.1', 5672, 'zhangcs', 'zhangcs', '/');
$channel = $connection->channel();

// 通过队列属性设置队列的过期时间为10s, 然后在管理页面查看10s之后队列是否消失
$arguments = new AMQPTable();
$arguments->set("x-expires", 10000);

$queueName = 'test_queue_ttl';
$channel->queue_declare($queueName, false, true, false, false, false, $arguments);

$channel->close();
$connection->close();