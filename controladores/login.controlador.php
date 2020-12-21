<?php
require 'controladores/acceso.controlador.php';
	require 'modelos/login.modelo.php';
	class LoginControladores{
		private $login;
		private $acceso;
		public function __CONSTRUCT(){
			$this->login = New Login();
			$this->acceso = new AccesoControlador();
		}

		public function FrmIniciarSesion(){
			require_once 'views/login.php';
		}

		public function FrmError(){
			require_once 'views/header.php';
			require_once 'views/error.php';
			require_once 'views/footer.php';
		}

		
	}
?>
