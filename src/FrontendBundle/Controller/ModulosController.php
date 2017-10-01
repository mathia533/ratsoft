<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ModulosController extends Controller
{
    public function contableAction()
    {
        return $this->render('FrontendBundle:Empresas:contable-home.html.twig');
    }

    public function impositivoAction()
    {
        return $this->render('FrontendBundle:Empresas:impositivo-home.html.twig');
    }
}
