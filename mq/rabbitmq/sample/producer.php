<?php 
require __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

// todo 换成自己的配置
$host = '192.168.33.1';
$port = 5672;
$username = 'zhangcs';
$password = 'zhangcs';
$vhost = '/';

// 1、连接到 RabbitMQ Broker，建立一个连接
$connection = new AMQPStreamConnection($host, $port, $username, $password, $vhost);

// 2、开启一个通道
$channel = $connection->channel();

$exchange = 'test_excahnge';
$queue = 'test_queue';
// 3、声明一个交换器，并且设置相关属性
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

// 4、声明一个队列, 并且设置相关属性
$channel->queue_declare($queue, false, true, false, false);

// 5、通过路由键将交换器和队列绑定起来
$channel->queue_bind($queue, $exchange);

$body = $argv[1] ?? 'Hello RabbitMQ';

// 6、初始化消息，并且持久化消息
$message = new AMQPMessage($body, [
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
]);

// 7、将消息发送到 RabbitMQ Broker
$channel->basic_publish($message, $exchange);

// 8、关闭通道
$channel->close();
// 9、关闭连接
$connection->close();

