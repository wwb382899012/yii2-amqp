<?php
/**
 * BaseOperation.php
 */

namespace core\providers;

class BaseOperation
{
    public static function logicDelete(BaseEntity $model, $where)
    {
        return $model::updateAll(['is_deleted' => BaseEntity::DELETED], $where);
    }
}