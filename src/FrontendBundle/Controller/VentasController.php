<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class VentasController extends Controller
{
	public function indexAction()
	{
		return $this->render('FrontendBundle:Ventas:ventas-home.html.twig');
	}

}