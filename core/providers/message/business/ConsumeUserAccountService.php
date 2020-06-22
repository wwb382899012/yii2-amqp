<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 14:54
 */

namespace core\providers\message\business;

use core\providers\BaseEntity;
use core\providers\user\entities\UserAccountEntity;
use core\providers\user\entities\UserEntity;
use core\providers\user\operations\UserAccountLogOperation;

class ConsumeUserAccountService
{
    public function normal($message)
    {

        var_dump($message);

    }


    public function transfer($message)
    {
        try {
            $messageArr = json_decode($message, true);
            $userId = $messageArr['receive_user_id'];
            $price = $messageArr['price'];
            $userAccount = UserAccountEntity::findOne(['user_id' => $userId, 'is_deleted' => BaseEntity::UN_DELETED]);
            if (!empty($userAccount)) {
                $userAccount->account = ($userAccount->account) + $price;
                $userAccount->save();

                $user = UserEntity::findOne(['id' => $userId]);
            }
            $content = '用户' . $user->user_name . '账户收入' . $price . '元';
            UserAccountLogOperation::addLog($content);
            echo '消费一条mq消息' . PHP_EOL . $content;
            return false;
        } catch (\Exception $e) {
            echo '到账业务处理失败:' . $e->getMessage();
        }


    }

    public function transfer2($message)
    {
        try {
            $messageArr = json_decode($message, true);
            $userId = $messageArr['receive_user_id'];
            $price = $messageArr['price'];
            $userAccount = UserAccountEntity::findOne(['user_id' => $userId, 'is_deleted' => BaseEntity::UN_DELETED]);
            if (!empty($userAccount)) {
                $userAccount->account = ($userAccount->account) + $price;
                $userAccount->save();

                $user = UserEntity::findOne(['id' => $userId]);
            }
            $content = '用户' . $user->user_name . '账户收入' . $price . '元';
            UserAccountLogOperation::addLog($content);
            echo '消费一条mq消息' . PHP_EOL . $content;
            return true;
        } catch (\Exception $e) {
            echo '到账业务处理失败:' . $e->getMessage();
        }


    }

}