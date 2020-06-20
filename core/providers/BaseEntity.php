<?php
/**
 * BaseEntity.php
 *
 */

namespace core\providers;

use core\extensions\common\Pager;
use core\extensions\utils\DateTimeUtil;
use core\extensions\utils\StringUtil;
use yii\db\ActiveRecord;

class BaseEntity extends ActiveRecord
{
    const DELETED = 1;
    const UN_DELETED = 0;

    public function beforeSave($insert)
    {
        $time = DateTimeUtil::now();
        if ($this->isNewRecord) {
            $this->created_on = !empty($this->created_on) ? $this->created_on : $time;
            $this->created_by = !empty($this->created_by) ? $this->created_by : '';
        }
        $this->modified_by = !empty($this->modified_by) ? $this->modified_by : '';
        $this->modified_on = !empty($this->modified_on) ? $this->modified_on : $time;

        return parent::beforeSave($insert);
    }

    /**
     * 获取一条记录，以ActiveRecord数组返回
     *
     * @param string|array $select
     * @param string|array $condition
     * @param array $params
     * @param string $option
     *
     * @return static
     */
    public static function validOne($select = '*', $condition = '', $params = [], $option = null)
    {
        if (is_array($condition) && !isset($condition['is_deleted'])) {
            $condition['is_deleted'] = self::UN_DELETED;
        }
        /** @var static $result */
        $result = static::getQuery($select, $condition, $params, $option)->one();
        return $result;
    }

    /**
     * 获取对应条件下的查询AQ对象
     *
     * @param string $select
     * @param string $condition
     * @param array $params
     * @param null $option
     * @param Pager|null $pager
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getQuery($select = '*', $condition = '', $params = [], $option = null, Pager $pager = null)
    {
        $cmd = static::find()->select($select, $option)->where($condition, $params);

        if ($pager && $pager instanceof Pager) {
            $cmd->offset($pager->getPageOffset())->limit($pager->getPageSize());
        }

        return $cmd;
    }

    /**单表查找数据记录
     * @param string $select
     * @param string $condition
     * @param array $params
     * @param bool $isReturnList
     * @param bool $asArray
     * @return array|null|\yii\db\ActiveQuery|ActiveRecord|ActiveRecord[]
     */
    public static function getTableData($select = '*', $condition = '', $params = [], $isReturnList = true, $asArray = true)
    {
        $cmd = static::find()->select($select)->where($condition, $params)->asArray($asArray);

        if ($isReturnList) {
            $cmd = $cmd->all();
        } else {
            $cmd = $cmd->one();
        }

        return $cmd;
    }

    /**单表查找数据记录 分页
     * @param string $select
     * @param string $condition
     * @param array $params
     * @param Pager|null $pager
     * @param bool $asArray
     * @return array|\yii\db\ActiveQuery|ActiveRecord[]
     * @throws \Exception
     */
    public static function getTablePage($select = '*', $condition = '', $params = [], Pager $pager = null, $asArray = true)
    {
        $cmd = static::find()->select($select)->where($condition, $params);

        if ($pager && $pager instanceof Pager) {
            $cmd->offset($pager->getPageOffset())->limit($pager->getPageSize());
        }
        $cmd = $cmd->asArray($asArray)->all();

        return $cmd;
    }

    /**
     * 只使用使用 uuid 作为主键的实体，且需要实现生成 uuid 比较有局限性
     * @param $data
     * @param $uniqueKeys
     * @param string $primaryKey
     * @return bool|mixed
     */
    public function saveEntity($data, $uniqueKeys, $primaryKey = 'id')
    {
        if (is_string($uniqueKeys)) {
            $uniqueKeys = explode(',', $uniqueKeys);
        }

        $where = [];
        foreach ($uniqueKeys as $uniqueKey) {
            if (isset($data[$uniqueKey])) {
                $where[$uniqueKey] = $data[$uniqueKey];
            }
        }

        if (empty($where)) {
            return false;
        }

        $entity = self::find()->where(array_merge(['is_deleted' => 0], $where))->one();

        if (empty($entity)) {
            $entity = new static();
            $entity->$primaryKey = !empty($data[$primaryKey]) ? $data[$primaryKey] : StringUtil::uuid();
        }

        foreach ($entity->attributes() as $attribute) {
            if (isset($data[$attribute])) {
                $entity->$attribute = $data[$attribute];
            }
        }

        $entity->save();

        return $entity->$primaryKey;
    }

    public function insertEntityByArr($data, $primaryKey = 'id')
    {
        $entity = new static();
        foreach ($entity->attributes() as $attribute) {
            if (isset($data[$attribute])) {
                $entity->$attribute = $data[$attribute];
            }
        }

        $entity->save();

        return $entity->attributes[$primaryKey];
    }

    /**
     * 获取当前公用的事务
     * @return \yii\db\Transaction
     */
    public static function beginTransaction()
    {
        return static::getDb()->beginTransaction();
    }





}

?>