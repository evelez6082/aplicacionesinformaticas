import {query, SnackAlerta} from './funcionesGlobales.js'; 

function construirLayout(data,clase,color){

    let div = ''
  
    data.data.forEach(fila => {

      div = div +   

      `<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 " >
      <button class="btn btn-outline-info btn-block personalizado_acordion contenedor_producto">
        
        <div class="caja_nombre_visto">
          <i style="margin-right: 0.5rem;font-size: 50px;margin-left: -31px;margin-top: -12px;margin-bottom: -12px;" class="material-icons agregar_producto_por_defecto">check_circle_outline</i>
          <span class="nombre_producto">${fila.nombre}</span>
          <span data-tipo-producto=${fila.tipo_producto} data-id = ${fila.id} data-precio=${fila.precio} data-tipo=${fila.tipo}></span>
        </div>
        <div class="caja_borrar_personalizados">
          <span ${color} class="badge badge-primary cant_producto"> 0 </span>
          <i class="material-icons eliminar_producto_personalizado" style="font-size: 50px;margin-right: -31px;">delete</i>
        </div>
      </button>
    </div>`;
  
    })
  
    query("."+clase+" .row").innerHTML = div
} 
  
function contruirLayoutAdicionalesAndExtra(data,tipo){
  
    let div = ''
  
    data.data.forEach(fila => {
  
      div = div + '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">'+
    '<div class="card card_recomendado contenedor_producto">'+
      '<div class="card-body">'+
        '<span class="badge badge-primary pull-right cant_producto actualizar_'+fila.nombre.replace(/ /g, "").replace("&","").replace("'","").replace("`","").replace(".","")+'">0</span>'+
        '<span data-tipo-producto='+fila.tipo_producto+' data-id="'+fila.id+'"></span>'+
        '<h5 class="card-title nombre_producto">'+fila.nombre+'</h5>'+
        '<p class="card-text">'+(tipo !== 'adicionales'?
        '<span class="text-muted"><b>Categoría: </b><span class="categori_producto">'+fila.categoria+'</span></span><br>': '')+
          '<span class="text-muted"><b>Precio: </b><span class="precio_producto">'+fila.precio+'</span></span>'+
          '<hr class="hr-recomendado">'+
        '</p>'+
      '</div>'+
      '<div class="card-footer">'+
          '<div class="row">'+
            '<div class="col-lg-12">'+
              '<center>'+
                '<div class="btn-group dropup pull-right">'+
                  '<button type="button" class="btn btn-link eliminar_venta_re"><i class="material-icons eliminar_venta_i">remove</i></button>'+
                  '<button type="button" class="btn btn-info agregar_venta_re"><i class="material-icons agregar_venta_i">add</i></button>'+
                '</div>'+
              '</center>'+
            '</div>'+
          '</div>'+
        '</div>'+
    '</div>'+
  '</div>';})
  
    if(tipo == "adicionales") query(".card_lista_adicionales").innerHTML = div
    else query(".card_lista_extras").innerHTML = div
    
}
  
