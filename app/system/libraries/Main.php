<?php

namespace System\Libraries;

use Phalcon\Di;
use \Phalcon\Mvc\Model\Manager as ModelManager;
use Defuse\Crypto\Key;

class Main
{
    protected $_session = null;
    protected $_config = null;
    protected $_modelsManager = null;


    public function __construct()
    {
        $this->_config = require __DIR__ . '/../../config/config.php';
        $this->_modelsManager = new ModelManager();

        $this->_setApplication();
    }

    protected function _setApplication()
    {
        $this->_session = Di::getDefault()->getSession();
    }
}