<?php 
require __DIR__ . '/../../../vendor/autoload.php';

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

$exchange = 'test_exchange';
$queue = 'test_queue';

// 3、声明一个交换器，并且设置相关属性
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

// 4、声明一个队列, 并且设置相关属性
$channel->queue_declare($queue, false, true, false, false);

// 5、通过路由键将交换器和队列绑定起来
$channel->queue_bind($queue, $exchange);

function process_message($message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }
}

// 6、消费消息，并且设置回调函数为 process_message
$channel->basic_consume($queue, 'consumer_tag', false, false, false, false, 'process_message');

// 7、注册终止函数，关闭通道，关闭连接
function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}
register_shutdown_function('shutdown', $channel, $connection);

// 8、一直阻塞消费数据
while ($channel ->is_consuming()) {
    $channel->wait();
}