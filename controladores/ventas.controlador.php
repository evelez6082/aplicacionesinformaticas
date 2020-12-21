<?php
require 'controladores/acceso.controlador.php';
require 'modelos/ventas.modelo.php';
class VentasControlador {
  	protected $venta;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->venta = new Ventas();
	    $this->acceso = new AccesoControlador();
  	}
  
  	public function FrmVentas(){
		if($this->acceso->VerificarAcceso()){
			require_once 'vistas/header.html';
			require_once 'vistas/ventas.html';
			require_once 'vistas/footer.html';
		}else{
      		$this->acceso->Login();
    	}
	}
 
	public function ListarVentas(){
		if($this->acceso->VerificarAcceso()){
    		$x = new Ventas();
	      	$id = 1;
	    	foreach ($this->venta->ListarVentas($x) as $r) {
				if($r->estado){
					$estado = '<span class="badge badge-success">ACTIVO</span>';
				}else{
					$estado = '<span class="badge badge-default"> INACTIVO</span>';
				}
		        $data[] = array(
						'num'=> $id++,
						'numero'=> $r->numero, 
						'idcliente' => $this->acceso->Crypto('encrypt',$r->idcliente),
						'cliente'=> $r->cliente,
						'idvendedor' => $this->acceso->Crypto('encrypt',$r->idvendedor),
						'vendedor'=> $r->vendedor,
			          	'total'=> $r->total_venta,
			          	'pagado'=> $r->valor_pago,
			          	'cambio'=> $r->cambio,
			          	'creado'=> $r->creado,
			          	'modificado'=> $r->modificado,
			          	'estado' => $estado,
			          	'accion' => '
				        	<div class="dropdown">
							  <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">more_vert</i>
							  </button>
							  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						     <a class="dropdown-item eliminarVenta" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="eliminar"><i class="material-icons">remove_circle_outline</i> Cancelar</a>
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
	public function ActualizarVentas(){
		if($this->acceso->VerificarAcceso()){
    		$x = new Ventas();
			 
			$x->cliente =  $this->acceso->Crypto('decrypt',$_POST['cliente']);
	    	$x->vendedor = $this->acceso->Crypto('decrypt',$_SESSION['idusuario_ice']);
    		$x->forma_pago = $this->acceso->Crypto('decrypt',$_POST['forma_pago']);
	      	$x->tipo_documento = 1;
    		$x->productos = $_POST['venta'];
	    	$x->valor_pago = strip_tags($_POST['forma_cantidad']);
	    	$x->usuario = $this->acceso->Crypto('decrypt',$_SESSION['idusuario_ice']);
	      	$x->accion = json_decode($x->productos)->ACCION;
	    	
	    	if(count(json_decode($x->productos)->RECOMENDADO) > 0){
		    	foreach (json_decode($x->productos)->RECOMENDADO as $key => $r) {
	    			$valores =  json_encode(array(
						'tipo_producto' => 'RECOMENDADO',
						'idproducto' => $this->acceso->Crypto('decrypt',$r->id),
						'precio' => $r->precio,
						'cantidad' => $r->cantidad,
						'subtotal' => ($r->precio*$r->cantidad),
					));
					$datos .= $valores.',';
				}
		    }

	    	if(count(json_decode($x->productos)->PERSONALIZADO) > 0){
		    	foreach (json_decode($x->productos)->PERSONALIZADO as $key => $s) {
					foreach ($s->detalle as $t) {
						$adicionales =  json_encode(array(
							'idproducto' => $this->acceso->Crypto('decrypt',$t->id),
							'categoria' => $t->tipo, 
							'extra' => $t->extra, 
							'precio' => $t->precio,
							'cantidad' => $t->cantidad,
							'subtotal' => ($t->precio*$t->cantidad),
						));
						$a .= $adicionales.',';
					}
					$a = '['.rtrim($a,',').']';
					$personalizados =  json_encode(array(
						'cantidad' => $s->cantidad, 
						'precio' => $s->precio,
						'subtotal' => ($s->precio*$s->cantidad),
						'tamano_producto' => $this->acceso->Crypto('decrypt',$s->tamano),
						'datos' => $a
					));
					$a = '';
					$b .= $personalizados.',';
				}
				$finales = '['.rtrim($b,',').']';
				$valores2 =  json_encode(array(
					'tipo_producto' => 'PERSONALIZADO',
					'productos' => $finales,
				));
				$datos .= $valores2.',';
			}

			if(count(json_decode($x->productos)->ADICIONAL) > 0){
				foreach (json_decode($x->productos)->ADICIONAL as $key => $t) {
		    		$valores =  json_encode(array(
						'tipo_producto' => 'ADICIONAL',
						'idproducto' => $this->acceso->Crypto('decrypt',$t->id),
						'precio' => $t->precio,
						'cantidad' => $t->cantidad,
						'subtotal' => ($t->precio*$t->cantidad),
					));
					$datos .= $valores.',';
				}
		    }
		    if(count(json_decode($x->productos)->EXTRA) > 0){
				foreach (json_decode($x->productos)->EXTRA as $key => $t) {
		    		$valores =  json_encode(array(
						'tipo_producto' => 'EXTRA',
						'idproducto' => $this->acceso->Crypto('decrypt',$t->id),
						'precio' => $t->precio,
						'cantidad' => $t->cantidad,
						'subtotal' => ($t->precio*$t->cantidad),
					));
					$datos .= $valores.',';
				}
		    }
	    	$x->productos = '['.rtrim($datos,',').']';
	    	
	    	#var_dump($x);
	    	if($x->accion == 'registrar' || $x->accion == 'modificar' || $x->accion == 'eliminar'){
				foreach ($this->venta->ActualizarVentas($x) as $r) {
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
	public function CargarFormasPago(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->venta->CargarFormasPago() as $r) {
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
