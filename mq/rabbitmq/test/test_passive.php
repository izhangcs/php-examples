<?php 
require __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 测试 RabbitMQ 中 passive 参数的作用
 * 
 * 结论：当 passive 设置为true是，如果对应的交换器不存在，则会抛出异常
 */

// todo 换成自己的配置
$host = '192.168.33.1';
$port = 5672;
$username = 'zhangcs';
$password = 'zhangcs';
$vhost = '/';

try {
    $connnection = new AMQPStreamConnection($host, $port, $username, $password, $vhost);
    $channel = $connnection->channel();
    $channel->exchange_declare('exchange1', AMQPExchangeType::FANOUT, true, true, false);
    $channel->queue_declare('queue1', true, true, false, false);
} catch(\Exception $e) {
    echo $e->getMessage();
}


