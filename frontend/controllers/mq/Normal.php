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

class Normal extends WebBaseAction
{
    public $checkParams = [
        ['page', 'default', '1'],
        ['pageSize', 'default', '20'],
        ['search', 'default', ''],
    ];


    public function run()
    {
        try {
            $trans = UserEntity::beginTransaction();
            $this->checkParams();
            $user = UserEntity::findOne(['id' => '1', 'is_deleted' => BaseEntity::UN_DELETED]);
            $user->user_name = 'A';
            $user->save();
            $trans->commit();

            $messageData = ['user_id' => $user->id, 'price' => '20'];
            $service = new SendMqService();
            $res = $service->sendNormalMessage($messageData);
            if ($res) {
                echo 'success';
            }


        } catch (Exception $e) {
            $trans->rollBack();
            $this->error('-1', '系统繁忙，请稍后再试:' . $e->getMessage());
        }
    }
}