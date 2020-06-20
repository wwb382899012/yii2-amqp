<?php
/**
 * GetList.php
 * @description : demo列表
 * @create date : 2020/3/6
 */

namespace frontend\controllers\mq;

use core\providers\message\services\SendMqService;
use core\providers\user\entities\UserAccountEntity;
use core\providers\user\operations\UserAccountLogOperation;
use Exception;
use core\actions\WebBaseAction;
use core\extensions\amqp\Publish;
use core\providers\user\entities\UserEntity;
use core\providers\BaseEntity;

class Transfer extends WebBaseAction
{
    public $checkParams = [

    ];


    public function run()
    {
        try {
            $price = 10;
            $userName = 'A';
            $trans = UserEntity::beginTransaction();
            $this->checkParams();
            $user = UserEntity::findOne(['user_name' => $userName, 'is_deleted' => BaseEntity::UN_DELETED]);
            if (empty($user)) {
                echo '用户A不存在';
            }
            //转账10元
            $userAccount = UserAccountEntity::findOne(['user_id' => $user->id, 'is_deleted' => BaseEntity::UN_DELETED]);
            $userAccount->account = ($userAccount->account) - $price;
            $userAccount->save();

            //日志
            $content = '用户' . $userName . '账户支出' . $price . '元';
            UserAccountLogOperation::addLog($content);

            $trans->commit();

            $messageData = ['receive_user_id' => 2, 'price' => $price];
            $service = new SendMqService();
            $res = $service->sendNormalMessage($messageData);
            if ($res) {
                echo   '生产mq消息一条'."<br />".$content;
            }


        } catch (Exception $e) {
            $trans->rollBack();
            $this->error('-1', '系统繁忙，请稍后再试:' . $e->getMessage());
        }
    }
}