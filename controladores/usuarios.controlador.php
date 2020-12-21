<?php
require 'controladores/acceso.controlador.php';
require 'modelos/usuarios.modelo.php';
class UsuariosControlador {
  	protected $usuario;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->usuario = new Usuarios();
	    $this->acceso = new AccesoControlador();
  	}
  
  	public function FrmUsuarios(){
		if($this->acceso->VerificarAcceso()){
			if($_SESSION['rol_ice'] === 'ADMINISTRADOR' || $_SESSION['rol_ice'] === 'SUPER USUARIO'){
				require_once 'vistas/header.html';
				require_once 'vistas/usuarios.html';
				require_once 'vistas/footer.html';
			}else{
				$this->acceso->Principal();
			}
		}else{
      		$this->acceso->Login();
    	}
	}


	public function ListarUsuarios(){
		if($this->acceso->VerificarAcceso()){
    		$id = 1;
	    	foreach ($this->usuario->ListarUsuarios() as $r) {
				if($r->estado){
					$estado = '<span class="badge badge-success">ACTIVO</span>';
				}else{
					$estado = '<span class="badge badge-default">INACTIVO</span>';
				}
		        $data[] = array(
					'num'=> $id++,
					'cedula'=> $r->cedula, 
					'nombres'=> $r->nombres,
					'apellidos'=> $r->apellidos,
		          	'telefono'=> $r->telefono,
		          	'correo'=> $r->correo,
		          	'contrasena'=> $r->contrasena,
		          	'direccion'=> $r->direccion,
		          	'idprovincia' => $this->acceso->Crypto('encrypt',$r->idprovincia),
		          	'provincia'=> $r->provincia,
		          	'canton'=> $r->canton,
		          	'idcanton' => $this->acceso->Crypto('encrypt',$r->idcanton),
		          	'parroquia'=> $r->parroquia,
		          	'idparroquia' => $this->acceso->Crypto('encrypt',$r->idparroquia),
		          	'rol'=> $r->rol,
		          	'idrol' => $this->acceso->Crypto('encrypt',$r->idrol),
		          	'observacion'=> $r->observacion,
		          	'creado'=> $r->creado,
		          	'modificado'=> $r->modificado,
		          	'estado' => $estado,
		          	'accion' => '
			        	<div class="dropdown">
						  <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">more_vert</i>
						  </button>
						  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						    <a class="dropdown-item editarUsuario" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="modificar" data-toggle="modal" data-target="#exampleModal"><i class="material-icons">edit</i> Modificar</a>
						    <a class="dropdown-item eliminarUsuario" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="eliminar"><i class="material-icons">remove_circle_outline</i> Eliminar</a>
						  </div>
						</div>'
				);
		  	}
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
		    echo json_encode($results);
	    }else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}


    public function ValidarExisteUsuario(){
		if($this->acceso->VerificarAcceso()){
			$x = new Usuarios(); 
			$x->cedula =strip_tags($_POST['cedula']);
			$x->usuario = $this->acceso->Crypto('decrypt',$_SESSION['idusuario_ice']);  
			foreach (json_decode('['.$this->usuario->ValidarExisteUsuario($x)[0]->resultado.']') as $r) {
				$data = array(
					'cedula' => $r->cedula,
					'apellidos' => $r->apellidos,
					'nombres' => $r->nombres,
					'telefono' => $r->telefono,
					'correo' => $r->correo,
					'direccion' => $r->direccion,
					'idprovincia' => $this->acceso->Crypto('encrypt',$r->idprovincia),
					'idcanton' => $this->acceso->Crypto('encrypt',$r->idcanton),
					'idparroquia' => $this->acceso->Crypto('encrypt',$r->idparroquia),
					'ok' => $r->ok,
					'existe' => $r->existe,
					'observacion' => $r->observacion
				);
			}
			echo json_encode($data);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}


	public function ActualizarUsuarios(){
		if($this->acceso->VerificarAcceso()){
			$x = new Usuarios();
			$x->id = $this->acceso->Crypto('decrypt',$_POST['id']);
			$x->cedula = $_POST['cedula'];
			$x->apellidos = strip_tags($_POST['apellidos']);
			$x->nombres = strip_tags($_POST['nombres']);
			$x->telefono = strip_tags($_POST['telefono']);
			$x->correo = strip_tags($_POST['correo']);
			$x->contrasena =  strip_tags($_POST['cambio_contrasena']) == 'true' ? $this->acceso->Crypto('encrypt',$_POST['contrasena']) : strip_tags($_POST['contrasena']);
			$x->direccion = strip_tags($_POST['direccion']);
			$x->parroquia = $this->acceso->Crypto('decrypt',$_POST['parroquia']);
			$x->rol = $this->acceso->Crypto('decrypt',$_POST['rol']);
			$x->observacion = strip_tags($_POST['observacion']);
			$x->estado = strip_tags($_POST['estado']) === 'on' ? 1 : 0; 
			$x->usuario = $this->acceso->Crypto('decrypt',$_SESSION['idusuario_ice']);
			$x->accion = strip_tags($_POST['accion']);
			
			#var_dump($x);
			if($x->accion == 'registrar' || $x->accion == 'modificar' || $x->accion == 'eliminar'){
				$resultado = $this->usuario->ActualizarUsuarios($x);
				foreach ($resultado as $r) {
					echo $r->resultado;
				}
			}else{
				echo json_encode(array('observacion' => 'No se enviaron los datos correctamente. Inténtelo nuevamente.'));
			}
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    public function CargarRoles(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->usuario->CargarRoles() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
 	        echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
}
?>
