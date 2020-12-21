
import {camposVaciosFormularios, query, SnackAlerta,removerClassErrorForm} from './funcionesGlobales.js'; 

let tabla_clientes = null;
let id = null;
let cedula = null;
let accion = "registrar";
let estado = null;
let reglasValidacion = []

window.addEventListener('load',iniciarEventos,true)

function iniciarEventos(){

 query("#cedula").addEventListener('keyup',validarCedula);
 query("#provincia").addEventListener('change',filtrarPorCanton)
 query("#canton").addEventListener('change',filtrarPorParroquia)
 query(".actualizar_cliente").addEventListener('click',actualizarCliente);
 query(".registrar_cliente").addEventListener('click',btnAgregarCliente)
 query('.tabla_clientes').addEventListener('click',EditarOrEliminarCliente)

  $(".datos_cliente_llenar").hide();
  $(".datos_cliente_llenar_btn_actualizar").hide();
}

function validarCedula(e){

  let patron = /^\d+$/;
  let reg = new RegExp(patron);
  cedula = e.target.value;

  if(cedula === "") {
    $(".datos_cliente_llenar").hide();
    $(".datos_cliente_llenar_btn_actualizar").hide();
  }

  if((e.keyCode == 13 && cedula != '' && reg.test(cedula))){
   
    let datos = new FormData();

    datos.append('cedula',cedula)

    query('.cancelar_actualizar_cliente').disabled = true;
    query('.cerrar_crear_cliente').disabled = true;
  
    fetch('validarexistecliente',{
      method: 'POST',
      body: datos})
    .then(response => response.json())  
    .then(data => {
      
      if(data.ok){
        if(data.existe){
          query("#apellidos").value = data.apellidos
          query("#nombres").value = data.nombres
          query("#telefono").value = data.telefono
          query("#correo").value = data.correo
          query("#direccion").value = data.direccion
          var select_provincia = document.querySelector("#provincia").children
      
          Array.from(select_provincia).forEach(function(item){
            if(item.value == data.idprovincia){
              item.selected = true
              $("#provincia").selectpicker('val',data.idprovincia)
              cargarCanton(data.idprovincia,data.idcanton,data.idparroquia)
              return 
            }
          })
          SnackAlerta({aviso:"ok", mensaje: data.observacion})
          query("#cedula").disabled = true;
          $(".datos_cliente_llenar").show();
          $(".datos_cliente_llenar_btn_actualizar").show();
        }else{
          query("#cedula").disabled = true;
          $("#apellidos").focus();
          $(".datos_cliente_llenar").show();
          $(".datos_cliente_llenar_btn_actualizar").show();
        } 
      }else{
        SnackAlerta({aviso:"olvido", mensaje: data.observacion})
      }

      query('.cancelar_actualizar_cliente').disabled = false;
      query('.cerrar_crear_cliente').disabled = false;
    
    }).catch(error => {
  
      SnackAlerta({aviso:"errorServidor"})
      query('.cancelar_actualizar_cliente').disabled = false;
      query('.cerrar_crear_cliente').disabled = false;
   
    }); 
  }else{
    (e.keyCode == 13) ? SnackAlerta({aviso:"olvido", mensaje: "Ingrese una cédula correcta"}) : ''
  }
}

function resetearFormulario(){

  query("#FrmActualizarCliente").reset(); 
  $("#provincia").val('default').selectpicker("refresh");
  $("#canton").html('<option value="0">Seleccionar</opcion>').selectpicker("refresh");
  $("#parroquia").html('<option value="0">Seleccionar</opcion>').selectpicker("refresh");
  
}

