import {SnackAlerta} from './funcionesGlobales.js'; 
 
$(function(){
  let datos = new FormData();
  datos.append('id','');

  $("#tipo_producto").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');
  
  fetch('cargartiposproductos',{
    method: 'POST',
    body: datos
  }).then(response => response.json()
  ).then(data => {
    $("#tipo_producto").html(data).prop('disabled',false).selectpicker('refresh');
  }).catch(error => {
    $("#tipo_producto").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });

  fetch('cargarproductos',{
    method: 'POST',
    body: datos
  }).then(response => response.json()
  ).then(data => {
    $("#producto").html(data).prop('disabled',false).selectpicker('refresh');
  }).catch(error => {
    $("#producto").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });

  fetch('cargartamanos',{
    method: 'POST',
    body: datos
  }).then(response => response.json()
  ).then(data => {
    $("#tamano_producto").html(data).prop('disabled',false).selectpicker('refresh');
  }).catch(error => {
    $("#tamano_producto").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });
});

$('#tipo_reporte').on('change',function() {
  if($(this).val() === '1' || $(this).val() === '3'){
    $('.fechas').show()
    $('.reporte,.tipos_productos,._productos,.precios_ice_cream').hide();
  }else if($(this).val() === '2'){
    $('#reporte').selectpicker('val',0)
    $('.reporte,.fechas').show();
  }else{
    $('.reporte,.tipos_productos,._productos,.precios_ice_cream,.fechas').hide();
  }
})

$('#reporte').on('change',function() {
  $('#tipo_producto,#producto,.tamano_producto').selectpicker('val',0)
  $('.tipos_productos,._productos,.precios_ice_cream').hide();
  if($(this).val() === '1'){
    $('.tipos_productos').show();
  }else if($(this).val() === '2'){
    $('._productos').show();
  }else if($(this).val() === '3'){
    $('.precios_ice_cream').show();
  }
})

$('#fechas').on('change',function() {
  $('#desde,#hasta').val('')
  $('.desde,.hasta').hide();
  if($(this).val() === '1'){
    $('.desde,.hasta').show();
  }
})


$('.generar_reporte').on('click',function() {
  if($('#tipo_reporte').val() === '1' || $('#tipo_reporte').val() === '3'){
    FiltrarFechas()
  }else if($('#tipo_reporte').val() === '2'){
    if($('#reporte').val() === '1'){
      if($('#tipo_producto').val() === '0' || $('#tipo_producto').val() === null){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona un tipo de producto!."})
      }else{
        FiltrarFechas()
      }
    }else if($('#reporte').val() === '2'){
      if($('#producto').val() === '0' || $('#producto').val() === null){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona un producto!."})
      }else{
        FiltrarFechas()
      }
    }else if($('#reporte').val() === '3'){
      if($('#tamano_producto').val() === '0' || $('#tamano_producto').val() === null){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona una categoría de precio!."})
      }else{
        FiltrarFechas()
      }
    }else if($('#reporte').val() === '4'){
      FiltrarFechas()
    }else{
      SnackAlerta({aviso:"ok", mensaje:"¡Selecciona el reporte que deseas generar!."})
    }
  }else{
    SnackAlerta({aviso:"ok", mensaje:"¡El tipo de reporte es requerido!."})
  }
})

function FiltrarFechas() {
  if($('#fechas').val() === '0' || $('#fechas').val() === null){
    SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona las fechas a realizar el reporte!."})
  }else{
    if($('#fechas').val() === '2'){
      GenerarReporte()
    }else{
      if(($('#desde').val() == '' || $('#desde').val() == null) && ($('#hasta').val() == '' || $('#hasta').val() == null)){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona un rango de fechas para realizar el reporte!."})
      }else if(($('#desde').val() == '' || $('#desde').val() == null)){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona el rango 'desde' para realizar el reporte!."})
      }else if(($('#hasta').val() == '' || $('#hasta').val() == null)){
        SnackAlerta({aviso:"ok", mensaje:"¡Por favor selecciona el rango 'hasta' para realizar el reporte!."})
      }else{
        GenerarReporte()
      }
    }
  }
}

function GenerarReporte() {
  let datos = new FormData();
  datos.append('tipo_reporte',$('#tipo_reporte').val())
  datos.append('tipo_reporte_nombre',$('#tipo_reporte option:selected').text())
  datos.append('reporte',$('#reporte').val())
  datos.append('reporte_nombre',$('#reporte option:selected').text())
  datos.append('tipo_producto',$('#tipo_producto').val())
  datos.append('tipo_producto_nombre',$('#tipo_producto option:selected').text())
  datos.append('producto',$('#producto').val())
  datos.append('producto_nombre',$('#producto option:selected').text())
  datos.append('tamano_producto',$('#tamano_producto').val())
  datos.append('tamano_producto_nombre',$('#tamano_producto option:selected').text())
  datos.append('fechas',$('#fechas').val())
  datos.append('fechas_nombre',$('#fechas option:selected').text())
  datos.append('agrupar',$('#agrupar').val())
  datos.append('agrupar_nombre',$('#agrupar option:selected').text())
  datos.append('desde',$('#desde').val())
  datos.append('hasta',$('#hasta').val())
  
  $('.generar_reporte').html('GENERANDO...').prop('disabled',true);
  fetch('generarreportes',{
    method: 'POST',
    body: datos
  }).then(response => response.json()
  ).then(data => {
    $('.generar_reporte').html('GENERAR REPORTE').prop('disabled',false);
    $('#modal_mostrar_reportes').modal('show')
    $(".mostrar_reportes").html(data.datos).prop('disabled',false).selectpicker('refresh');
  }).catch(error => {
    $('.generar_reporte').html('GENERAR REPORTE').prop('disabled',false);
    $('#modal_mostrar_reportes').modal('hide')
    $(".mostrar_reportes").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });
}

$('.imprimir_reporte').on('click',function(){
  var a = $('.mostrar_reportes').html();
  console.log(a)
  $('.table-responsive').css({'max-height':'none'});
  $.print('.mostrar_reportes > .card');
  $('.mostrar_reportes').html(a)
})