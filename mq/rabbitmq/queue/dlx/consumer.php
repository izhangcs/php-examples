<?php 
require __DIR__ . '/../../../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * 死信队列测试
 * 消费死信队列 queue.dlx
 */

// todo 更改配置
$connection = new AMQPStreamConnection('192.168.33.1', 5672, 'zhangcs', 'zhangcs', '/');
$channel = $connection->channel();

$channel->exchange_declare('exchange.dlx', AMQPExchangeType::DIRECT, false, true);
$channel->queue_declare('queue.dlx', false, true, false, false);

$channel->queue_bind('queue.dlx', 'exchange.dlx', 'routingkey');

function process_message($message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
}

$channel->basic_consume('queue.dlx', 'consumer_tag', false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}
register_shutdown_function('shutdown', $channel, $connection);

while ($channel ->is_consuming()) {
    $channel->wait();
}
