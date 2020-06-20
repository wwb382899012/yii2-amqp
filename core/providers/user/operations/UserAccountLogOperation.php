<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 15:35
 */

namespace core\providers\user\operations;

use core\providers\BaseOperation;
use core\providers\user\entities\UserAccountLogEntity;

class UserAccountLogOperation extends BaseOperation
{

    public static function addLog($content)
    {
        $entity = new UserAccountLogEntity();
        $entity->content = $content;
        $entity->save();

    }

}