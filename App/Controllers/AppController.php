<?php

namespace App\Controllers;

//os recursos do miniframework

use MF\Controller\Action; //abstração do controlador
use MF\Model\Container;

class AppController extends Action
{
	public function timeline()
	{
		$this->validaAutenticação(); //valida se o usuario esta autenticado

		//recupera os tweets SELECT TWEETS
		$tweet = Container::getModel('Tweet');
		$tweet->__set('id_usuario', $_SESSION['id']);
		$tweets = $tweet->getAll();

		$this->view->tweets = $tweets;
		
		//PASSANDO UMA FUNÇÃO DA MODEL PARA VIEW
		//Informações do perfil a esquerda da tela timeline 
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);
		$this->view->info_usuario = $usuario->getInfoUser();
		$this->view->todos_tweets = $usuario->getAllTweet();
		$this->view->todos_seguindo = $usuario->getFollowing();
		$this->view->todos_seguidores = $usuario->getFollowers();

		$this->render('timeline');
	}

	public function tweet()
	{
		$this->validaAutenticação(); //valida se o usuario esta autenticado

		//conexão com o banco
		$tweet = Container::getModel('Tweet');
		//definir os valores do input
		$tweet->__set('id_usuario', $_SESSION['id']);
		$tweet->__set('tweet', $_POST['tweet']);
		//executa o metodo salvar da model Tweet
		$tweet->salvar();

		header('Location: /timeline');
	}

	/**
	 *Deletar tweet
	 * @return void
	 */
	public function deleteTweet(){
		$this->validaAutenticação();

		$delete = Container::getModel('Tweet');
		$delete->__set('id', $_GET['id']);

		$delete->deleteTweet();
		header('Location: /timeline');
	}

	public function quemSeguir()
	{
		$this->validaAutenticação();

		$pesquisa = isset($_GET['usuario']) ? $_GET['usuario'] : '';

		$usuarios = array();

		if ($pesquisa != '') {
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisa);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}
		$this->view->usuarios = $usuarios;

		$this->render('quemSeguir');
	}

	/**
	 *Seguir e deixar de seguir
	 * @return void
	 */

	public function acao()
	{
		$this->validaAutenticação();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Seguidores');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguindo);


		} elseif($acao =='deixar_seguir') {
			$usuario->deixaSeguirUsuario($id_usuario_seguindo);
		}

		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

	public function validaAutenticação()
	{
		session_start();
		if (
			!isset($_SESSION['id']) || !$_SESSION['id'] != '' &&
			!isset($_SESSION['nome']) || !$_SESSION['nome'] != ''
		) {
			header('Location: /?login=erro');
		}
	}
}
