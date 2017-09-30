<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PruebaController extends Controller
{
    public function indexAction()
    {

    	return $this->render('FrontendBundle:Default:prueba.html.twig');
    }
}