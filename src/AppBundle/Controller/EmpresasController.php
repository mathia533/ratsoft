<?php

namespace AppBundle\Controller;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\TblEmpresas;
use BackendBundle\Entity\TblSituacionIva;

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
    // // Cargo los servicios que voy a utilizar.
    // $helpers = $this->get('app.helpers');
    // $serializer = SerializerBuilder::create()->build();

    // // Recupero el hash recibido
    // $json = $request->get('authorization', null);

    // // Pregunto al servicio si el hash es auténtico.
    // $authCheck = $helpers->authCheck($hash);

    // $em = $this->getDoctrine()->getManager();
    // $query = $em->createQuery('select u from TblRubros u');
    // $rubros = $query->getResult();
    // $jsonResponse = $serializer->serialize($rubros, 'json');
    // return new Response($jsonResponse);

    // // $rubros = $query->getResult();
    // $data = array(
    //     'status' => 'ERROR',
    //     'msg' => 'El usuario no pudo ser creado.'
    // );

		$em = $this->getDoctrine()->getManager();
		$empresas = $em->getRepository("BackendBundle:TblEmpresas")->findAll();

		$serializer = SerializerBuilder::create()->build();
		$jsonResponse = $serializer->serialize($empresas, 'json');
		return new Response($jsonResponse);		
	}

	// 

}