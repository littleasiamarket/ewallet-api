<?php
namespace Softco\Controllers;

defined('APP_PATH') || define('APP_PATH', realpath('.'));

use Phalcon\Mvc\Controller;
use Phalcon\Translate\Adapter\NativeArray;

use System\Models\Webservice;
use System\Models\WebserviceBotRequestLog;
use System\Models\WebserviceRequestLog;

use System\Libraries\Security\General as GeneralSecurity;

class BaseController extends Controller
{
    //language
    protected $_language = "en";
    protected $_languageList = null;

    protected $_game = false;
    protected $_ewallet = false;

    protected $_webservice = null;
    protected $_requestData = null;
    protected $_requestLog = null;
    protected $_ip = null;
    protected $_responseData = array(
        "code" => 200,
        "messages" => array(),
        "request_id" => null,
        "data" => array()
    );
    protected $_statusCode = 200;

    public function initialize()
    {
        $this->view->disable();
        $this->_setLanguage();

        if ($this->request->getPost()){
            //check secure token
            $this->_requestData = $this->request->getPost();

            if(isset($this->_requestData["app_id"]) && isset($this->_requestData["app_secret"])){
                if($this->_webservice = $this->checkAccess()){
                    if(isset($this->_requestData["ip"]) && !empty($this->_requestData["ip"])){
                        $this->_ip = $this->_requestData["ip"];
                    }else{
                        $this->_ip = GeneralSecurity::getIP();
                    }
                    if($this->_requestLog = $this->getRequestId()){
                        //success log the request
                        $this->session->set("app_id", $this->_requestData["app_id"]);
                        $this->session->set("app_secret", $this->_requestData["app_secret"]);
                    }else{
                        $this->_statusCode = 418;
                        $this->_responseData["messages"][] = "I'm a teapot! Request not found.";
                    }
                }else{
                    $this->_statusCode = 401;
                    $this->_responseData["messages"][] = "Unauthorized.";
                }
            }else{
                $this->_statusCode = 401;
                $this->_responseData["messages"][] = "Unauthorized.";
            }
        }else{
            $this->_statusCode = 405;
            $this->_responseData["messages"][] = "A request method is not supported for the requested resource.";
        }
    }

    protected function _setLanguage()
    {
        //set language list
        $this->_languageList = $this->config->language;
        $this->view->languageList = $this->_languageList;

        $messages = array();

        if($this->cookies->has('language')){
            $this->_language = $this->cookies->get('language')->getValue();
        }

        $this->view->language = $this->_language;

        //get languages
        if (file_exists(APP_PATH."/app/system/languages/" . $this->_language . ".php")) {
            $messages = (include APP_PATH."/app/system/languages/" . $this->_language . ".php");
        } else {
            $messages = (include APP_PATH."/app/system/languages/en.php");
        }

        $this->view->t = new NativeArray(
            array(
                "content" => $messages
            )
        );
    }

    public function checkAccess(){
        $webservice = Webservice::findFirst(
            array(
                "conditions" => "token = :token: AND auth = :auth: AND status = :status:",
                "bind" => array(
                    "token" => $this->_requestData['app_id'],
                    "auth" => $this->_requestData['app_secret'],
                    "status" => 1
                )
            )
        );

        if(!$webservice || $webservice->getStatus() == 0){
            return false;
        }

        //TODO: FIX TO GET GAME OR EWALLET
//        $this->_company = User::findFirst($webservice->getCompany());
//
//        if(!$this->_company){
//            return false;
//        }

        return $webservice;
    }

    public function getRequestId(){
        $webserviceRequestLog = new WebserviceRequestLog();
        $webserviceRequestLog->setWebservice($this->_webservice->getId());
        $webserviceRequestLog->setUrl($_SERVER['REQUEST_URI']);
        $webserviceRequestLog->setRequest(json_encode($this->_requestData));
        $webserviceRequestLog->setRequestDate(\date("Y-m-d H:i:s"));
        $webserviceRequestLog->setIp($this->_ip);

        $webserviceRequestLog->save();

        return $webserviceRequestLog;
    }

    public function sendMessage(){
        //Create a response instance
        $response = new \Phalcon\Http\Response();

        //Set the content of the response
        $this->_responseData["code"] = $this->_statusCode;
        if($this->_requestLog){
            $this->_responseData["request_id"] = $this->_requestLog->getId();
        }

        $responseData = json_encode($this->_responseData , JSON_UNESCAPED_SLASHES);
        $response->setContent($responseData);

        if($this->_requestLog){
            //save the response first before sent it out to the world
            $this->_requestLog->setResponse($responseData);
            $this->_requestLog->setStatus($this->_statusCode);

            $this->_requestLog->save();
        }

        //Return the response
        return $response;
    }

    protected function noticeFlash($message)
    {
        $this->flash->notice($message);
    }

    protected function errorFlash($message)
    {
        $this->flash->error($message);
    }

    protected function successFlash($message)
    {
        $this->flash->success($message);
    }
}
