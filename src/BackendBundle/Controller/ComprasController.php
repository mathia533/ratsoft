<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\TblCompras;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ComprasController extends Controller
{
	/**
	*	@Route("/compras",name="compras_all")
	*	@Method({"GET"})
	*/

	public function allAction($id,Request $request){
    	$em = $this->getDoctrine()->getManager();
		$result = $em->getRepository("BackendBundle:TblCompras")->findBy(array('cuit' => $id));
		$empresas = array(
			'draw' => '',
			'recordsTotal' => '',
			'recordsFiltered' => '',
			'data' => $result,
		);
		$serializer = SerializerBuilder::create()->build();
		$jsonResponse = $serializer->serialize($empresas, 'json');
		return new Response($jsonResponse);		
		//return new Response($id);
	}

	public function addAction($id,Request $request){
    	$em = $this->getDoctrine()->getManager();
    	$compras = new TblCompras();
    	
    	$compras->setFechaIngreso($postFechaIngreso);
    	
		



		$serializer = SerializerBuilder::create()->build();
		$jsonResponse = $serializer->serialize($empresas, 'json');
		return new Response($jsonResponse);		
		//return new Response($id);
	}


}