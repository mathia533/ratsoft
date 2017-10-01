<?php

namespace BackendBundle\Controller;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\TblEmpresas;
use BackendBundle\Entity\TblSituacionIva;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EmpresasController extends Controller
{


	public function newAction(Request $request) {

		$json = $request->get('json', null);
		$request = json_decode($json);

		if ($json != null) {
			$nombre = (isset($request->nombre)) ? $request->nombre : null;
			$domicilio = (isset($request->domicilio)) ? $request->domicilio : null;
			$localidad = (isset($request->localidad)) ? $request->localidad : null;
			$cuit = (isset($request->cuit)) ? $request->cuit : null;
			$iibb = (isset($request->iibb)) ? $request->iibb : null;
			$titular = (isset($request->titular)) ? $request->titular : null;
			$activo = (isset($request->activo)) ? $request->activo : null;
			$iva = (isset($request->iva)) ? $request->iva : null;
			$provincia = (isset($request->provincia)) ? $request->provincia : null;
			$rubro = (isset($request->rubro)) ? $request->rubro : null;
		}

		$serializer = SerializerBuilder::create()->build();

		// {"nombre":"ratsoft SA", "domicilio":"calle falsa 123", "localidad":"CABA", "cuit":"1111", "iibb":"123456789", "titular":"El gordo Samid", "activo":"1", "iva":"1" ,"provincia":"1", "rubro":"1"}

		$data = array(
			'status' => 'ERROR',
			'msg' => 'No se enviaron todos los datos requeridos para completar la solicitud'
			);

		// Si $rubro es null no se puede completar la solicitud.
		if (empty($rubro)) {

			$jsonResponse = $serializer->serialize($data, 'json');
			return new Response($jsonResponse);
		} else {
	   	// Busco en la DB si existe una empresa con el cuit ingresado.
			$em = $this->getDoctrine()->getManager();
			$isset_empresa = $em->getRepository("BackendBundle:TblEmpresas")->findOneBy(
				array(
					'cuit' => $request->cuit
					));

    	// Si el cuit no existe, se inserta en la DB.
			if (empty($isset_empresa)) {
	  		// Instanciamos un objeto Empresa y seteamos sus datos.
				$empresa = new TblEmpresas();

				$empresa->setNombre($nombre);
				$empresa->setDomicilio($domicilio);
				$empresa->setLocalidad($localidad);
				$empresa->setCuit($cuit);
				$empresa->setIibb($iibb);
				$empresa->setTitular($titular);
				$empresa->setActivo($activo);
				$empresa->setIva($iva);
				$empresa->setProvincia($provincia);
				$empresa->setRubro($rubro);	

				$em->persist($user);
				$em->flush();	

				$data = array(
					'status' => 'OK',
					'msg' => 'La empresa ha sido registrada con exito'
					);
			} else {
				$data = array(
					'status' => 'ERROR',
					'msg' => 'Ya existe una empresa registrada con el cuit ingresado'
					);
			}
		}

		$jsonResponse = $serializer->serialize($data, 'json');
		return new Response($jsonResponse);
	}


	public function allAction(Request $request){
    	$em = $this->getDoctrine()->getManager();
		$result = $em->getRepository("BackendBundle:TblEmpresas")->findAll();
		$empresas = array(
			'draw' => '',
			'recordsTotal' => '',
			'recordsFiltered' => '',
			'data' => $result,
		);
		$serializer = SerializerBuilder::create()->build();
		$jsonResponse = $serializer->serialize($empresas, 'json');
		return new Response($jsonResponse);		
	}

	/**
	*	@Route("/empresa/find",name="empresa_find")
	*	@Method({"POST"})
	*/

	public function findByCuitAction(Request $request){

				
		// guardamos la variable POST que viene en el request
		$cuit = $request->request->get("cuit");
    	
    	// obtenemos el JSON con el resultado de la búsqueda por cuit
    	$em = $this->getDoctrine()->getManager();
		$result = $em->getRepository("BackendBundle:TblEmpresas")->findOneBy(array('cuit' => $cuit));

		// armamos el JSON para mantener el formato que requiere un Datatable
		$empresas = array(
			'draw' => '',
			'recordsTotal' => '',
			'recordsFiltered' => '',
			'data' => $result,
		);

		// usamos el serializer para armar un string que contenga el JSON recursivo, es decir que se vean todos los niveles de profundidad del arbol. Si no hacemos esto solo mandariamos el nivel 0.
		
		$serializer = SerializerBuilder::create()->build();
		$jsonResponse = $serializer->serialize($empresas, 'json');
		
		//Mandamos la respuesta.

		return new Response(
			$jsonResponse
		);
	} 
}