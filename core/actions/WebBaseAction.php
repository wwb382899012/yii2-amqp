<?php
/**
 * ApiBaseAction.php
 *
 * @description : action 的公共方法
 *
 * @author : liaobw <liaobw@mingyuanyun.com>
 * @create date : 2019/7/23
 */

namespace core\actions;

use core\extensions\traits\ResponseTrait;
use yii\base\Action;

class WebBaseAction extends Action
{
    /**
     * @var bool 是否抛出未登录错误
     */
    public $isShowLoinErr = true;

    protected $checkParams = [];

    use ResponseTrait;

    public function __construct($id, $controller, $config = [])
    {
        // 加载 post 数据
        $this->loadData();
        $this->checkParams();

        parent::__construct($id, $controller, $config);
    }

    /**
     * 验证参数规则
     * checkParams = [
     * ['code', 'require','10001',false],
     * ['字段名', '验证规则','错误码|错误信息',true|false 为true则表示自定义错误信息，否则认定为错误编码，默认false]
     * ];
     * @param bool $params
     */
    public function checkParams($params = false)
    {
        if (false === $params) {
            $params = $this->checkParams;
        }
        if (!empty($params)) {
            foreach ($params as $value) {
                list($key, $rules) = $value;
                $msg = '';//初始化错误信息
                $code = isset($value[2]) ? $value[2] : ''; //如果第三个参数存在则默认为错误编码
                if (isset($value[3]) && $value[3] === true) { //如果第四个参数存在，并且 === true,则为自定义错误信息，$code 重新默认为-1
                    $msg = $code;
                    $code = '-1';
                }
                $reData = YII_DEBUG ? [$key] : false;
                switch ($rules) {
                    case 'require': //必填
                        $code = !empty($code) ? $code : '90000';
                        if (!isset($this->requestData[$key]) || empty($this->requestData[$key])) {
                            $this->error($code, $msg, $reData);
                        }
                        break;
                    case 'in': //在区间
                        $code = !empty($code) ? $code : '90001';
                        $inArr = isset($value[4]) ? $value[4] : [];
                        if (!isset($this->requestData[$key]) || !in_array($this->requestData[$key], $inArr)) {
                            $this->error($code, $msg, $reData);
                        }
                        break;
                    case 'email': //email
                        $code = !empty($code) ? $code : '90002';
                        if (!filter_var($this->requestData[$key], FILTER_VALIDATE_EMAIL)) {
                            $this->error($code, $msg, $reData);
                        }
                        break;
                    case 'default': //设置默认值
                        if ((!isset($this->requestData[$key]) && empty($this->requestData[$key])) && isset($value[2])) {
                            $this->requestData[$key] = $value[2];
                        }
                        break;
                    default:
                        continue;
                }
            }
        }
    }


    public function formatPaging($total, $data, $page, $pageSize)
    {
        return [
            'total' => $total,
            'data' => $data,
            'page' => $page,
            'pageSize' => (string)$pageSize,
        ];
    }


}