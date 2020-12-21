<?php
require 'controladores/acceso.controlador.php';
require 'modelos/tamanos.modelo.php';
class TamanosProductosControlador {
  	protected $tamano;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->tamano = new TamanosProductos();
	    $this->acceso = new AccesoControlador();
  	}
  
  	public function FrmTamanosProductos(){
		if($this->acceso->VerificarAcceso()){
			if($_SESSION['rol_ice'] === 'ADMINISTRADOR' || $_SESSION['rol_ice'] === 'SUPER USUARIO'){
				require_once 'vistas/header.html';
				require_once 'vistas/tamanos.html';
				require_once 'vistas/footer.html';
			}else{
				$this->acceso->Principal();
			}
		}else{
      		$this->acceso->Login();
    	}
	}


	public function ListarTamanosProductos(){
		if($this->acceso->VerificarAcceso()){
    		$id = 1;
	    	foreach ($this->tamano->ListarTamanosProductos() as $r) {
				if($r->estado){
					$estado = '<span class="badge badge-success">ACTIVO</span>';
				}else{
					$estado = '<span class="badge badge-default"> INACTIVO</span>';
				}
		        $data[] = array(
					'num'=> $id++,
					'nombre'=> $r->nombre,
		          	'costo'=> $r->costo,
		          	'precio'=> $r->precio,
		          	'utilidad'=> number_format((($r->precio - $r->costo) / ($r->precio <= 0 ? 1 : $r->precio)),2)."%",
					'observacion'=> $r->observacion,
		          	'creado'=> $r->creado,
		          	'modificado'=> $r->modificado,
		          	'estado' => $estado,
		          	'accion' => '
			        	<div class="dropdown">
						  <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">more_vert</i>
						  </button>
						  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						    <a class="dropdown-item editarTamano" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="modificar" data-toggle="modal" data-target="#ActualizarTamano"><i class="material-icons">edit</i> Modificar</a>
						    <a class="dropdown-item eliminarTamano" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="eliminar"><i class="material-icons">remove_circle_outline</i> Eliminar</a>
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
	public function ActualizarTamanosProductos(){
		if($this->acceso->VerificarAcceso()){
			$x = new TamanosProductos();
			$x->id = $this->acceso->Crypto('decrypt',$_POST['id']);
			$x->nombre = strip_tags($_POST['nombre']);
			$x->costo = strip_tags($_POST['costo']);
			$x->precio = strip_tags($_POST['precio']);
			$x->observacion = strip_tags($_POST['observacion']);
			$x->estado = strip_tags($_POST['estado']) === 'on' ? 1 : 0;
			$x->usuario = $this->acceso->Crypto('decrypt',$_SESSION['idusuario_ice']);
			$x->accion = strip_tags($_POST['accion']);
			
			if($x->accion == 'registrar' || $x->accion == 'modificar' || $x->accion == 'eliminar'){
				$resultado = $this->tamano->ActualizarTamanosProductos($x);
				foreach ($resultado as $r) {
					echo $r->resultado;
				}
			}else{
				echo 'No se enviaron los datos correctamente. Inténtelo nuevamente.';
			}
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }

    public function CargarTamanosProductos(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->tamano->CargarTamanosProductos() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.': '.$r->precio.'</opcion>';
			}
			echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
}
?>
