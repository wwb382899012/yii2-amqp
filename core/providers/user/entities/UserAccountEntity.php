<?php
/**
 * Created by PhpStorm.
 * User: wenwb
 * Date: 2019/7/24
 * Time: 10:50
 */

namespace core\providers\user\entities;

use core\providers\BaseEntity;

class UserAccountEntity extends BaseEntity
{
    public static function tableName()
    {
        return 'c_user_account';
    }

}