function actualizarCliente(e){
  
  e.preventDefault();
    
  var datos = new FormData(query("#FrmActualizarCliente") );
  var dataValida = {};

  const data = {  
   formulario: query("#FrmActualizarCliente")
 } 
    
 dataValida = validarDatosFormulario(data);
 

  if(dataValida["acceso"] === true){

    datos.append("id",id)
    datos.append("accion",accion)
    datos.append("cedula",query("#cedula").value)
            
    let actualizarCliente = query(".actualizar_cliente")
    let cancelarCliente = query(".cancelar_actualizar_cliente")
    let cerrar_crear_cliente = query(".cerrar_crear_cliente")
    
    let titulo = actualizarCliente.textContent; 
    actualizarCliente.textContent = titulo.replace("AR","ANDO...") 
    actualizarCliente.disabled = true;
    cancelarCliente.disabled = true 
    cerrar_crear_cliente.disabled = true;

    fetch('actualizarclientes',{
      method: 'POST',
      body: datos})
    .then(response => response.json())  
    .then(data => {

      if(data.ok){
        tabla_clientes.ajax.reload();
        SnackAlerta({aviso:"ok", mensaje: data.observacion})
        $('#exampleModal').modal('hide')
      }else{
        SnackAlerta({aviso:"olvido", mensaje: data.observacion})
      }
      

      actualizarCliente.disabled = false;
      cancelarCliente.disabled = false;
      cerrar_crear_cliente.disabled = false
      actualizarCliente.textContent = titulo;
      
    }).catch(error => {

      SnackAlerta({aviso:"errorServidor"})
      actualizarCliente.disabled = false
      cancelarCliente.disabled = false
      cerrar_crear_cliente.disabled = false
      actualizarCliente.textContent = titulo
    }); 
   
   }

}

function validarDatosFormulario(inf = {}){

  var acceso = true

   if(camposVaciosFormularios({formulario:inf["formulario"],excepto:reglasValidacion})){ 
        SnackAlerta({aviso:"olvido",mensaje:"Recuerde que los campos marcados con asteriscos son obligatorios"})
        acceso = false
    }

    if(acceso == false){
      setTimeout(()=>{
        removerClassErrorForm({formulario:query("#FrmActualizarCliente")}) 
      },4000)
    }
    

  return {
       acceso: acceso
   }    
    
}


function EditarOrEliminarCliente(e){

  let clase = e.target.classList;

  if (clase.contains("editarCliente")) {
       accion = "modificar"
       let btnEditar = e.target
       id = e.target.id
       editarCliente(btnEditar)
  }

  if (clase.contains("eliminarCliente")) {
       accion = "eliminar"
       id = e.target.id
       eliminarCliente()
}

}
 
function editarCliente(btnEditar){
  
  var tr  = $(btnEditar).closest('tr'), 
  row = tabla_clientes.row(tr).data();

  query(".titulo_cliente").textContent = "Modificar Cliente";
  query(".actualizar_cliente").textContent = "MODIFICAR" 
  resetearFormulario();

  query("#cedula").disabled = true;
  query("#apellidos").focus();
  query("#cedula").value = row.cedula;
  query("#apellidos").value = row.apellidos;
  query("#nombres").value = row.nombres;
  query("#telefono").value = row.telefono;
  query("#correo").value = row.correo; 
  query("#direccion").value = row.direccion;
  query("#observacion").value = row.observacion != null ?  row.observacion : "";
  $(row.estado).text() === 'ACTIVO' ? query("#estado").checked = true : query("#estado").checked = false

  $(".creado_modificado").show();
  query(".modificado").textContent = row.modificado != null ? row.modificado : ''
  query(".creado").textContent = row.creado

  $(".datos_cliente_llenar").show();
  $(".datos_cliente_llenar_btn_actualizar").show();

  var select_provincia = document.querySelector("#provincia").children
      
  Array.from(select_provincia).forEach(function(item){

     if(item.value == row.idprovincia){
  
         item.selected = true
         $("#provincia").selectpicker('val',row.idprovincia)
         cargarCanton(row.idprovincia,row.idcanton,row.idparroquia)
         return 
  }})  
  

} 

function btnAgregarCliente(e){
  
  accion = "registrar"
  query(".titulo_cliente").textContent = "Registrar Cliente";
  query(".actualizar_cliente").textContent = "REGISTRAR" 
  resetearFormulario();

  query("#cedula").disabled = false;
  query("#cedula").focus();
  $(".datos_cliente_llenar").hide();
  $(".datos_cliente_llenar_btn_actualizar").hide();
  query("#estado").checked = true
  $(".creado_modificado").hide();

}


