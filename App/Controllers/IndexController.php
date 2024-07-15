<?php

namespace App\Controllers;

//os recursos do miniframework

use MF\Controller\Action; //abstração do controlador
use MF\Model\Container;

class IndexController extends Action
{

	public function index()
	{
		$this->view->login = isset($_GET['login']) ? isset($_GET['login']) : '';
		$this->render('index');
	}

	public function inscreverse()
	{
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => ''
		);
		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar()
	{
		//debugar =  action="/registrar" e $routes['registrar'] declarada.
		/*
		echo '<pre>';
		echo '</pre>';
		*/

		//receber o dados do formulario
		$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
		$formEmail = (filter_input_array(INPUT_POST, FILTER_VALIDATE_EMAIL));

		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $formData['nome']);
		$usuario->__set('email', $formEmail['email']);
		$usuario->__set('senha', md5($formData['senha']));

		if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {

			$usuario->salvarCadastro();

			$this->render('cadastro');
		} else {

			$this->view->usuario = array(
				'nome' => $formData['nome'],
				'email' => $formEmail['email'],
				'senha' => $formData['senha']
			);
			$this->view->erroCadastro = true; //ativa msg de erro na view
			$this->render('inscreverse'); //rendereiza a view
		}

		//DEBUG
		
		// echo '<pre>';
		// print_r($usuario);
		// echo '</pre>';
		
	}

	public function sair()
	{
		session_start();
		session_destroy();
		header('Location: /');
	}
}
