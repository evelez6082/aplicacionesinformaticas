<?php
require 'controladores/acceso.controlador.php';
require 'modelos/otros.modelo.php';
class OtrosControlador {
  	protected $otro;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->producto = new Otros();
	    $this->acceso = new AccesoControlador();
  	}

	public function CargarProvincias(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarProvincias() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
 	       echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    public function CargarCantones(){
		if($this->acceso->VerificarAcceso()){
			$x = new Otros();
			$x->idprovincia = $this->acceso->Crypto('decrypt',$_POST['idprovincia']);
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarCantones($x) as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
 	        echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    public function CargarParroquias(){
		if($this->acceso->VerificarAcceso()){
			$x = new Otros();
			$x->idcanton = $this->acceso->Crypto('decrypt',$_POST['idcanton']);
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarParroquias($x) as $r) {
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
