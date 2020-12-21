
import {camposVaciosFormularios, query, SnackAlerta,removerClassErrorForm} from './funcionesGlobales.js'; 

let id = null;
let tipo_producto = null;
let accion = "registrar";
let reglasValidacion = []
let estado = null;
let tabla_productos = null;

window.addEventListener('load',iniciarEventos,true)

function iniciarEventos(){
   
  cargarTipoProductos();
  cargarSabores();
  cargarSalsas();
  cargarCategorias();
  cargarIngredientes();
  tabla_productos = cargarTabla();
  
  
  $('.recomendado,.personalizado').hide()
  query(".actualizar_producto").addEventListener('click',actualizarProducto);
  query(".registrarProducto").addEventListener('click',btnAgregarProducto)
  query('#tabla_productos').addEventListener('click',EditarOrEliminarProducto)
  query('#costo').addEventListener('keyup',calcularUtilidad)
  query('#precio').addEventListener('keyup',calcularUtilidad)
    
} 


function calcularUtilidad(valor = 0){

  const precio = query('#precio').value; 
  const costo = query('#costo').value;

  const valor1  = (precio != '' && precio != 0 && precio != null && precio != undefined)
  const valor2  = (costo != '' && costo != 0 && costo != null && costo != undefined)
  
    if(valor1 && valor2) query("#utilidad").value = (((precio - costo)/costo)*100).toFixed(2)
     else query("#utilidad").value = ''

}

function colocarFormularioPorDefecto(){
  
  $(".personalizado").hide();  
  $(".recomendado").hide();

}

function resetearFormulario(){
  
  $("#sabor").selectpicker('val',null) 
  $("#ingrediente").selectpicker('val',null)
  $("#salsa").selectpicker('val',null)  
  $("#categoria").selectpicker('val',null)

  removerClassErrorForm({formulario:query("#FrmActualizarProducto")}) 

  query('#nombre').value = ""
  query('#costo').value = ""
  query('#precio').value = ""
  query('#costo').value = ""
  query('#utilidad').value = ""
  query('#observacion').value = ""
  query('#estado').checked = true

  tipo_producto = null;
  
}

function EditarOrEliminarProducto(e){
  let clase = e.target.classList;
  if (clase.contains("editarProducto")) {
    accion = "modificar"
    id = e.target.id
    let btnEditar = e.target 
    editarProducto(btnEditar)
  }

  if (clase.contains("eliminarProducto")) {
    accion = "eliminar"
    id = e.target.id
    eliminarProducto();
  }
}

function editarProducto(btnEditar){

  var tr  = $(btnEditar).closest('tr'), 
  row = tabla_productos.row(tr).data();

  resetearFormulario()
  query("#FrmActualizarProducto").reset();
  
  query(".titulo_producto").textContent = "Modificar Producto";
  query(".actualizar_producto").textContent = "MODIFICAR" 
  tipoDeProducto(row.tipo_producto)

  Array.from(document.querySelector("#tipo_producto").children).forEach(function(item){

     if(item.textContent == row.tipo_producto){
         $("#tipo_producto").selectpicker('val',item.value)
         item.selected = true
         return 
  }})  

  Array.from(document.querySelector("#categoria").children).forEach(function(item){

    if(item.textContent == row.categoria){
       $("#categoria").selectpicker('val',item.value)
        item.selected = true
        return 
 }})  
 
  $(".creado_modificado").show();
  query(".modificado").textContent = row.modificado != null ? row.modificado : ''
  query(".creado").textContent = row.creado 
  
  query("#nombre").value = row.nombre 
  query("#costo").value = row.costo
  query("#precio").value = row.precio
  query("#observacion").value = row.observacion != null ?  row.observacion : "";
  calcularUtilidad()
  

  $("#ingrediente").selectpicker('val',row.ingredientes != null ? row.ingredientes.map(obj => {return obj.id}) : ''); 
  $("#sabor").selectpicker('val',row.sabores != null ? row.sabores.map(obj => {return obj.id}) : ''); 
  $("#salsa").selectpicker('val',row.salsas != null ? row.salsas.map(obj => {return obj.id}) : ''); 

  $(row.estado).text() === 'ACTIVO' ? query("#estado").checked = true : query("#estado").checked = false

}  

function tipoDeProducto(select){

  switch(select) {
    case 'RECOMENDADO':
      $(".personalizado").hide();  
      $(".recomendado").show();
      reglasValidacion = ["categoria"]  
    break
    case 'PERSONALIZADO':
      $(".recomendado").hide(); 
      $(".personalizado").show();  
      reglasValidacion = ["sabor","ingrediente","salsa"]  
    break
    default:
      reglasValidacion = ["categoria","sabor","ingrediente","salsa"]  
      $(".personalizado").hide();  
      $(".recomendado").hide(); 
  }

}


