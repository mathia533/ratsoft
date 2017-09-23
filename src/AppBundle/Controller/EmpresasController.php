<?php

namespace AppBundle\Controller;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class EmpresasController extends Controller
{
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need ----  render('default/index.html.twig'
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function listEmpresas(Request $request){
     /*   // Cargo los servicios que voy a utilizar.
        $helpers = $this->get('app.helpers');
        $serializer = SerializerBuilder::create()->build();

        // Recupero el hash recibido
        $json = $request->get('authorization', null);

        // Pregunto al servicio si el hash es auténtico.
        $authCheck = $helpers->authCheck($hash);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('select u from TblRubros u');
        $rubros = $query->getResult();
        $jsonResponse = $serializer->serialize($rubros, 'json');
        return new Response($jsonResponse);
    */
        $helpers = $this->get('app.helpers');
        $serializer = SerializerBuilder::create()->build();
        
        $em = $this->getDoctrine()->getManager();
        
        $empresas = $em->getRepository("BackendBundle:TblEmpresas")->findAll();
        
        // $rubros = $query->getResult();
        /*$data = array(
            'status' => 'ERROR',
            'msg' => 'El usuario no pudo ser creado.'
        );
        */
        $jsonResponse = $serializer->serialize($empresas, 'json');
        return new Response($jsonResponse);
        
    }
}