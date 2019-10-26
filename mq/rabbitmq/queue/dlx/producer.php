<?php 
require __DIR__ . '/../../../../vendor/autoload.php';

use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 死信队列测试
 * 1、创建两个交换器 exchange.normal 和 exchange.dlx, 分别绑定两个队列 queue.normal 和 queue.dlx
 * 2、把 queue.normal 队列里面的消息配置过期时间，然后通过 x-dead-letter-exchange 指定死信交换器为 exchange.dlx
 * 3、发送消息到 queue.normal 中，消息过期之后流入 exchange.dlx，然后路由到 queue.dlx 队列中，进行消费
 */

// todo 更改配置
$connection = new AMQPStreamConnection('192.168.33.1', 5672, 'zhangcs', 'zhangcs', '/');

$channel = $connection->channel();

$channel->exchange_declare('exchange.dlx', AMQPExchangeType::DIRECT, false, true);
$channel->exchange_declare('exchange.normal', AMQPExchangeType::FANOUT, false, true);
$args = new AMQPTable();
// 消息过期方式：设置 queue.normal 队列中的消息10s之后过期
$args->set('x-message-ttl', 10000);
// 设置队列最大长度方式： x-max-length 
//$args->set('x-max-length', 1);
$args->set('x-dead-letter-exchange', 'exchange.dlx');
$args->set('x-dead-letter-routing-key', 'routingkey');
$channel->queue_declare('queue.normal', false, true, false, false, false, $args);
$channel->queue_declare('queue.dlx', false, true, false, false);

$channel->queue_bind('queue.normal', 'exchange.normal');
$channel->queue_bind('queue.dlx', 'exchange.dlx', 'routingkey');
$message = new AMQPMessage('Hello DLX Message');
$channel->basic_publish($message, 'exchange.normal', 'rk');

$channel->close();
$connection->close();
