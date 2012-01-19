<?php

namespace app\controllers;

class Site extends \pinturicchio\Controller
{
    public function before()
    {
        parent::before();
        $this->getView()->title = 'Pinturicchio';
    }
    
    public function indexAction()
    {
        $this->renderPartial('site/index');
    }
    
    public function greetAction()
    {
        $this->getView()->title = 'Greeting | ' . $this->getView()->title;
        $this->render(array('name' => ucfirst($_GET['name'])));
    }
}