function eliminarProducto(e){

    Swal.fire({
      title: '¿Deseas eliminar este producto?',
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
      
        fetch('actualizarproductos',{
          method: 'POST',
          body: datos})
        .then(response => response.json())  
        .then(data => {
          
          if(data.ok){
            tabla_productos.ajax.reload();
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


function btnAgregarProducto(e){
  
  accion = "registrar"
  resetearFormulario()
  colocarFormularioPorDefecto()
  query("#FrmActualizarProducto").reset();
 
  query(".titulo_producto").textContent = "Registrar Producto";
  query(".actualizar_producto").textContent = "REGISTRAR"
  $("#tipo_producto").val('default').selectpicker("refresh");
  query("#estado").checked = true
  $(".creado_modificado").hide();

}

function actualizarProducto(e){
  
  e.preventDefault();
    
  var datos = new FormData(query("#FrmActualizarProducto") );
  var dataValida = {};

  const data = {  
   formulario: query("#FrmActualizarProducto")
 } 
    
 dataValida = validarDatosFormulario(data);

  if(dataValida["acceso"] === true){
    
    datos.append("id",id)
    datos.append("accion",accion)
    datos.set("estado",(datos.get("estado") !== null)  ? true : false)

    datos.append("saboress",$("#sabor").val())
    datos.append("ingredientess",$("#ingrediente").val())
    datos.append("salsass",$("#salsa").val())
            
    let actualizarProducto = query(".actualizar_producto")
    let cancelarProducto = query(".cancelar_actualizar_producto")
    let cerrar_modal_producto = query(".cerrar_modal_producto")
    
    let titulo = actualizarProducto.textContent; 
    actualizarProducto.textContent = titulo.replace("AR","ANDO...") 
    actualizarProducto.disabled = true;
    cancelarProducto.disabled = true
    cerrar_modal_producto.disabled = true
    

    fetch('actualizarproductos',{
      method: 'POST',
      body: datos})
    .then(response => response.json())  
    .then(data => {
      if(data.ok){
        tabla_productos.ajax.reload();
        $('#ActualizarProductos').modal('hide')
        SnackAlerta({aviso:"ok", mensaje: data.observacion})
      }else{
        SnackAlerta({aviso:"olvido", mensaje: data.observacion})
      }
      

      actualizarProducto.disabled = false;
      cancelarProducto.disabled = false;
      cerrar_modal_producto.disabled = false;
      actualizarProducto.textContent = titulo;
    }).catch(error => {

      SnackAlerta({aviso:"errorServidor"})
      actualizarProducto.disabled = false
      cancelarProducto.disabled = false
      cerrar_modal_producto.disabled = false;
      actualizarProducto.textContent = titulo
    }); 
   
   }

   
}


function validarDatosFormulario(inf = {}){

  var acceso = true
 
  if(tipo_producto === null){
    
    SnackAlerta({aviso:"olvido",mensaje:"Debe seleccionar al menos un tipo de producto"})
    acceso = false
 
  }else{

      if(camposVaciosFormularios({formulario:inf["formulario"],excepto:reglasValidacion})){ 
        SnackAlerta({aviso:"olvido",mensaje:"Recuerde que los campos marcados con asteriscos son obligatorios"})
        
        acceso = false
    }

    setTimeout(()=>{
      removerClassErrorForm({formulario:query("#FrmActualizarProducto")}) 
    },4000)

  }
  
 

  return {
       acceso: acceso
   }    
    
}

function cargarTipoProductos(){
  let datos = new FormData();
  datos.append('id','');

  $("#tipo_producto").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargartiposproductos',{
    method: 'POST',
    body: datos})
  .then(response => response.json())
  .then(data => {
    $("#tipo_producto").html(data).prop('disabled',false).selectpicker('refresh');
    $('#tipo_producto option[value="abUngorHYktLenkdbgDXpA=="]').remove().selectpicker('refresh');
   })
  .catch(error => {
    $("#tipo_producto").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });

}

function cargarSabores(){
  let datos = new FormData();
  datos.append('id','');

  $("#sabor").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargarsabores',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {
    $("#sabor").html(data).prop('disabled',false).selectpicker('refresh');
   })
  .catch(error => {
    $("#sabor").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });

}

function cargarIngredientes(){
  let datos = new FormData();
  datos.append('id','');

  $("#ingrediente").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargaringredientes',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {
    $("#ingrediente").html(data).prop('disabled',false).selectpicker('refresh');
   })
  .catch(error => {
    $("#ingrediente").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });
}

