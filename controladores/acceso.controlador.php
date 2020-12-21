<?php
	require 'modelos/acceso.modelo.php';
	class AccesoControlador {
		private $acceso;
		public function __CONSTRUCT(){
			$this->acceso = new Acceso();
		}
		public function Principal(){
			if($this->VerificarAcceso()){
				require_once 'vistas/header.html';
				require_once 'vistas/home.html';
				require_once 'vistas/footer.html';
			}else{
				$this->Login();
			}
		}
		public function Login(){
			#echo $this->encrypt_decrypt_usrs('encrypt','Stefano_1994');
			#echo $this->encrypt_decrypt_usrs('decrypt','RzdQeFpCOU5oMkIrc2xuZTA1Y0FCdz09');
			require_once 'vistas/login.html';
		}

		public function IniciarSesion(){
		   	$x = new Acceso();
		   	$x->usuario = strip_tags($_POST['usuario']);
		    $x->contrasena = $this->Crypto('encrypt',$_POST['contrasena']);
		    $x->ip = $this->IP();
		    $x->navegador = $this->Navegador();
		    $x->proveedor_internet = $this->ProveedorInternet();
		    
		    foreach (json_decode($this->acceso->IniciarSesion($x)[0]->resultado) as $r) {
		     	if(!$r->ok){
			    	unset($_SESSION['idusuario_ice']);
				    unset($_SESSION['rol_ice']);
				    unset($_SESSION['usuario_ice']);
				    unset($_SESSION['nombres_usuario_ice']);
				}else{
	   				unset($_SESSION['idusuario_ice']);
	   				unset($_SESSION['rol_ice']);
				    unset($_SESSION['usuario_ice']);
				    unset($_SESSION['nombres_usuario_ice']);
	   				$_SESSION['idusuario_ice'] = $this->Crypto('encrypt',$r->idusuario);
			    	$_SESSION['rol_ice'] = $r->rol;
	   				$_SESSION['usuario_ice'] = $r->usuario;
	   				$_SESSION['nombres_usuario_ice'] = $r->nombres_usuario;
	   			}
	   			echo json_encode($r);
		    }
		}

		public function CerrarSesion(){
		    unset($_SESSION['idusuario_ice']);
		    unset($_SESSION['rol_ice']);
		    unset($_SESSION['usuario_ice']);
		    unset($_SESSION['nombres_usuario_ice']);
		    echo '<script language = javascript>
	                self.location = "http://'.$_SERVER['SERVER_NAME'].'/"
	              </script>';
		}

		public function VerificarAcceso(){
		    if($_SESSION['usuario_ice']){
		  //     $x = new Acceso();
			 //   	$x->usuario = $_SESSION['idusuario_ice'];
			 //    foreach ($this->acceso->VerAccessoBD($x) as $x) {
				// 	if($x->estado){
				// 		return true;
				// 	}else{
				// 		unset($_SESSION['idusuario_ice']);
				// 	    unset($_SESSION['usuario_ice']);
				// 	    return false;
				// 	}
				// }
				return true;
		    }else{
		    	unset($_SESSION['idusuario_ice']);
			    unset($_SESSION['usuario_ice']);
			    unset($_SESSION['nombres_usuario_ice']);
		    	return false;
		    }
		}

		public function MensajeNotificacion(){
			echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><font><strong> ¡Error de acceso!.</strong> Primero debes iniciar sesión. </font></div>';
		    echo "<script language='javascript'>
		            jQuery('.alert').appendTo('.alerta');
		          </script>";
		}
		protected function Navegador(){
			$u_agent = $_SERVER['HTTP_USER_AGENT'];
			$bname = 'Desconocido';
			$plataforma = 'Desconocido';
			$version= "";

			  //First get the plataforma?
			if (preg_match('/linux/i', $u_agent)) {
			    $plataforma = 'Linux';
			}elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			    $plataforma = 'Mac';
			}elseif (preg_match('/windows|win32/i', $u_agent)) {
			    $plataforma = 'Windows';
			}

		  	// Next get the name of the useragent yes seperately and for good reason
			if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
			    $bname = 'Internet Explorer';
			    $ub = "MSIE";
			}elseif(preg_match('/Firefox/i',$u_agent)){
			    $bname = 'Mozilla Firefox';
			    $ub = "Firefox";
			}elseif(preg_match('/OPR/i',$u_agent)){
			    $bname = 'Opera';
			    $ub = "Opera";
			}elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			    $bname = 'Google Chrome';
			    $ub = "Chrome";
			}elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			    $bname = 'Apple Safari';
			    $ub = "Safari";
			}elseif(preg_match('/Netscape/i',$u_agent)){
			    $bname = 'Netscape';
			    $ub = "Netscape";
			}elseif(preg_match('/Edge/i',$u_agent)){
			    $bname = 'Edge';
			    $ub = "Edge";
			}elseif(preg_match('/Trident/i',$u_agent)){
			    $bname = 'Internet Explorer';
			    $ub = "MSIE";
			}

		  	// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
			    // we have no matching number just continue
			}
		  	// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
			    //we will have two since we are not using 'other' argument yet
			    //see if version is before or after the name
			    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
			        $version= $matches['version'][0];
			    }else {
			        $version= $matches['version'][1];
			    }
			}else {
			    $version= $matches['version'][0];
			}

		  	// check if we have a number
			if ($version==null || $version=="") {$version="?";}

			return "Su navegador: ".$bname." ".$version." en los informes de ".$plataforma . ": <br>".$u_agent;
		}
		protected function IP(){
			switch(true){
	          	case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
	          	case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
	          	case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
	          	default : return $_SERVER['REMOTE_ADDR'];
	        }
		}
		protected function ProveedorInternet(){
			/*$query = @unserialize (file_get_contents('http://ip-api.com/php/'));
			$verificador = array();
			if($query && $query['status'] == 'success') {
				foreach ($query as $key => $data) {
					if (!in_array(array($key => $data), $verificador)) {
						array_push($verificador, array($key => $data));
					    if($key == 'region' || $key == 'isp' || $key == 'timezone' || $key == 'countryCode' || $key == 'city' || $key == 'city' || $key == 'org' || $key == 'query' || $key == 'country' || $key == 'regionName' || $key == 'status'){
					    	$datos .= json_encode(array($key => $data)).',';
					    }
					}

				}
			}
			return '['.rtrim($datos,',').']';*/
			return NULL;
		}
		function Crypto($action, $string) {
		    $salida = false;
		    $metodo = "AES-256-CBC";
		    $key = 'colegiospswd';
		    $key_iv = 'NEJpNVJISEM2Q29OMVBmY2l3QUZiejJiRm4wUXpDcUwwM29YeWpDN1Nraz0=';
		    // hash
		    $key = hash('sha256', $key);
		    
		    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		    #$ivSize = openssl_cipher_iv_length($metodo);
		    $iv = substr(hash('sha256', $key_iv), 0, 16);
		    if ($action == 'encrypt') {
		    	if(strlen($string) > 0){
			        $salida = openssl_encrypt($string, $metodo, $key, OPENSSL_RAW_DATA, $iv);
			        $salida = base64_encode($salida);
			    }
		    } else if($action == 'decrypt') {
		    	if(strlen($string) > 0){
		        	$salida = openssl_decrypt(base64_decode($string), $metodo, $key, OPENSSL_RAW_DATA, $iv);
		        }
		    }
		    return $salida;
		}
	}
?>
