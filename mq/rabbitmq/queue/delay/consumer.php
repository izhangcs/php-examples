<?php 
require __DIR__ . '/../../../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 延迟队列测试
 * 消费死信队列 queue.delay
 */

// todo 更改配置
$connection = new AMQPStreamConnection('192.168.33.1', 5672, 'zhangcs', 'zhangcs', '/');
$channel = $connection->channel();

$channel->exchange_declare('exchange.delay', AMQPExchangeType::DIRECT, false, true);
$channel->queue_declare('queue.delay', false, true, false, false);

$channel->queue_bind('queue.delay', 'exchange.delay', 'routingkey.delay');

function process_message($message)
{
    echo "开始处理订单，订单号:" . $message->body . PHP_EOL;
    // todo 获取订单的状态，如果未支付，则进行取消订单操作
    echo "获取订单的状态，如果未支付，则进行取消订单操作" . PHP_EOL;
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
}

$channel->basic_consume('queue.delay', 'cancelOrder', false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}
register_shutdown_function('shutdown', $channel, $connection);

while ($channel ->is_consuming()) {
    $channel->wait();
}