function cargarSalsas(){

  let datos = new FormData();
  datos.append('id','');

  $("#salsa").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargarsalsas',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {
    $("#salsa").html(data).prop('disabled',false).selectpicker('refresh');
   })
  .catch(error => {
    $("#salsa").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });
}

function cargarCategorias(){


  let datos = new FormData();
  datos.append('id','');

  $("#categoria").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');

  fetch('cargarcategorias',{
    method: 'POST',
    body: datos})
  .then(response => response.json()) 
  .then(data => {

    $("#categoria").html(data).prop('disabled',false).selectpicker('refresh');
   })
  .catch(error => {
    $("#categoria").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
    SnackAlerta({aviso:"errorServidor"})
  });
}

function cargarTabla(){
  return  TablasDatos({
    tabla       : 'tabla_productos',
    url         : 'listarproductos',
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
      { data: 'nombre'},
      { data: 'tipo_producto'},
      { data: 'costo', sClass: "centro"},
      { data: 'precio', sClass: "centro"},
      { data: 'utilidad', sClass: "centro"},
      { data: 'estado', sClass: "centro"},
      { data: 'accion', sClass: "centro"},
      { data: 'categoria', sClass: "oculto"},
      
    ],
  });
}

query(".tipo_producto").addEventListener('change',function(e){

    e.preventDefault();
    resetearFormulario();

    tipo_producto = (e.target.value == 0) ? null : e.target.value;

    let combo = e.target; 
    let select = combo.options[combo.selectedIndex].text;

    tipoDeProducto(select)

});

  
$('#tabla_productos tbody').on('click', 'td.details-control', function () {
  
    var tr  = $(this).closest('tr'),
        row = tabla_productos.row(tr);
     
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
  if(d.tipo_producto === 'RECOMENDADO'){
    return '<table class="table table-bordered">'+
      '<tr><td><b>Sabores:</b></td><td>'+(d.sabores == '' || d.sabores == null ?'No registrado': Descomprimir(d.sabores))+'</td></tr>'+
      '<tr><td><b>Ingredientes:</b></td><td>'+(d.ingredientes == '' || d.ingredientes == null ?'No registrado': Descomprimir(d.ingredientes))+'</td></tr>'+
      '<tr><td><b>Salsas:</b></td><td>'+(d.salsas == '' || d.salsas == null ?'No registrado': Descomprimir(d.salsas))+'</td></tr>'+
     '<tr><td><b>Creado:</b></td><td>'+(d.creado == '' || d.creado == null ?'No registrado': d.creado)+'</td></tr>'+
      '<tr><td><b>Modificado:</b></td><td>'+(d.modificado == '' || d.modificado == null ?'No registrado': d.modificado)+'</td></tr>'+
      '<tr><td><b>Observación:</b></td><td>'+(d.observacion == '' || d.observacion == null ?'No registrado': d.observacion)+'</td></tr>'+
    '</table>';
  }else if(d.tipo_producto === 'PERSONALIZADO'){
    return '<table class="table table-bordered">'+
      '<tr><td><b>Categoría:</b></td><td>'+(d.categoria == '' || d.categoria == null ?'No registrado': d.categoria)+'</td></tr>'+
      '<tr><td><b>Creado:</b></td><td>'+(d.creado == '' || d.creado == null ?'No registrado': d.creado)+'</td></tr>'+
      '<tr><td><b>Modificado:</b></td><td>'+(d.modificado == '' || d.modificado == null ?'No registrado': d.modificado)+'</td></tr>'+
      '<tr><td><b>Observación:</b></td><td>'+(d.observacion == '' || d.observacion == null ?'No registrado': d.observacion)+'</td></tr>'+
    '</table>';
  }else{
    return '<table class="table table-bordered">'+
      '<tr><td><b>Creado:</b></td><td>'+(d.creado == '' || d.creado == null ?'No registrado': d.creado)+'</td></tr>'+
      '<tr><td><b>Modificado:</b></td><td>'+(d.modificado == '' || d.modificado == null ?'No registrado': d.modificado)+'</td></tr>'+
      '<tr><td><b>Observación:</b></td><td>'+(d.observacion == '' || d.observacion == null ?'No registrado': d.observacion)+'</td></tr>'+
    '</table>';
  }
}

function Descomprimir(valores) {
  var a = '';
  console.log(valores)
  if(valores != null){ 
    for (var i in valores) {
      //console.log(valores[i])
      a += valores[i].nombre+', ';
    }
  }
  a = a.slice(0, -2)+'.';
  return a;
} 


$('#categoria').change(function() {

  for(var i in $(this).val()){
     
    if($(this).val()[i] == 0){
      $(this).val($(this).val()[i]).selectpicker('refresh');
    }
  }
});

