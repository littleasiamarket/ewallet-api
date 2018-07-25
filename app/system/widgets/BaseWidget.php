<?php
namespace System\Widgets;

class BaseWidget extends \Phalcon\DI\Injectable
{

    protected $params;

    public function __construct(array $params = array())
    {
        $this->params = $params;
    }

    public function getContent()
    {
        return FALSE;
    }

    /**
     * @param $file
     * @param array $data
     * @return string
     */
    public function setView($file, $data = array())
    {
        $view = $this->di->getView();

        ob_start();
        if ($this->dispatcher->getModuleName())
        {
            /**
             *
             * @var \Phalcon\Mvc\View $view
             */
            $view->partial('../../../../views/softco/widgets/' . $file, $data);
        }
        else
        {
            /**
             *
             * @var \Phalcon\Mvc\View $view
             */
            $view->partial('widgets/' . $file, $data);
        }
        return ob_get_clean();
    }
}
