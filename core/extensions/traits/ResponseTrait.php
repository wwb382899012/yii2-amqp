<?php
/**
 * ResponseTrait.php
 *
 * @description : Http 响应类
 *
 * @author : liaobw <liaobw@mingyuanyun.com>
 * @create date : 2019/7/23
 */

namespace core\extensions\traits;

use core\extensions\utils\StringUtil;
use yii\web\Response;

trait ResponseTrait
{
    protected $requestData = [];
    protected $requestGetData = []; // get参数
    protected $token = ''; //登录token
    protected $defaultErrMsg = '系统错误，请联系管理员';

    public function loadData()
    {
        $postData = \Yii::$app->request->post();
        $getData = \Yii::$app->request->get();
        //接收每个接口具体的数据
        if (isset($postData['data'])) {
            //兼容data为字符串
            if (is_string($postData['data'])) {
                $data = json_decode($postData['data'], true);
                json_last_error() == JSON_ERROR_NONE && $postData['data'] = $data;
            }
        }
        //兼容raw
        $raw = \Yii::$app->request->getRawBody();
        if (is_string($raw)) {
            $data = json_decode($raw, true);
            json_last_error() == JSON_ERROR_NONE && $postData = array_merge($postData, $data);
        }
        $this->requestData = !empty($postData['data']) ? $postData['data'] : [];
        $this->requestGetData = $getData;

        //初始化token
        if (!empty($postData['token'])) {
            $this->token = $postData['token'];
        } elseif (!empty($getData['token'])) {
            $this->token = $getData['token'];
        } else {
            //$this->token = StringUtil::uuid();
        }

    }

    /**
     * @param array $data
     * @param string $msg
     */
    public function success($data = null, $msg = '', $code = false)
    {
        if (empty($msg) && $code !== false) {
            $msg = $this->getMsgByCode($code);
        }
        $this->tips(true, $msg, $code, $data);
    }

    public function tips($status, $msg, $code = false, $data = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $res = [
            'status' => $status,
            'msg' => $msg,
        ];
        !is_null($data) && $res['data'] = $data;
        $code !== false && $res['errcode'] = $code;
        \Yii::$app->response->data = $res;
        \Yii::$app->response->send(); //向客户端发送响应
    }

    public function error($errCode, $msg = '', $data = null)
    {
        if (empty($msg)) {
            $msg = $this->getMsgByCode($errCode);
        }

        $this->tips(false, $msg, $errCode, $data);
        // 触发事件
        \Yii::$app->end();
    }

    private function getMsgByCode($code)
    {
        $errCodes = \Yii::$app->params['errorCode'];
        if (isset($errCodes)) {
            return isset($errCodes[$code]) ? $errCodes[$code] : $this->defaultErrMsg;
        }
        return $this->defaultErrMsg;
    }


}