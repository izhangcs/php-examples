<?php 
require __DIR__ . '/../../../../vendor/autoload.php';

use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 延迟队列
 * 1、创建两个交换器 exchange.order 和 exchange.delay, 分别绑定两个队列 queue.order 和 queue.delay
 * 2、把 queue.delay 队列里面的消息配置过期时间，一般订单是20分钟，这里设置成10秒，然后通过 x-dead-letter-exchange 指定死信交换器为 exchange.delay
 * 3、发送消息到 queue.order 中，消息过期之后流入 exchange.delay，然后路由到 queue.delay 队列中，然后检查订单状态，如果未支付，则进行取消操作
 */

// todo 更改配置
$connection = new AMQPStreamConnection('192.168.33.1', 5672, 'zhangcs', 'zhangcs', '/');

$channel = $connection->channel();

$channel->exchange_declare('exchange.order', AMQPExchangeType::DIRECT, false, true);
$channel->exchange_declare('exchange.delay', AMQPExchangeType::DIRECT, false, true);
$args = new AMQPTable();
// 消息过期方式：设置 queue.order 队列中的消息10s之后过期
$args->set('x-message-ttl', 10000);
$args->set('x-dead-letter-exchange', 'exchange.delay');
$args->set('x-dead-letter-routing-key', 'routingkey.delay');
$channel->queue_declare('queue.order', false, true, false, false, false, $args);
$channel->queue_declare('queue.delay', false, true, false, false);

$channel->queue_bind('queue.order', 'exchange.order', 'routingkey.cancel.order');
$channel->queue_bind('queue.delay', 'exchange.delay', 'routingkey.delay');
$message = new AMQPMessage('F20190413180108970');
$channel->basic_publish($message, 'exchange.order', 'routingkey.cancel.order');

$channel->close();
$connection->close();