function cargarCanton(idprovincia,idcanton,idparroquia){
  let datos = new FormData();
    
  let actualizarCliente = query(".actualizar_cliente")
  let cancelarCliente = query(".cancelar_actualizar_cliente")
  
  actualizarCliente.disabled = true;
  cancelarCliente.disabled = true    
  

  datos.append('idprovincia',idprovincia);
  
  
  $("#canton").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');


  fetch('cargarcantones',{  
    method: 'POST',  
    body: datos}) 
  .then(response => response.json())
  .then(data => {  
     let select = query("#canton").children
      $("#canton").html(data).prop('disabled',false).selectpicker('refresh');
     Array.from(select).forEach(function(item){
       if(item.value == idcanton){
           item.selected = true;
           $("#canton").selectpicker('val',idcanton)
           CargarParroquia(idcanton,idparroquia)
           return}})
  
  }).catch(error => {
    
     $("#canton").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
     SnackAlerta({aviso:"errorServidor"})
     actualizarCliente.disabled = false
     cancelarCliente.disabled = false
    
  });

}

function CargarParroquia(idcanton,idparroquia){
  let datos = new FormData();

  let actualizarCliente = query(".actualizar_cliente")
  let cancelarCliente = query(".cancelar_actualizar_cliente")
  
  actualizarCliente.disabled = true;
  cancelarCliente.disabled = true  
    
  datos.append('idcanton',idcanton);     
  
  
  
  $("#parroquia").html("<option value='0' selected> Cargando datos...</option>").prop('disabled',true).selectpicker('refresh');
  fetch('cargarparroquias',{  
    method: 'POST',
    body: datos}) 
  .then(response => response.json())
  .then(data => {  
     let select = query("#parroquia").children
     $("#parroquia").html(data).prop('disabled',false).selectpicker('refresh');
     Array.from(select).forEach(function(item){
       if(item.value == idparroquia){
        $("#parroquia").selectpicker('val',idparroquia);item.selected = true; return}
        
      })
       actualizarCliente.disabled = false
       cancelarCliente.disabled = false 
  
  }).catch(error => {
     SnackAlerta({aviso:"errorServidor"})
     $("#parroquia").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
     actualizarCliente.disabled = false
     cancelarCliente.disabled = false 
     
  });
}

function eliminarCliente(e){

  Swal.fire({
    title: '¿Deseas eliminar este cliente?',
    text: "¡Si lo haces ya no podrás recuperarlo!.",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.value) {

  var datos = new FormData();

  datos.append('accion',accion)
  datos.append('id',id)

  fetch('actualizarclientes',{
    method: 'POST',
    body: datos})
  .then(response => response.json())  
  .then(data => {
    
    if(data.ok){
      tabla_clientes.ajax.reload();
      SnackAlerta({aviso:"ok", mensaje: data.observacion})
    }else{
      SnackAlerta({aviso:"olvido", mensaje: data.observacion})
    }
  
  }).catch(error => {

    SnackAlerta({aviso:"errorServidor"})
 
  }); 


    }
  })

 
}

function filtrarPorParroquia(e){

  e.preventDefault();
  
  const data = e.target.value;

  if(data !== 0 || data !== null){
    let datos = new FormData();
    datos.append('idcanton',data);

    query('.cancelar_actualizar_cliente').disabled = true;
    query('.cerrar_crear_cliente').disabled = true;

    $("#parroquia").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

    fetch('cargarparroquias',{
      method: 'POST',
      body: datos})
    .then(response => response.json()) 
    .then(data => {
  
      $("#parroquia").html(data).prop('disabled',false).selectpicker('refresh');
      query('.cancelar_actualizar_cliente').disabled = false;
      query('.cerrar_crear_cliente').disabled = false;
     })
    .catch(error => {
      query('.cancelar_actualizar_cliente').disabled = false;
      query('.cerrar_crear_cliente').disabled = false;
      $("#parroquia").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
      SnackAlerta({aviso:"errorServidor"})
    });
    
  }

}


