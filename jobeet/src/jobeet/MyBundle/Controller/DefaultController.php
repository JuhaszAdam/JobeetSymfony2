<?php

namespace jobeet\MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyBundle:Default:index.html.twig', array('name' => $name));
    }
}
