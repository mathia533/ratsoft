<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use BackendBundle\Entity\User;
use BackendBundle\Entity\TblRubros;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class UserController extends Controller
{
    public function newAction(Request $request) {
        // Cargo los servicios que voy a utilizar.
        $helpers = $this->get('app.helpers');
        $serializer = SerializerBuilder::create()->build();

        $json = $request->get('json', null);
        $params = json_decode($json);
        
        // Por default $data devuelve un error generico.      
        $data = array(
    		'status' => 'ERROR',
    		'msg' => 'El usuario no pudo ser creado.'
    	);


    	// Si $json no es null, procedo a crear el usuario.
        if ($json != null) {
        	$username = (isset($params->username)) ? $params->username : null;
        	$password = (isset($params->password)) ? $params->password : null;
        	$nombre = (isset($params->nombre)) ? $params->nombre : null;
        	$apellido = (isset($params->apellido)) ? $params->apellido : null;
        	$email = (isset($params->email)) ? $params->email : null;
        	$role = (isset($params->role)) ? $params->role : 'USER';
        	$activo = 1;
        	$createdAt = new \Datetime('now');

        	// Valido que el Email sea un realmente un email mediante Asserts.
            $emailContraint = new Assert\Email();
            $emailContraint->message = "Este email no es válido";
            $validate_email = $this->get('validator')->validate($email, $emailContraint);

            // Valido que ninguno de los datos del usuario sea null.
            if ($email != null && count($validate_email) == 0 && $password != null 
            	&& $nombre != null && $apellido != null	&& $role != null) {

            	// Instanciamos un objeto User y seteamos sus datos.
            	$user = new User();
            	
            	$user->setUsername($username);
            	$user->setNombre($nombre);
            	$user->setApellido($apellido);
            	$user->setEmail($email);
            	$user->setRole($role);
            	$user->setActivo($activo);
            	$user->setCreatedat($createdAt);

            	// Cifrar contraseña
            	$psw = hash('sha256', $password);
            	$user->setPassword($psw);

            	// Busco en la DB si existe un usuario con el email ingresado.
            	$em = $this->getDoctrine()->getManager();
            	$isset_user = $em->getRepository("BackendBundle:User")->findBy(
            		array(
            			'email' => $email
            		));

            	// Si el usuario no existe, se inserta en la DB.
            	if (count($isset_user) == 0) {
            		$em->persist($user);
					$em->flush();	

	            	$data = array(
	        			'status' => 'OK',
	        			'msg' => 'El usuario ha sido creado con exito!'
	        		);
            	} else {
            		// Si el usuario existe, retornamos mensaje de usuario duplicado.
            		$data = array(
	        			'status' => 'ERROR',
	        			'msg' => 'El usuario ya se encuentra registrado!'
	        		);
            	}
            }

        }

        $jsonResponse = $serializer->serialize($data, 'json');
        return new Response($jsonResponse);
    }


	public function editAction(Request $request) {
        // Cargo los servicios que voy a utilizar.
        $helpers = $this->get('app.helpers');
        $serializer = SerializerBuilder::create()->build();

        // Recupero el hash recibido
        $json = $request->get('authorization', null);

        // Pregunto al servicio si el hash es auténtico.
        $authCheck = $helpers->authCheck($hash);

    	// Busco en la DB si existe un usuario con el email ingresado.
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository("BackendBundle:User")->findOneBy(
    		array(
    			'id' => $getIdentity->sub
    		));
        
        // Por default $data devuelve un error generico.      
        $data = array(
    		'status' => 'ERROR',
    		'msg' => 'El usuario no pudo ser creado.'
    	);
	}

    public function listAction(Request $request){
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