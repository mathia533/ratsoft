<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
	public $manager;
	
	function __construct($manager) {
		$this->manager = $manager;
	}


	public function signup($email, $password, $getHash = NULL) {
		$key = 'clave-secreta';

		// Busco en la DB si el usuario con el Email y Password del Request existe.
		$user = $this->manager->getRepository('BackendBundle:User')->findOneBy(
			array(
				'email' => $email,
				'password' => $password
			)
		);

		$signup = false;
		// Compruebo si es un objeto.
		if (is_object($user)) {
			$signup = true;
		}

		// Si es un objeto.. tomo los datos de la base y genero el token..
		if ($signup == true) {
			$token = array(
				'sub' => $user->getId(),
				'username' => $user->getUsername(),
				'password' => $user->getPassword(),
				'nombre' => $user->getNombre(),
				'apellido' => $user->getApellido(),
				'email' => $user->getEmail(),
				'role' => $user->getRole(),
				'activo' => $user->getActivo(),
				'createdat'=> $user->getCreatedat(),
				'iat' => time(), // Fecha creación Hash.
				'exp' => time() + (5 * 10) // Fecha expiración Hash. 50seg masomenos
			);

			// Token encodeado..
			$jwt = JWT::encode($token, $key, 'HS256');
			// Token decodificado..
			$decoded = JWT::decode($jwt, $key, array('HS256'));

			if ($getHash != null) {
				// Si envie el hash, devuelvo el token encodeado..
				return $jwt;
			} else {
				// Sino devuelvo el token decodificado..
				return $decoded;
			}
		} else {
			return array(
				'status' => 'ERROR',
				'data' => 'Login fallido!'
			);
		}
	}


	public function checkToken($jwt, $getIdentity = false) {
		$key = 'clave-secreta';
		$auth = false;

		try {
			$decoded = JWT::decode($jwt, $key, array('HS256'));
		} catch (\UnexpectedValueException $e) {
			$auth = false;
		} catch (\DomainException $e) {
			$auth = false;
		}

		// Verificamos que exista el campo sub ('id' del usuario).
		if (isset($decoded->sub)) {
			$auth = true;
		} else {
			$auth = false;
		}

		// Si le pasamos $getIdentity == true nos devuelve los datos decodificados.
		if ($getIdentity == true) {
			return $decoded;
		} else {
			return $auth;
		}
	}

}