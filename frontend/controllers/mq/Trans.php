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

class Trans extends WebBaseAction
{
    public $checkParams = [

    ];


    public function run()
    {
        try {
            $messageData = ['user_id' => 2, 'price' => '22'];
            $service = new SendMqService();
            $res = $service->sendTransMessage($messageData);
            if ($res) {
                echo '事物消息已入列，消息内容:' . json_encode($messageData);
            }
        } catch (Exception $e) {
            $this->error('-1', '系统繁忙，请稍后再试:' . $e->getMessage());
        }
    }
}