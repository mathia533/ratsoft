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
use BackendBundle\Entity\TblProvincias;
use BackendBundle\Entity\TblRubros;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EmpresasController extends Controller
{


	public function newAction(Request $request) {

		$respuesta = array (
			'nombre' => $request->request->get("nombre"),
			'domicilio' => $request->request->get("domicilio"),
			'localidad' => $request->request->get("localidad"),
			'cuit' => $request->request->get("cuit"),
			'iibb' => $request->request->get("iibb"),
			'titular' => $request->request->get("titular"),
			'activo' => $request->request->get("activo"),
			'iva' => (int)$request->request->get("iva"),
			'provincia' => $request->request->get("provincia"),
			'rubro' => $request->request->get("rubro"),
		);
		
		$status = "SUCCESS";
		$msg = $respuesta;

		// Me aseguro que no me hayan mandado ningun campo vacío

		if ( empty($respuesta["nombre"])
			|| empty($respuesta["domicilio"])
			|| empty($respuesta["localidad"])
		 	|| empty($respuesta["cuit"])
			|| empty($respuesta["iibb"])
			|| empty($respuesta["titular"])
			|| empty($respuesta["activo"])
			|| empty($respuesta["iva"])
			|| empty($respuesta["provincia"])
			|| empty($respuesta["rubro"])
			) {
				$status = "ERROR";
				$msg = "No se enviaron todos los datos requeridos para completar la solicitud";
		}
		
		$serializer = SerializerBuilder::create()->build();

		$data = array(
			'status' => $status,
			'msg' => $msg
			);

		// Busco en la DB si existe una empresa con el cuit ingresado.
		$em = $this->getDoctrine()->getManager();
		$isset_empresa = $em->getRepository("BackendBundle:TblEmpresas")->findOneBy(
			array(
				'cuit' => $respuesta["cuit"]
			)
		);

		// Genero el objeto SituacionIva y lo cargo en base al ID recibido
		$situacionIva = new TblSituacionIva();
		$situacionIva = $em->getRepository("BackendBundle:TblSituacionIva")->findOneBy(
			array(
				'id' => $respuesta["iva"]
			)
		);
		// Genero el objeto Provincia y lo cargo en base al ID recibido
		$provincia = new TblProvincias();
		$provincia = $em->getRepository("BackendBundle:TblProvincias")->findOneBy(
			array(
				'id' => $respuesta["provincia"]
			)
		);
		// Genero el objeto Rubro y lo cargo en base al ID recibido
		$rubro = new TblRubros();
		$rubro = $em->getRepository("BackendBundle:TblRubros")->findOneBy(
			array(
				'id' => $respuesta["rubro"]
			)
		);

    	// Si el cuit no existe, se inserta en la DB.
		if (empty($isset_empresa)) {
	  	// Instanciamos un objeto Empresa y seteamos sus datos.
			$empresa = new TblEmpresas();

			$empresa->setNombre($respuesta["nombre"]);
			$empresa->setDomicilio($respuesta["domicilio"]);
			$empresa->setLocalidad($respuesta["localidad"]);
			$empresa->setCuit($respuesta["cuit"]);
			$empresa->setIibb($respuesta["iibb"]);
			$empresa->setTitular($respuesta["titular"]);
			$empresa->setActivo($respuesta["activo"]);
			$empresa->setIva($situacionIva);
			$empresa->setProvincia($provincia);
			$empresa->setRubro($rubro);	

			$em->persist($empresa);
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