function filtrarPorCanton(e){

  e.preventDefault();

  const data = e.target.value;

  if(data !== 0 || data !== null){

  let datos = new FormData();
    
  datos.append('idprovincia',data);
  
  query('.cancelar_actualizar_cliente').disabled = true;
  query('.cerrar_crear_cliente').disabled = true;
 
  fetch('cargarcantones',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {

    $("#canton").html(data).prop('disabled',false).selectpicker('refresh');
    
    query('.cancelar_actualizar_cliente').disabled = false;
    query('.cerrar_crear_cliente').disabled = false;
   })
  .catch(error => {
    $("#canton").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
    
    query('.cancelar_actualizar_cliente').disabled = false;
    query('.cerrar_crear_cliente').disabled = false;
  });
 }
}


//LLENAR TABLA
$(function(){

  let datos = new FormData();
  datos.append('id','');

  $("#provincia").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargarprovincias',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {

    $("#provincia").html(data).prop('disabled',false).selectpicker('refresh');
   })
  .catch(error => {
    $("#provincia").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });



  tabla_clientes = TablasDatos({
    tabla       : 'tabla_clientes',
    url         : 'listarclientes',
    data        : {id: ''},
    encabezado  : document.title,
    titulo      : $('#titulo').text(),
    subtitulo   : $('#subtitulo').text(),
    columnas    :  [
      {
        className      : 'details-control centro',
        defaultContent : '<i class="material-icons desglosar_menu_tabla">chevron_right</i>'
      },
      { data: 'num', sClass: "centro"},
      { data: 'cedula'},
      { data: 'apellidos'},
      { data: 'nombres'},
      { data: 'telefono'},
      { data: 'correo'},
      { data: 'estado'},
      { data: 'accion', sClass: "centro"}
    ],
  });

$('#tabla_clientes tbody').on('click', 'td.details-control', function () {
    var tr  = $(this).closest('tr'),
        row = tabla_clientes.row(tr);
    if (row.child.isShown()) {
      tr.next('tr').removeClass('details-row');
      row.child.hide();
      tr.removeClass('shown');
      tr.find('td:first i').remove();
      tr.find('td:first').append('<i class="material-icons desglosar_menu_tabla">chevron_right</i>');
    }
    else {
      row.child(format(row.data())).show();
      tr.next('tr').addClass('details-row');
      tr.addClass('shown');
      tr.find('td:first i').remove();
      tr.find('td:first').append('<i class="material-icons desglosar_menu_tabla">expand_more</i>');
    }
 });

function format ( d ) {
  return '<table class="table table-bordered">'+
    '<tr><td><b>Provincia:</b></td><td>'+(d.provincia == '' || d.provincia == null ?'No registrado': d.provincia)+'</td></tr>'+
    '<tr><td><b>Cantón:</b></td><td>'+(d.canton == '' || d.canton == null ?'No registrado': d.canton)+'</td></tr>'+
    '<tr><td><b>Parroquia:</b></td><td>'+(d.parroquia == '' || d.parroquia == null ?'No registrado': d.parroquia)+'</td></tr>'+
    '<tr><td><b>Dirección:</b></td><td>'+(d.direccion == '' || d.direccion == null ?'No registrado': d.direccion)+'</td></tr>'+
    '<tr><td><b>Creado:</b></td><td>'+(d.creado == '' || d.creado == null ?'No registrado': d.creado)+'</td></tr>'+
    '<tr><td><b>Modificado:</b></td><td>'+(d.modificado == '' || d.modificado == null ?'No registrado': d.modificado)+'</td></tr>'+
    '<tr><td><b>Observaciones:</b></td><td>'+(d.observacion == '' || d.observacion == null ?'No registrado': d.observacion)+'</td></tr>'+
  '</table>';
}

});
