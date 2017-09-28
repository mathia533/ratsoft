<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('FrontendBundle:Default:index.html.twig');
	}

	public function loginAction()
	{
		// return $this->render('FrontendBundle:Users:login.html.twig');
		return $this->render('login/login.html.twig');
	}

	public function registerAction()
	{
		return $this->render('FrontendBundle:Users:register.html.twig');
	}
}
