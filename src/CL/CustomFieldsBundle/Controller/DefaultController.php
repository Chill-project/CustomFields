<?php

namespace CL\CustomFieldsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CLCustomFieldsBundle:Default:index.html.twig', array('name' => $name));
    }
}