function contruirLayoutRecomendados(data){
  
    let div = ''
  
    data.data.forEach(fila => {
   
    div = div + '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">'+
    '<div class="card card_recomendado contenedor_producto">'+
      '<div class="card-body">'+
        '<span class="badge badge-primary pull-right cant_producto actualizar_'+fila.nombre.replace(/ /g, "").replace("'","").replace("&","").replace("`","").replace(".","")+'">0</span>'+
        '<span data-tipo-producto='+fila.tipo_producto+' data-id="'+fila.id+'"></span>'+
        '<h5 class="card-title nombre_producto">'+fila.nombre+'</h5>'+
        '<p class="card-text">'+
            '<b class="sabor_venta">Sabor: </b>'+fila.sabores+'<br>'+
            '<b class="ingrediente_venta">Ingredientes: </b>'+fila.ingredientes+'<br>'+
            '<b class="salsa_venta">Salsa: </b> '+fila.sabores+'<br>'+
          '<span class="text-muted"><b>Precio: </b><span class="precio_producto">'+fila.precio+'</span></span>'+
          '<hr class="hr-recomendado">'+
        '</p>'+
      '</div>'+
      '<div class="card-footer">'+
          '<div class="row">'+
            '<div class="col-lg-12">'+
              '<center>'+
                '<div class="btn-group dropup pull-right">'+
                  '<button type="button" class="btn btn-link eliminar_venta_re"><i class="material-icons eliminar_venta_i">remove</i></button>'+
                  '<button type="button" class="btn btn-info agregar_venta_re"><i class="material-icons agregar_venta_i">add</i></button>'+
                '</div>'+
              '</center>'+
            '</div>'+
          '</div>'+
        '</div>'+
    '</div>'+
  '</div>';
   })
  
   query(".card_lista_recomendados").innerHTML = div
  
} 
  
function cargarFunciones(){

  
    $('.personalizado,.recomendado,.adicionales').hide()
  
    let datos = new FormData();
    datos.append('id','');
  
    $("#clientes_ventas").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');
  
    fetch('cargarclientes',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      $("#clientes_ventas").html(data).prop('disabled',false).selectpicker('refresh');
      document.getElementById("clientes_ventas").selectedIndex = 1 
      query('[data-id="clientes_ventas"] .filter-option-inner-inner').textContent = "CONSUMIDOR FINAL / 0000000000"
      
    }).catch(error => {
      $("#clientes_ventas").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
      SnackAlerta({aviso:"errorServidor"})
    });
   


    $("#precio").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');
  
    fetch('cargartamanos',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      $("#precio").html(data).prop('disabled',false).selectpicker('refresh');
    }).catch(error => {
      $("#precio").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
      SnackAlerta({aviso:"errorServidor"})
    });
  

    $("#forma_pago").html("<option value='0' selected> Cargando datos.</option>").prop('disabled',true).selectpicker('refresh');
  
    fetch('cargarformaspagos',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      $("#forma_pago").html(data).prop('disabled',false).selectpicker('refresh');
    }).catch(error => {
      $("#forma_pago").html('<option value="0">Error</option>').prop('disabled',false).selectpicker('refresh');
      SnackAlerta({aviso:"errorServidor"})
    });
  
  
    fetch('cargarproductosextras',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
  
      contruirLayoutAdicionalesAndExtra(data,"extras") 
  
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
  
  
    fetch('cargartiposproductosjson',{
      method: 'POST',
      body: datos})
    .then(response => response.json()) 
    .then(data => {
      
      let boton = '';
       data.data.forEach(btn=>{
         boton = boton + `<button id=${btn.id} type="button" class="${btn.tipo}elegir btn btn-secondary">${btn.tipo}</button>`
       })
       query(".tipo_producto").innerHTML = boton;
      
    })
    .catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
    
  
    fetch('cargarrecomendadosjson',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      contruirLayoutRecomendados(data)
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });

    fetch('cargarsaboresjson',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
  
      construirLayout(data,"lista-sabores","style='background: #9c27b0;'")
  
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
  
    fetch('cargaringredientesjson',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      construirLayout(data,"lista-ingredientes","style='background: #00bcd4;'")
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
  
  
    fetch('cargarsalsasjson',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      construirLayout(data,"lista-salsas","style='background: #679297;'")
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
  
    fetch('cargarAdicionalesjson',{
      method: 'POST',
      body: datos
    }).then(response => response.json()
    ).then(data => {
      contruirLayoutAdicionalesAndExtra(data,"adicionales") 
    }).catch(error => {
      SnackAlerta({aviso:"errorServidor"})
    });
  
  
}

  // EXPORTAR FUNCIONES 
export {construirLayout,contruirLayoutAdicionalesAndExtra,
    contruirLayoutRecomendados,cargarFunciones}; 
