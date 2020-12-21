<?php
require 'controladores/acceso.controlador.php';
require 'modelos/reportes.modelo.php';
class ReportesControlador {
  	protected $reportes;
  	protected $acceso;
  	public function __CONSTRUCT(){
	    $this->reportes = new Reportes();
	    $this->acceso = new AccesoControlador();
  	}
  
  	public function FrmReportes(){
		if($this->acceso->VerificarAcceso()){
			require_once 'vistas/header.html';
			require_once 'vistas/reportes.html';
			require_once 'vistas/footer.html';
		}else{
      		$this->acceso->Login();
    	}
	}


	public function GenerarReportes(){
		if($this->acceso->VerificarAcceso()){
			$tipo_reporte = strip_tags($_POST['tipo_reporte']);
			$tipo_reporte_nombre = strip_tags($_POST['tipo_reporte_nombre']);
			$reporte = strip_tags($_POST['reporte']);
			$reporte_nombre = strip_tags($_POST['reporte_nombre']);
			$tipo_producto = $this->acceso->Crypto('decrypt',$_POST['tipo_producto']);
			$tipo_producto_nombre = strip_tags($_POST['tipo_producto_nombre']);
			$producto = $this->acceso->Crypto('decrypt',$_POST['producto']);
			$producto_nombre = strip_tags($_POST['producto_nombre']);
			$tamano_producto = $this->acceso->Crypto('decrypt',$_POST['tamano_producto']);
			$tamano_producto_nombre = strip_tags($_POST['tamano_producto_nombre']);
			$fechas = strip_tags($_POST['fechas']);
			$fechas_nombre = strip_tags($_POST['fechas_nombre']);
			$desde = strip_tags($_POST['desde']);
			$hasta = strip_tags($_POST['hasta']);
			$agrupar = strip_tags($_POST['agrupar']);
			$agrupar_nombre = strip_tags($_POST['agrupar_nombre']);
			
			if($tipo_reporte === '1'){
				$adicionales .= '<span><b>Tipo de reporte: </b>'.$tipo_reporte_nombre.'.</span><br>';
				$resultados = $this->ReporteGeneral($fechas,$desde,$hasta,$agrupar);
			}elseif($tipo_reporte === '3'){
				$adicionales .= '<span><b>Tipo de reporte: </b>'.$tipo_reporte_nombre.'.</span><br>';
				$resultados = $this->ReporteGlobal($fechas,$desde,$hasta,$agrupar);
			}elseif($tipo_reporte === '2'){
				$adicionales .= '<span><b>Tipo de reporte: </b>'.$tipo_reporte_nombre.'.</span><br>';
				if($reporte === '1'){
					$adicionales .= '<span><b>Reporte: </b>'.$reporte_nombre.'.</span><br>';
					if($tipo_producto !== '0' || $tipo_producto !== null){
						$adicionales .= '<span><b>Tipo de producto: </b>'.$tipo_producto_nombre.'.</span><br>';
					}else{
						$adicionales .= '<span>Debes seleccionar correctamente el tipo de producto.</span><br>';
					}
					$resultados = $this->ReporteEspecificoTipoProducto($tipo_producto,$fechas,$desde,$hasta,$agrupar);
				}else if($reporte === '2'){
					$adicionales .= '<span><b>Reporte: </b>'.$reporte_nombre.'.</span><br>';
					if($producto !== '0' || $producto !== null){
						$adicionales .= '<span><b>Producto: </b>'.$producto_nombre.'.</span><br>';
					}else{
						$adicionales .= '<span>Debes seleccionar correctamente el tipo de producto.</span><br>';
					}
					$resultados = $this->ReporteEspecificoProductosIndividuales($producto,$fechas,$desde,$hasta,$agrupar);
				}else if($reporte === '3'){
					$adicionales .= '<span><b>Reporte: </b>'.$reporte_nombre.'.</span><br>';
					if($tamano_producto !== '0' || $tamano_producto !== null){
						$adicionales .= '<span><b>Tamaño producto: </b>'.$tamano_producto_nombre.'.</span><br>';
					}else{
						$adicionales .= '<span>Debes seleccionar correctamente el tipo de producto.</span><br>';
					}
					$resultados = $this->ReporteEspecificoTamanoProductos($tamano_producto,$fechas,$desde,$hasta,$agrupar);
				}else if($reporte === '4'){
					$adicionales .= '<span><b>Reporte: </b>'.$reporte_nombre.'.</span><br>';
					$resultados = $this->ReporteEspecificoMasVendidos($fechas,$desde,$hasta,$agrupar);
				}else{
					$adicionales .= '<span>Debes seleccionar correctamente el reporte que deseas generar.</span><br>';
				}
			}else{
				$adicionales .= '<span>Debes seleccionar correctamente el tipo de reporte.</span><br>';
			}

			$datos = '
				<div class="card border" style="margin-top: 0px;">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								'.$adicionales.'
								<span><b>Fecha: </b>'.$_POST['fechas_nombre'].'.</span><br>
								'.($fechas === '1'?'<b>Desde: </b>'.$desde.', <b>Hasta: </b>'.$hasta.'.<br>':'').'
								<span><b>Agrupado por: </b>'.$_POST['agrupar_nombre'].'.</span><hr>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<span><b>Resultados: </b></span>
								'.$resultados.'
							</div>
						</div>
					</dib>
				</div>
			';

			echo json_encode(array('ok'=>true,'datos'=>$datos));
		}else{ 
	    	echo $this->acceso->MensajeNotificacion();
	    	$this->acceso->Principal();
	    }
    }
    private function ReporteGeneral($fechas,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;
    	$resultado = $this->reportes->ReporteGeneral($x);
	    if(is_array($resultado)){
	    	if(count($resultado) > 0){
		    	foreach ($resultado as $r) {
					$total_ingresos += $r->ingresos;
					$total_costos += $r->costos;
					$total_utilidad += $r->utilidad;
					$count++;
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$datos2 .= '
						<tr>
							<td>'.$fecha.'</td>
							<td>$'.number_format($r->ingresos,2).'</td>
							<td>$'.number_format($r->costos,2).'</td>
							<td>$'.number_format($r->utilidad,2).'</td>
							<td>'.number_format($r->margen*100,2).'%</td>
						</tr>
					';
				}
				$datos .= '		
					<div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
							<table class="table">
								<thead>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">INGRESOS</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">COSTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">UTILIDAD</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">% MARGEN</th>
								</thead>
							<tbody>'.$datos2.'
							</tbody>
								<tfooter>
									<th>TOTAL</th>
									<th>$'.number_format($total_ingresos,2).'</th>
									<th>$'.number_format($total_costos,2).'</th>
									<th>$'.number_format($total_utilidad,2).'</th>
									<th>'.number_format(($total_utilidad / $total_ingresos) * 100,2).'%</th>
								</tfooter>
							</table>
						</div>';
		    	return $datos;
		    }
    	}
    }
    private function ReporteGlobal($fechas,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;
    	$resultado_general = '';
    	$total_ventas_general = 0;
    	$total_ventas_helados = 0;
    	foreach ($this->reportes->CargarTamanosProductos() as $q) {
    		$i++;
    		$x->tamano_producto = $q->id;
    		$resultado = $this->reportes->ReporteEspecificoTamanoProductos($x);
		    if(is_array($resultado)){
		    	if(count($resultado) > 0){
			    	foreach ($resultado as $r) {
						$fecha = $this->VerFecha($x->agrupar,$r->fecha);
						$pro = $this->VerProductoTamano2($r->producto,$r->costo,$r->precio);
						$total_cantidad += $r->cantidad;
						$total_precios += $r->precio;
						$total_ventas += $r->venta;
						$datos2 .= '
							<tr>
								<td style="vertical-align: middle;">'.$fecha.'</td>
								<td>'.json_decode($pro)->producto.'</td>
								<td>'.number_format($r->cantidad).'</td>
								<td>$'.number_format($r->venta,2).'</td>
							</tr>
						';
					}
					$datos .= '
						<div class="card">
						    <div class="card-header" id="heading'.$i.'" style="border: 1px solid #dee2e6 !important;    background: #fff;box-shadow: none;width: 100.5%;margin-left: -3px;border-radius: 0;padding-top:0;padding-bottom:0;cursor:pointer" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">
						        <span style="margin-top: 12px;position: absolute;">
						          <b>'.$q->nombre.' ('.$total_cantidad.')</b>
						        </span>
					            <span class="nav-link pull-right" style="margin-top:7px">Total: <b>$'.number_format($total_ventas,2).'</b></span>
			                </div>
						    <div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordionExample1">
						      <div class="card-body">
						        <div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
									<table class="table">
										<thead>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
										</thead>
									<tbody>'.$datos2.'
									</tbody>
										<tfooter>
											<th>TOTAL</th>
											<th>--</th>
											<th>'.number_format($total_cantidad).'</th>
											<th>$'.number_format($total_ventas,2).'</th>
										</tfooter>
									</table>
								</div>
						      </div>
						    </div>
						</div>
					';
					$total_ventas_helados += $total_ventas;
			    	$dat .= $datos;
			    	$datos = '';$datos2 = '';
			    	$total_cantidad = 0;
					$total_precios = 0;
					$total_ventas = 0;
			    }
	    	}
    	}
    	$resultado_general .= '
    	<h5>HELADOS:</h5>
    	<div class="accordion" id="accordionExample1">
    		'.$dat.'
    	</div>';
    	
    	$dat = '';
    	$x->tipo_producto = 'RECOMENDADO';
		$resultado2 = $this->reportes->ReporteEspecificoOtros($x);
	    $i++;
	    if(is_array($resultado2)){
	    	if(count($resultado2) > 0){
		    	foreach ($resultado2 as $r) {
		    		#var_dump($r);
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$total_cantidad += $r->cantidad;
					$total_precios += $r->precio;
					$total_ventas += $r->venta;
					$datos2 .= '
						<tr>
							<td style="vertical-align: middle;">'.$fecha.'</td>
							<td>'.$r->producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->venta,2).'</td>
						</tr>
					';
				}
				$datos .= '
					<div class="card">
					    <div class="card-header" id="heading'.$i.'" style="border: 1px solid #dee2e6 !important;    background: #fff;box-shadow: none;width: 100.5%;margin-left: -3px;border-radius: 0;padding-top:0;padding-bottom:0;cursor:pointer" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">
					        <span style="margin-top: 12px;position: absolute;">
					          <b>Recomendados ('.$total_cantidad.')</b>
					        </span>
				            <span class="nav-link pull-right" style="margin-top:7px">Total: <b>$'.number_format($total_ventas,2).'</b></span>
		                </div>
					    <div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordionExample1">
					      <div class="card-body">
					        <div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
								<table class="table">
									<thead>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
									</thead>
								<tbody>'.$datos2.'
								</tbody>
									<tfooter>
										<th>TOTAL</th>
										<th>--</th>
										<th>'.number_format($total_cantidad).'</th>
										<th>$'.number_format($total_ventas,2).'</th>
									</tfooter>
								</table>
							</div>
					      </div>
					    </div>
					</div>
				';
				$total_ventas_helados += $total_ventas;
		    	$dat .= $datos;
		    	$datos = '';$datos2 = '';
		    	$total_cantidad = 0;
				$total_precios = 0;
				$total_ventas = 0;
		    }
    	}

    	$x->tipo_producto = 'EXTRAS';
		$resultado3 = $this->reportes->ReporteEspecificoOtros($x);
	    $i++;
	    if(is_array($resultado3)){
	    	if(count($resultado3) > 0){
		    	foreach ($resultado3 as $r) {
		    		#var_dump($r);
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$total_cantidad += $r->cantidad;
					$total_precios += $r->precio;
					$total_ventas += $r->venta;
					$datos2 .= '
						<tr>
							<td style="vertical-align: middle;">'.$fecha.'</td>
							<td>'.$r->producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->venta,2).'</td>
						</tr>
					';
				}
				$datos .= '
					<div class="card">
					    <div class="card-header" id="heading'.$i.'" style="border: 1px solid #dee2e6 !important;    background: #fff;box-shadow: none;width: 100.5%;margin-left: -3px;border-radius: 0;padding-top:0;padding-bottom:0;cursor:pointer" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">
					        <span style="margin-top: 12px;position: absolute;">
					          <b>Solo extras ('.$total_cantidad.')</b>
					        </span>
				            <span class="nav-link pull-right" style="margin-top:7px">Total: <b>$'.number_format($total_ventas,2).'</b></span>
		                </div>
					    <div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordionExample2">
					      <div class="card-body">
					        <div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
								<table class="table">
									<thead>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
										<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
									</thead>
								<tbody>'.$datos2.'
								</tbody>
									<tfooter>
										<th>TOTAL</th>
										<th>--</th>
										<th>'.number_format($total_cantidad).'</th>
										<th>$'.number_format($total_ventas,2).'</th>
									</tfooter>
								</table>
							</div>
					      </div>
					    </div>
					</div>
				';
				$total_ventas_helados += $total_ventas;
		    	$dat .= $datos;
		    	$datos = '';$datos2 = '';
		    	$total_cantidad = 0;
				$total_precios = 0;
				$total_ventas = 0;
		    }
    	}
    	$resultado_general .= '
    	<div class="accordion" id="accordionExample2">
    		'.$dat.'
    		<span class="pull-right" style="margin-right:28px"><b>TOTAL VENTAS HELADOS: $'.number_format($total_ventas_helados,2).'</b></span><br><hr>
    	</div>';
    	$dat = '';
    	
    	foreach ($this->reportes->CargarProductosAdicionales() as $q) {
    		$x->idproducto = $q->id;
    		$x->tipo_producto = 'ADICIONALES';
			$i++;
	    	$resultado6 = $this->reportes->ReporteEspecificoOtrosAdicionales($x);
		    if(is_array($resultado6)){
		    	if(count($resultado6) > 0){
			    	foreach ($resultado6 as $r) {
			    		#var_dump($r);
						$fecha = $this->VerFecha($x->agrupar,$r->fecha);
						$total_cantidad += $r->cantidad;
						$total_precios += $r->precio;
						$total_ventas += $r->venta;
						$datos2 .= '
							<tr>
								<td style="vertical-align: middle;">'.$fecha.'</td>
								<td>'.$r->producto.'</td>
								<td>'.number_format($r->cantidad).'</td>
								<td>$'.number_format($r->venta,2).'</td>
							</tr>
						';
					}
					$datos .= '
						<div class="card">
						    <div class="card-header" id="heading'.$i.'" style="border: 1px solid #dee2e6 !important;    background: #fff;box-shadow: none;width: 100.5%;margin-left: -3px;border-radius: 0;padding-top:0;padding-bottom:0;cursor:pointer" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">
						        <span style="margin-top: 12px;position: absolute;">
						          <b>'.$q->nombre.' ('.$total_cantidad.')</b>
						        </span>
					            <span class="nav-link pull-right" style="margin-top:7px">Total: <b>$'.number_format($total_ventas,2).'</b></span>
			                </div>
						    <div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordionExample3">
						      <div class="card-body">
						        <div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
									<table class="table">
										<thead>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
											<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
										</thead>
									<tbody>'.$datos2.'
									</tbody>
										<tfooter>
											<th>TOTAL</th>
											<th>--</th>
											<th>'.number_format($total_cantidad).'</th>
											<th>$'.number_format($total_ventas,2).'</th>
										</tfooter>
									</table>
								</div>
						      </div>
						    </div>
						</div>
					';
					$total_ventas_adicionales += $total_ventas;
			    	$dat .= $datos;
			    	$datos = '';$datos2 = '';
			    	$total_cantidad = 0;
					$total_precios = 0;
					$total_ventas = 0;
			    }
	    	}
    	}    	
    	
    	$resultado_general .= '
    	<h5>ADICIONALES:</h5>
    	<div class="accordion" id="accordionExample3">
    		'.$dat.'
    		<span class="pull-right" style="margin-right:29px"><b>TOTAL VENTAS ADICIONALES: $'.number_format($total_ventas_adicionales,2).'</b></span><br><hr>
    		<span class="pull-right" style="margin-right:29px;font-size:17px;"><b>TOTAL VENTAS GENERALES: $'.number_format($total_ventas_adicionales + $total_ventas_helados,2).'</b></span>
    	</div>';
    	return $resultado_general;
	}
    private function ReporteEspecificoTipoProducto($tipoproducto,$fecha,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->tipo_producto = $tipoproducto;
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;
    	$resultado = $this->reportes->ReporteEspecificoTipoProducto($x);
	    if(is_array($resultado)){
	    	if(count($resultado) > 0){
		    	foreach ($resultado as $r) {
					$total_cantidad += $r->cantidad;
					$total_costos += $r->costos;
					$total_ventas += $r->ventas;
					$total_ganancias += $r->ganancia;
					$count++;
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$datos2 .= '
						<tr>
							<td>'.$fecha.'</td>
							<td>'.$r->tipo_producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->costos,2).'</td>
							<td>$'.number_format($r->ventas,2).'</td>
							<td>$'.number_format($r->ganancia,2).'</td>
						</tr>
					';
				}
				$datos .= '
					<div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
							<table class="table">
								<thead>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">TIPO PRODUCTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">COSTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">GANANCIA</th>
								</thead>
							<tbody>'.$datos2.'
							</tbody>
								<tfooter>
									<th>TOTAL</th>
									<th>'.$r->tipo_producto.'</th>
									<th>'.number_format($total_cantidad).'</th>
									<th>$'.number_format($total_costos,2).'</th>
									<th>$'.number_format($total_ventas,2).'</th>
									<th>$'.number_format($total_ganancias,2).'</th>
								</tfooter>
							</table>
						</div>';
		    	return $datos;
		    }
    	}
    }
    private function ReporteEspecificoProductosIndividuales($producto,$fechas,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->producto = $producto;
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;

    	#echo $x->desde;
    	$resultado = $this->reportes->ReporteEspecificoProductosIndividuales($x);
	    if(is_array($resultado)){
	    	if(count($resultado) > 0){
		    	foreach ($resultado as $r) {
					$total_cantidad += $r->cantidad;
					$total_costos += $r->costo;
					$total_precios += $r->precio;
					$total_ventas += $r->venta;
					$total_ganancias += $r->ganancia;
					$count++;
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$datos2 .= '
						<tr>
							<td>'.$fecha.'</td>
							<td>'.$r->producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->costo,2).'</td>
							<td>$'.number_format($r->precio,2).'</td>
							<td>$'.number_format($r->ganancia,2).'</td>
						</tr>
					';
				}
				$datos .= '
				<div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
							<table class="table">
								<thead>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">PRODUCTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">COSTOS('.number_format($r->costo_real,2).')</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">PRECIOS('.number_format($r->precio_real,2).')</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">GANANCIAS</th>
								</thead>
							<tbody>'.$datos2.'
							</tbody>
								<tfooter>
									<th>TOTAL</th>
									<th>'.$r->producto.'</th>
									<th>'.number_format($total_cantidad).'</th>
									<th>$'.number_format($total_costos,2).'</th>
									<th>$'.number_format($total_precios,2).'</th>
									<th>$'.number_format($total_ganancias,2).'</th>
								</tfooter>
							</table>
						</div>';
		    	return $datos;
		    }
    	}
		return $datos;
    }
    private function ReporteEspecificoTamanoProductos($tamano_producto,$fechas,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->tamano_producto = $tamano_producto;
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;

    	$resultado = $this->reportes->ReporteEspecificoTamanoProductos($x);
	    if(is_array($resultado)){
	    	if(count($resultado) > 0){
		    	foreach ($resultado as $r) {
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$pro = $this->VerProductoTamano($r->producto,$r->costo,$r->precio);
					$total_cantidad += $r->cantidad;
					$total_costos += number_format($r->costo_total+(json_decode($pro)->costo * $r->cantidad),2);
					$total_ventas += $r->venta;
					$total_ganancias += ($r->venta - ($r->costo_total+(json_decode($pro)->costo * $r->cantidad)));

					$datos2 .= '
						<tr>
							<td style="vertical-align: middle;">'.$fecha.'</td>
							<td>'.json_decode($pro)->producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->costo_total+(json_decode($pro)->costo * $r->cantidad),2).'</td>
							<td>$'.number_format($r->venta,2).'</td>
							<td>$'.number_format(($r->venta - ($r->costo_total+(json_decode($pro)->costo * $r->cantidad))),2).'</td>
						</tr>
					';
				}
				$datos .= '
				<div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
							<table class="table">
								<thead>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">COSTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">GANANCIA</th>
								</thead>
							<tbody>'.$datos2.'
							</tbody>
								<tfooter>
									<th>TOTAL</th>
									<th>--</th>
									<th>'.number_format($total_cantidad).'</th>
									<th>$'.number_format($total_costos,2).'</th>
									<th>$'.number_format($total_ventas,2).'</th>
									<th>$'.number_format($total_ganancias,2).'</th>
								</tfooter>
							</table>
						</div>';
		    	return $datos;
		    }
    	}
		return $datos;
    }
    private function ReporteEspecificoMasVendidos($fechas,$desde,$hasta,$agrupar){
    	$x = new Reportes();
    	$x->fechas = $fechas;
    	$x->desde = date("Y-m-d H:i:s", strtotime($desde));
    	$x->hasta = date("Y-m-d H:i:s", strtotime($hasta));
    	$x->agrupar = $agrupar;

    	$resultado = $this->reportes->ReporteEspecificoMasVendidos($x);
	    if(is_array($resultado)){
	    	if(count($resultado) > 0){
		    	foreach ($resultado as $r) {
					$fecha = $this->VerFecha($x->agrupar,$r->fecha);
					$pro = $this->VerProducto($r->idproducto,$r->producto,$r->costo,$r->precio);
					$total_cantidad += $r->cantidad;
					$total_costos += number_format($r->costo_total+(json_decode($pro)->costo * $r->cantidad),2);
					$total_ventas += $r->venta;
					$total_ganancias += ($r->venta - ($r->costo_total+(json_decode($pro)->costo * $r->cantidad)));
					$datos2 .= '
						<tr>
							<td style="vertical-align: middle;">'.$fecha.'</td>
							<td>'.json_decode($pro)->producto.'</td>
							<td>'.number_format($r->cantidad).'</td>
							<td>$'.number_format($r->costo_total+(json_decode($pro)->costo * $r->cantidad),2).'</td>
							<td>$'.number_format($r->venta,2).'</td>
							<td>$'.number_format(($r->venta - ($r->costo_total+(json_decode($pro)->costo * $r->cantidad))),2).'</td>
						</tr>
					';
				}
				$datos .= '
				<div class="table-responsive" style="overflow-y: auto;max-height: 350px;">
							<table class="table">
								<thead>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">FECHA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;width: 50%;">DETALLE</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">CANTIDAD</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">COSTO</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">VENTA</th>
									<th style="z-index: 1;top: -1px;position: sticky;background-color: #eeeeee;">GANANCIA</th>
								</thead>
							<tbody>'.$datos2.'
							</tbody>
								<tfooter>
									<th>TOTAL</th>
									<th>--</th>
									<th>'.number_format($total_cantidad).'</th>
									<th>$'.number_format($total_costos,2).'</th>
									<th>$'.number_format($total_ventas,2).'</th>
									<th>$'.number_format($total_ganancias,2).'</th>
								</tfooter>
							</table>
						</div>';
		    	return $datos;
		    }
    	}
		return $datos;
    }
    private function VerFecha($agrupar,$fecha){
    	if($agrupar === '2'){
			return strtoupper(date("Y-m-d H:00:00", strtotime($fecha)));
		}elseif($agrupar === '3'){
			return strtoupper(date("Y-m-d", strtotime($fecha)));
		}elseif($agrupar === '4'){
			return strtoupper(date("Y-m-d", strtotime($fecha)));
		}elseif($agrupar === '5'){
			return strtoupper(date("Y-m-00", strtotime($fecha)));
		}else{
			return strtoupper(date("Y-m-d H:i:s", strtotime($fecha)));
		}
    }
    private function VerProducto($id,$producto,$costo,$precio){
    	$costo_ = 0;
		$precio_ = 0;
    	if($id === 0){
    		if(json_encode($producto)){
				foreach (json_decode('['.$producto.']') as $r) {
		     		$dato .= '
		     			<b>Producto personalizado:</b><br>
     					<b>Tamaño producto: </b>'.$r->tamano_producto.' (C: $'.number_format($costo,2).') | (P: $'.number_format($precio,2).').<br>';
		     		if($r->sabores !== NULL){
		     			$dato .= '<li><b>Sabores: </b>';
			     		foreach ($r->sabores as $s) {
			     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).'+) | (P: $'.number_format($s->precio,2).'+)':'').', ';
			     			if($s->extra){
			     				$costo_ = $costo_ + $s->costo;
			     				$precio_ = $precio_ + $s->precio;
			     			}
			     		}
			     		$dato = substr($dato, 0, -2).'.<br>';
			     		$dato .= '</li>';
			     	}
		     		if($r->ingredientes !== NULL){
		     			$dato .= '<li><b>Ingredientes: </b>';
		     			foreach ($r->ingredientes as $s) {
			     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
			     			if($s->extra){
			     				$costo_ = $costo_ + $s->costo;
			     				$precio_ = $precio_ + $s->precio;
			     			}
			     		}
			     		$dato = substr($dato, 0, -2).'.<br>';
			     		$dato .= '</li>';
		     		}
		     		$dato .= '</li>';
		     		if($r->salsas !== NULL){
		     			$dato .= '<li><b>Salsas: </b>';
			     		foreach ($r->salsas as $s) {
			     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
			     			if($s->extra){
			     				$costo_ = $costo_ + $s->costo;
			     				$precio_ = $precio_ + $s->precio;
			     			}
			     		}
			     		$dato = substr($dato, 0, -2).'.<br>';
		     			$dato .= '</li>';
		     		}
		     	}
			}
			return json_encode(array('producto'=>$dato,'costo'=>$costo_,'precio',$precio_));
		}else{
    		return json_encode(array('producto'=>$producto,'costo'=>$costo_,'precio',$precio_));
    	}
    }
    private function VerProductoTamano($producto,$costo,$precio){
    	$costo_ = 0;
		$precio_ = 0;
    	if(json_encode($producto)){
			foreach (json_decode('['.$producto.']') as $r) {
	     		$dato .= '
	     			<b>Producto personalizado:</b><br>
	     			<b>Tamaño producto: </b>'.$r->tamano_producto.' (C: $'.number_format($costo,2).') | (P: $'.number_format($precio,2).').<br>';
	     		if($r->sabores !== NULL){
	     			$dato .= '<li><b>Sabores: </b>';
	     			foreach ($r->sabores as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).'+) | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
		     		$dato .= '</li>';
	     		}
	     		if($r->ingredientes !== NULL){
	     			$dato .= '<li><b>Ingredientes: </b>';
		     		foreach ($r->ingredientes as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
		     		$dato .= '</li>';
	     		}
	     		if($r->salsas !== NULL){
	     			$dato .= '<li><b>Salsas: </b>';
		     		foreach ($r->salsas as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
	     		}
	     	}
		}
		return json_encode(array('producto'=>$dato,'costo'=>$costo_,'precio',$precio_));
	}
	private function VerProductoTamano2($producto,$costo,$precio){
    	$costo_ = 0;
		$precio_ = 0;
    	if(json_encode($producto)){
			foreach (json_decode('['.$producto.']') as $r) {
	     		$dato .= '
	     			<b>Producto personalizado:</b><br>';
	     		if($r->sabores !== NULL){
	     			$dato .= '<li><b>Sabores: </b>';
	     			foreach ($r->sabores as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).'+) | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
		     		$dato .= '</li>';
	     		}
	     		if($r->ingredientes !== NULL){
	     			$dato .= '<li><b>Ingredientes: </b>';
		     		foreach ($r->ingredientes as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
		     		$dato .= '</li>';
	     		}
	     		if($r->salsas !== NULL){
	     			$dato .= '<li><b>Salsas: </b>';
		     		foreach ($r->salsas as $s) {
		     			$dato .= $s->nombre.($s->extra?' (C: $'.number_format($s->costo,2).') | (P: $'.number_format($s->precio,2).'+)':'').', ';
		     			if($s->extra){
		     				$costo_ = $costo_ + $s->costo;
		     				$precio_ = $precio_ + $s->precio;
		     			}
		     		}
		     		$dato = substr($dato, 0, -2).'.<br>';
	     		}
	     	}
		}
		return json_encode(array('producto'=>$dato,'costo'=>$costo_,'precio',$precio_));
	}
}
?>
