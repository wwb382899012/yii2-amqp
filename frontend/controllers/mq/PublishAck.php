<?php
/**
 * GetList.php
 * @description : demo列表
 * @create date : 2020/3/6
 */

namespace frontend\controllers\mq;

use core\providers\message\services\SendMqService;
use Exception;
use core\actions\WebBaseAction;
use core\extensions\amqp\Publish;
use core\providers\user\entities\UserEntity;
use core\providers\BaseEntity;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishAck extends WebBaseAction
{
    public $checkParams = [
        ['page', 'default', '1'],
        ['pageSize', 'default', '20'],
        ['search', 'default', ''],
    ];


    public function run()
    {
        try {
            $conn = new AMQPStreamConnection('127.0.0.1', 5672, 'test', '123456');
            //建立通道
            $channel = $conn->channel();
            //确认投放队列，并将队列持久化
            $channel->queue_declare('change.money', false, true, false, false);
            //异步回调消息确认
            $channel->set_ack_handler(
                function (AMQPMessage $message) {
                    echo "Message acked with content " . $message->body . PHP_EOL;
                }
            );
            $channel->set_nack_handler(
                function (AMQPMessage $message) {
                    echo "Message nacked with content " . $message->body . PHP_EOL;
                }
            );

            //开启消息确认
            $channel->confirm_select();
            //建立消息，并消息持久化
            $msg = new AMQPMessage('eee', array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
            $channel->basic_publish($msg, 'money', 'web');//

            echo "发送生产消息，";
            //阻塞等待消息确认
            //$channel->wait_for_pending_acks();
            $channel->wait();
            $channel->close();
            $conn->close();

        } catch (Exception $e) {

            $this->error('-1', '系统繁忙，请稍后再试:' . $e->getMessage());
        }
    }
}