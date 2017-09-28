<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use BackendBundle\Entity\TblUser;
use BackendBundle\Entity\TblRubros;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class UserController extends Controller
{

  public function allAction() {
        // Cargo los servicios que voy a utilizar.
    $serializer = SerializerBuilder::create()->build();

        // Busco en la DB los usuarios existentes.
    $em = $this->getDoctrine()->getManager();
    $users = $em->getRepository("BackendBundle:TblUser")->findAll();

    $data = array(
      'status' => 'OK',
      'users' => $users
      );

    $jsonResponse = $serializer->serialize($data, 'json');
    return new Response($jsonResponse);
}

    public function newAction(Request $request) {
            // Cargo los servicios que voy a utilizar.
        $helpers = $this->get('app.helpers');
        $serializer = SerializerBuilder::create()->build();

        var_dump($request);
        die();
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
         $role = (isset($params->role)) ? $params->role : 1;
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
             $user = new TblUser();

         $user->setUsername($username);
         $user->setNombre($nombre);
         $user->setApellido($apellido);
         $user->setEmail($email);
         $user->setRole($role);
         $user->setActivo($activo);
         $user->setCreateDate($createdAt);

                	// Cifrar contraseña
         $psw = hash('sha256', $password);
         $user->setPassword($psw);

                	// Busco en la DB si existe un usuario con el email ingresado.
         $em = $this->getDoctrine()->getManager();
         $isset_user = $em->getRepository("BackendBundle:TblUser")->findBy(
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
    $serializer = SerializerBuilder::create()->build();
    $helpers = $this->get('app.helpers');

    // Recupero el hash recibido en el request.
    $hash = $request->get('authorization', null);
    $authCheck = $helpers->authCheck($hash);

    if ($authCheck == true) {
      // Pedimos que nos devuelva todos los datos del user logueado.
      $identity = $helpers->authCheck($hash, true);

      // Busco la entidad correspondiente al usuario logueado.
      $em = $this->getDoctrine()->getManager();
      $user = $em->getRepository("BackendBundle:TblUser")->find($identity->sub);

      // Busco en la DB si existe un usuario con el email ingresado.
      $isset_email = $em->getRepository("BackendBundle:TblUser")->findBy(
          array(
             'email' => $identity->email,
             'id' => $identity->sub
             ));

      // Busco en la DB si existe un usuario con el username ingresado.
      $isset_username = $em->getRepository("BackendBundle:TblUser")->findBy(
          array(
             'username' => $identity->username
             ));


      // Recibimos los datos nuevos del usuario del Request.
      $json = $request->get('json', null);
      $params = json_decode($json);

      // Por default $data devuelve un error generico.      
      $data = array(
        'status' => 'ERROR',
        'msg' => 'El usuario no pudo ser editado.'
        );


      // FALTA HACER LAS EXCEPCIONES PARA CUANDO QUIERO MODIFICAR UN USUARIO Y ASIGNARLE EL EMAIL O USERNAME DE OTRO USUARIO YA EXISTENTE.

      // Si $json no es null, procedo a crear el usuario.
      if ($json != null) {
        $username = (isset($params->username)) ? $params->username : null;
        $nombre = (isset($params->nombre)) ? $params->nombre : null;
        $apellido = (isset($params->apellido)) ? $params->apellido : null;
        $email = (isset($params->email)) ? $params->email : null;

        // Valido que ninguno de los datos recibidos sea null.
        if ($email != null && $username != null && $nombre != null && $apellido != null) {
          $user->setUsername($username);
          $user->setNombre($nombre);
          $user->setApellido($apellido);
          $user->setEmail($email);

          // Persisto los datos en la DB.
          $em->persist($user);
          $em->flush();

          $data = array(
            'status' => 'OK',
            'msg' => 'El usuario ha sido modificado con exito!'
            );
      }
  }
} else {
  $data = array(
    'status' => 'ERROR',
    'msg' => 'Se ha cerrado la sesión'
    );
}

$jsonResponse = $serializer->serialize($data, 'json');
return new Response($jsonResponse);
}

}