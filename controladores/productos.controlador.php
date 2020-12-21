<?php
require 'controladores/acceso.controlador.php';
require 'modelos/productos.modelo.php';
class ProductosControlador {
  	protected $producto;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->producto = new Productos();
	    $this->acceso = new AccesoControlador();
  	}
  
  	public function FrmProductos(){
		if($this->acceso->VerificarAcceso()){
			if($_SESSION['rol_ice'] === 'ADMINISTRADOR' || $_SESSION['rol_ice'] === 'SUPER USUARIO'){
				require_once 'vistas/header.html';
				require_once 'vistas/productos.html';
				require_once 'vistas/footer.html';
			}else{
				$this->acceso->Principal();
			}
		}else{
      		$this->acceso->Login();
    	}
	}

	public function ListarProductos(){
		if($this->acceso->VerificarAcceso()){
    		$id = 1;
	    	foreach ($this->producto->ListarProductos() as $r) {
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
		          	'tipo_producto'=> $r->tipo_producto,
		          	'categoria'=> $r->categoria,
		          	'sabores'=> $this->Comprimir($r->sabores),
		          	'ingredientes'=> $this->Comprimir($r->ingredientes),
		          	'salsas'=> $this->Comprimir($r->salsas),
		          	'creado'=> $r->creado,
				    'modificado'=> $r->modificado,
				    'observacion'=> $r->observacion, 
		          	'estado' => $estado,
		          	'accion' => '
			        	<div class="dropdown">
						  <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">more_vert</i>
						  </button>
						  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						    <a class="dropdown-item editarProducto" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="modificar" data-toggle="modal" data-target="#ActualizarProductos"><i class="material-icons">edit</i> Modificar</a>
						    <a class="dropdown-item eliminarProducto" href="#" id="'.$this->acceso->Crypto('encrypt',$r->id).'" accion="eliminar" data-target="#exampleModal"><i class="material-icons">remove_circle_outline</i> Eliminar</a>
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

	public function CargarTiposProductos(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarTiposProductos() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->tipo.'</opcion>';
			}
 	        echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }

	public function ActualizarProductos(){
		if($this->acceso->VerificarAcceso()){
			
			$x = new Productos();
			$x->id = $this->acceso->Crypto('decrypt',$_POST['id']);
			$x->tipo_producto = $this->acceso->Crypto('decrypt',$_POST['tipo_producto']);
			$x->nombre = strip_tags($_POST['nombre']);
			$x->costo = strip_tags($_POST['costo']);
			$x->precio = strip_tags($_POST['precio']);
			$x->observacion = strip_tags($_POST['observacion']);
			$x->estado = strip_tags($_POST['estado']);
			//$x->usuario = $this->acceso->Crypto('decrypt',$_SESSION['idusuario']);
			$x->accion = strip_tags($_POST['accion']);
			$x->usuario = 1;
			
			$x->categoria = $this->acceso->Crypto('decrypt',$_POST['categoria']);
			$x->sabores = rtrim($this->DesComprimir($_POST['saboress']),',');
			$x->ingredientes = rtrim($this->DesComprimir($_POST['ingredientess']),',');
			$x->salsas = rtrim($this->DesComprimir($_POST['salsass']),',');

			if($x->accion == 'registrar' || $x->accion == 'modificar' || $x->accion == 'eliminar'){
				$resultado = $this->producto->ActualizarProductos($x);
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

	public function CargarProductos(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			$productos = array();
			$nuevos = array();
			#$datos .= '<optgroup label="Picnic">';
			// foreach ($this->producto->CargarProductos() as $r) {
			// 	$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			// }
			foreach ($this->producto->CargarProductos() as $e) {
				if(!in_array(array('tipo_producto'=>$e->tipo_producto), $productos)){
					array_push($productos, array('tipo_producto'=>$e->tipo_producto));
				}
				array_push($nuevos, array('tipo_producto'=>$e->tipo_producto,'producto'=>'<option value="'.$this->acceso->Crypto('encrypt',$e->id).'" '.($e->categoria?'data-subtext="'.$e->categoria.'"':'').'>'.$e->nombre.' ($'.$e->precio.')</option>'));

			}
			foreach ($productos as $key) {
				$datos .= '<optgroup label="'.$key['tipo_producto'].'">';
				foreach ($nuevos as $key2) {
					if ($key2['tipo_producto'] == $key['tipo_producto']) {
						$datos .= $key2['producto'];
					}
				}
				$datos .= '</optgroup>';
			}
			$datos .= '</optgroup>';
			echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}

	public function CargarProductosExtras(){

		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarProductosExtras() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio,
						'tipo_producto' => "extras",
						'categoria' => $r->categoria
					); 
					 
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}

	public function CargarTiposProductosJson(){

		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarTiposProductos() as $r) {
				$data[] = array(
					'id'=> $this->acceso->Crypto('encrypt',$r->id),
					'tipo'=> $r->tipo,
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
	
	public function CargarSaboresJson(){
		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarSabores() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio,
						'tipo'=> "sabor",
						"tipo_producto" => "personalizado"
					);
					
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}

	public function CargarSalsasJson(){
		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarSalsas() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio,
						'tipo'=> "salsa",
						'tipo_producto' => "personalizado"
					);
					
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
	
	public function CargarIngredientesJson(){
		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarIngredientes() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio,
						'tipo'=> "ingrediente",
						"tipo_producto" => "personalizado"
					);
					
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}

	public function CargarRecomendadosJson(){
		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarRecomendados() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'sabores' => $this->ConcatenarPersonalizados($r->sabores),
						'ingredientes' => $this->ConcatenarPersonalizados($r->ingredientes),
						'salsas' => $this->ConcatenarPersonalizados($r->salsas),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio ,
						"tipo_producto" => "recomendado"
					);
					 
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}
	
	public function CargarAdicionalesJson(){

		if($this->acceso->VerificarAcceso()){

			foreach ($this->producto->CargarAdicionales() as $r) {
				if($r->estado){

					$data[] = array(
						'id'=> $this->acceso->Crypto('encrypt',$r->id),
						'nombre'=> $r->nombre,
						'precio'=> $r->precio,
						'tipo_producto' => "adicionales"
					); 
					 
				}else{
					
				}
		    }
			if(!$data) {$data = '';};
			$results = array("data"	=>	$data);
			echo json_encode($results);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}
	
 
	public function CargarSabores(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarSabores() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
			echo json_encode($datos); 
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
	}
	
    public function CargarIngredientes(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarIngredientes() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
			echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    public function CargarSalsas(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarSalsas() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
			echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    public function CargarCategorias(){
		if($this->acceso->VerificarAcceso()){
			$datos = '<option value="0">Seleccionar</opcion>';
			foreach ($this->producto->CargarCategorias() as $r) {
				$datos .= '<option value="'.$this->acceso->Crypto('encrypt',$r->id).'">'.$r->nombre.'</opcion>';
			}
			echo json_encode($datos);
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    private function Comprimir($varios){
		if(json_encode($varios)){
			foreach ((array)json_decode($varios) as $r) {
	     		$dato[] = array('id'=>$this->acceso->Crypto('encrypt',$r->id),'nombre'=>$r->nombre);
	     	}
		}
		return $dato;
	}
	private function DesComprimir($varios){
		foreach (explode(",", strip_tags($varios)) as $r) {
			$dato .= $this->acceso->Crypto('decrypt',$r).',';
		}
		return $dato;	 
	}
	private function ConcatenarPersonalizados($varios){
		if(json_encode($varios)){
			foreach ((array)json_decode($varios) as $r) {
	     		$dato .= $r->nombre.", ";
	     	}
		}
		return substr($dato, 0, -2);
	}

	public function CargarProductosPorTipo(){
		if($this->acceso->VerificarAcceso()){
			$x = new Productos();
			$x->tipo_producto = $this->acceso->Crypto('decrypt',$_POST['tipo_producto']);
			foreach ($this->producto->CargarProductosPorTipo($x) as $r) {
				$datos .= '
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
			            <div class="card">
			              <div class="card-body card-recomendado">
			                <h5 class="card-title card-title-recomendado">'.$r->nombre.'</h5>
			                <p class="card-text">
			                  <b>Sabores: </b>'.$this->ConcatenarPersonalizados($r->sabores).'.<br>
			                  <b>Ingredientes: </b>'.$this->ConcatenarPersonalizados($r->ingredientes).'.<br>
			                  <b>Salsas: </b>'.$this->ConcatenarPersonalizados($r->salsas).'.<br>
			                  <span class="text-muted"><b>Precio: </b> $'.$r->precio.'</span>
			                  <hr class="hr-recomendado">
			                </p>
			              </div>
			              <div class="card-footer">
			                <div class="row">
			                  <div class="col-lg-12">
			                    <center>
			                      <div class="btn-group dropup pull-right">
			                        <button type="button" class="btn btn-link"><i class="material-icons">remove</i></button>
			                        <button type="button" class="btn btn-primary"><i class="material-icons">add</i></button>
			                      </div>
			                    </center>
			                  </div>
			                </div>
			              </div>
			            </div>
			          </div>
				';
			}
 	        echo json_encode(array('tipo_producto'=>$x->tipo_producto,'datos'=>$datos));
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
}
?>
