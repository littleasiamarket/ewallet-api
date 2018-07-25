<?php
namespace Softco\User\Controllers;

class IndexController extends \Softco\Controllers\BaseController
{

    public function indexAction()
    {
        $input = array();

        if($this->_statusCode == 200){
            if(!isset($this->_requestData["login_id"]) || !isset($this->_requestData["token"])){
                $this->_statusCode = 400;
                $this->_responseData["messages"][] = 'wrong_format';

                return $this->sendMessage();
            }

            try {
                $input['login_id'] = strtoupper(\filter_var(\strip_tags(\addslashes($this->_requestData['login_id'])), FILTER_SANITIZE_STRING));
                $input['token'] = \filter_var(\strip_tags(\addslashes($this->_requestData['token'])), FILTER_SANITIZE_STRING);

                $this->_responseData["data"]["user"] = true;
                $this->_responseData["messages"][] = 'invalid_user';
            }catch (\Exception $e){
                $this->_statusCode = 400;
                $this->_responseData["messages"][] = $e->getMessage();
            }
        }

        return $this->sendMessage();
    }
}