<?php

namespace AppBundle\Controller;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{

    public function loginAction(Request $request) {
        // Cargo el Servicio jwt_auth para validar el login.
        $jwt_auth = $this->get('app.jwt_auth');

        // Cargo el servicio serializer para devolver un objeto en el response.
        $serializer = SerializerBuilder::create()->build();

        // Recibo json por POST
        $json = $request->get('json', null);


        // Si existe algo en el Json..
        if ($json != null)  {
            $params = json_decode($json);

            // Tomo los datos recibidos en el Request.
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->gethash)) ? $params->gethash : null;

            // Valido que el Email sea un realmente un email mediante Asserts.
            $emailContraint = new Assert\Email();
            $emailContraint->message = "Este email no es válido";
            $validate_email = $this->get('validator')->validate($email, $emailContraint);

            // Si Email cumple con los estándares, y password no es null..
            if (count($validate_email) == 0 && $password != null) {
                /* Llamo al servicio de autenticación y le envío el user y password del Request, 
                    para saber si el usuario existen en la DB..
                    Genero el token y lo recibo en $signup */
                    if ($getHash == null) {
                        $signup = $jwt_auth->signup($email, $password);
                    } else {
                        $signup = $jwt_auth->signup($email, $password, true);
                    }

                // Convierto en formato Json la respuesta del servicio, y la envío al Front.
                    $jsonContent = $serializer->serialize($signup, 'json');
                    return new Response($jsonContent);
                } else {
                    return array(
                        'status' => 'ERROR',
                        'data' => 'Los datos ingresados son incorrectos.'
                        );
                }
            } else {
                return array(
                    'status' => 'ERROR',
                    'data' => 'No se han enviado datos por POST.'
                    );
            }
        }


        public function autenticacionAction(Request $request)
        {
        // Cargo los servicios que voy a utilizar.
            $helpers = $this->get('app.helpers');
            $serializer = SerializerBuilder::create()->build();

        // Recibo el parametro 'authorization' del Request.
            $hash = $request->get('authorization', null);

        /* Le pasamos el hash que tenemos del usuario, y si le pasamos true al metodo authCheck, 
            nos devuelve todos los datos del usuario logueado.
            Si no le pasamos ningun parametro, nos devuelve true o false (Si está o no logueado) 

            Envío true: Devuelve los datos decodificados.
            Envío sólo hash: Devuelve boolean. */
            $check = $helpers->authCheck($hash);

            $jsonContent = $serializer->serialize($check, 'json');
            return new Response($jsonContent);
        }
    }
