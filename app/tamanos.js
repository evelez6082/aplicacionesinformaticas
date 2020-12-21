import {camposVaciosFormularios, query, SnackAlerta,removerClassErrorForm} from './funcionesGlobales.js'; 

let tabla_tamanos = null;
let id = null;
let accion = "registrar";
let estado = null;
let reglasValidacion = [] 

window.addEventListener('load',iniciarEventos,true)

function iniciarEventos(){

 query(".actualizar_tamano").addEventListener('click',actualizartamano);
 query(".registrar_tamano").addEventListener('click',btnAgregarTamano)
 query('.tabla_tamanos').addEventListener('click',EditarOrEliminarTamano)
 query(".actualizar_tamano").addEventListener('click',actualizartamano);
}



function actualizartamano(e){
  
  e.preventDefault();
    
  var datos = new FormData(query("#FrmActualizarTamano") );
  var dataValida = {};

  const data = {  
   formulario: query("#FrmActualizarTamano")
 } 
    
 dataValida = validarDatosFormulario(data);
 
 
  if(dataValida["acceso"] === true){
    
    datos.append("id",id)
    datos.append("accion",accion)
    
            
    let actualizartamano = query(".actualizar_tamano")
    let cancelartamano = query(".cancelar_actualizar_tamano")
    let cerrar_crear_tamano = query(".cerrar_crear_tamano")
    
    let titulo = actualizartamano.textContent; 
    actualizartamano.textContent = titulo.replace("AR","ANDO...") 
    
    actualizartamano.disabled = true;
    cancelartamano.disabled = true
    cerrar_crear_tamano.disabled = true; 

    fetch('actualizartamanos',{
      method: 'POST',
      body: datos})
    .then(response => response.json())  
    .then(data => {

      if(data.ok){
        $('#ActualizarTamano').modal('hide')
        tabla_tamanos.ajax.reload();
        SnackAlerta({aviso:"ok", mensaje: data.observacion})
      }else{
        SnackAlerta({aviso:"olvido", mensaje: data.observacion})
      }

      actualizartamano.disabled = false;
      cancelartamano.disabled = false;
      cerrar_crear_tamano.disabled = false;
      actualizartamano.textContent = titulo;
    }).catch(error => {

      SnackAlerta({aviso:"errorServidor"})
      actualizartamano.disabled = false
      cancelartamano.disabled = false
      cerrar_crear_tamano.disabled = false
      actualizartamano.textContent = titulo 
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
        removerClassErrorForm({formulario:query("#FrmActualizarTamano")}) 
      },4000)
    }
    

  return {
       acceso: acceso
   }    
    
}

function resetearFormulario(){
  query("#FrmActualizarTamano").reset(); 
}

function EditarOrEliminarTamano(e){

  let clase = e.target.classList;

  if (clase.contains("editarTamano")) {
       accion = "modificar"
       let btnEditar = e.target
       id = e.target.id
       editarTamano(btnEditar)
  }

  if (clase.contains("eliminarTamano")) {
       accion = "eliminar"
       id = e.target.id
       eliminarTamano()
}

}
 
function editarTamano(btnEditar){
  
  var tr  = $(btnEditar).closest('tr'), 
  row = tabla_tamanos.row(tr).data();
  resetearFormulario();

  query(".titulo_tamano").textContent = "Modificar tamaño producto";
  query(".actualizar_tamano").textContent = "MODIFICAR" 

  query("#nombre").focus();
  query("#precio").value = row.precio;
  query("#costo").value = row.costo;
  query("#nombre").value = row.nombre;
  
  query("#observacion").value = row.observacion != null ?  row.observacion : "";
  
  $(row.estado).text() === 'ACTIVO' ? query("#estado").checked = true : query("#estado").checked = false

  $(".creado_modificado").show();
  query(".modificado").textContent = row.modificado != null ? row.modificado : ''
  query(".creado").textContent = row.creado
 
}  

function eliminarTamano(e){

  Swal.fire({
    title: '¿Deseas eliminar este tamaño producto?',
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

  fetch('actualizartamanos',{
    method: 'POST',
    body: datos})
  .then(response => response.json())  
  .then(data => {
    
    if(data.ok){
      tabla_tamanos.ajax.reload();
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

function btnAgregarTamano(e){
  
  accion = "registrar" 
  query(".titulo_tamano").textContent = "Registrar tamaño producto";
  query(".actualizar_tamano").textContent = "REGISTRAR" 
  resetearFormulario();

  query("#estado").checked = true
  $(".creado_modificado").hide();

}



$(function(){
  tabla_tamanos = TablasDatos({
    tabla       : 'tabla_tamanos',
    url         : 'listartamanos',
    data        : {id: ''},
    encabezado  : document.title,
    titulo      : $('#titulo').text(),
    subtitulo   : $('#subtitulo').text(),
    columnas    :  [
      { data: 'num', sClass: "centro"},
      { data: 'nombre'},
      { data: 'costo', sClass: "centro"},
      { data: 'precio', sClass: "centro"},
      { data: 'utilidad', sClass: "centro"},
      { data: 'estado', sClass: "centro"},
      { data: 'accion', sClass: "centro"}
    ],
  });
});