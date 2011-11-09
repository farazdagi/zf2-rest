<?php

namespace Application\Controller;

use Zend\Mvc\Controller\ActionController;

class IndexController extends ActionController
{
    public function indexAction()
    {
        $em = $this->getLocator()->get('doctrine')->getEntityManager();
        var_dump($em);

        return array();
    }
}
