<?php

namespace core\extensions\amqp;

use AMQPChannel;
use AMQPConnection;
use AMQPExchange;
use AMQPQueue;

abstract class BaseMq
{
    //rabbitMQ配置信息（默认配置）
    public $config = array(
        //'host'=>'192.168.247.133',  //host
        'host' => '127.0.0.1',
        'port' => 5672,         //端口
        'login' => 'test',  //账号
        'password' => '123456',  //密码
        'vhost' => '/'         //虚拟主机
    );

    public $exchangeName; //交换机
    public $queueName;    //队列名
    public $routeKey;     //路由键
    public $exchangeType;  //交换机类型

    public $channel;      //信道
    public $connection;      //连接
    public $exchange;     //交换机
    public $queue;        //队列

    //初始化RabbitMQ（$config数组是用来修改rabbitMQ的配置信息的）
    public function __construct($exchangeName, $queueName, $routeKey, $exchangeType = 'direct', $config = array())
    {
        $this->exchangeName = $exchangeName;
        $this->queueName = $queueName;
        $this->routeKey = $routeKey;
        $this->exchangeType = $exchangeType;
        $config = empty($config) ? \Yii::$app->params['amqp'] : $config;
        if (!empty($config)) {
            $this->setConfig($config);
        }
        $this->createConnet();
    }

    //对RabbitMQ的配置重新进行配置
    public function setConfig($config)
    {
        if (!is_array($config)) {
            throw new \Exception('config不是一个数组');
        }
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    //创建连接与信道
    public function createConnet()
    {
        //创建连接
        $this->connection = new AMQPConnection($this->config);
        if (!$this->connection->connect()) {
            throw new \Exception('RabbitMQ创建连接失败');
        }

        //创建信道
        $this->channel = new AMQPChannel($this->connection);
        //创建交换机
        $this->createExchange();
        //生产时不需要队列,故队列名为空,只有消费时需要队列名
        if (!empty($this->queueName)) {
            $this->createQueue();
        }
    }

    //创建交换机
    public function createExchange()
    {
        $this->exchange = new AMQPExchange($this->channel);
        $this->exchange->setName($this->exchangeName);
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT);
        $this->exchange->setFlags(AMQP_DURABLE);
    }

    //创建队列,绑定交换机
    public function createQueue()
    {
        $this->queue = new AMQPQueue($this->channel);
        $this->queue->setName($this->queueName);
        $this->queue->setFlags(AMQP_DURABLE);
        $this->queue->bind($this->exchangeName, $this->routeKey);
    }

    public function dealMq($flag)
    {
        if ($flag) {
            $this->queue->consume(function ($envelope) {
                $this->getMsg($envelope, $this->queue);
            }, AMQP_AUTOACK);//自动ACK应答
        } else {
            $this->queue->consume(function ($envelope) {
                $this->processMessage($envelope, $this->queue);
            });
        }
    }

    public function getMsg($envelope, $queue)
    {
        $msg = $envelope->getBody();
        $this->doProcess($msg);
    }

    public function processMessage($envelope, $queue)
    {
        $msg = $envelope->getBody();
        $res = $this->doProcess($msg);
        if ($res) {
            $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
        } else {
            //$queue->nack($envelope->getDeliveryTag(), AMQP_REQUEUE); //重新放回队列,nack 方法将消息放回队列后, 队列会将消息再次推送给消费者. 如果此时队列只有一个消费者, 将会造成死循环.
        }

    }

    //处理消息的真正函数，在消费者里使用
    abstract public function doProcess($msg);

    /** 生产消息
     * @param $message  string or Array
     * @return mixed
     */
    public function sendMessage($message, $durable = false)
    {
        if (!is_string($message) && is_array($message)) {
            $message = json_encode($message);
        }
        if ($durable) {
            $res = $this->exchange->publish($message, $this->routeKey, AMQP_NOPARAM, ['delivery_mode' => 2]);
        } else {
            $res = $this->exchange->publish($message, $this->routeKey);
        }
        return $res;
    }


    //关闭连接
    public function closeConnect()
    {
        $this->channel->close();
        $this->connection->disconnect();
    }
}