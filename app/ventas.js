import {query, SnackAlerta,alerta} from './funcionesGlobales.js'; 
import {cargarFunciones} from './plantillaVentas.js'

window.addEventListener('load',iniciarEventos,true)

let salsa = {}
let sabor = {}
let ingrediente = {}
let ArrayPersonalizado = []
let ticketDatos = []
let tabla_ventas = null

function iniciarEventos(){ 
 cargarFunciones()
 tablaVentas()
  query(".card_lista_recomendados").addEventListener('click',agregarOrQuitarFila)
  query(".card_lista_adicionales").addEventListener('click',agregarOrQuitarFila)
  query(".card_lista_extras").addEventListener('click',agregarOrQuitarFila)
  query(".tabla_ventas_productos").addEventListener('click',eliminarFila)
  query(".tabla_ventas_productos").addEventListener('keyup',actualizarSubTotalFilas)
  query(".tabla_ventas_productos").addEventListener('change',actualizarSubTotalFilas)
  query(".btn_agregar_cliente_venta").addEventListener('click',abrirModalProductos) 
  query(".cancelar_venta").addEventListener('click',cancelarVenta) 
  query(".realizar_venta").addEventListener('click',realizarVenta) 
  query(".lista_personalizados").addEventListener('click',sumarOrRestarPersonalizados)
  query(".agregar_personalizado_carrito").addEventListener('click',validarPersonalizado),
  document.getElementById("precio").addEventListener('change',sumarPersonalizados)
  query(".confirmarBtnVentaTotal").addEventListener('click',confirmarBtnVentaTotal)
 }

//***********PERSONALIZADOS***********************//


function sumarOrRestarPersonalizados(e){

  let clase = e.target.classList;
  var btnMasOrMenos = e.target;


  if (clase.contains("nombre_producto")) btnMasOrMenos = e.target.parentElement

  if(clase.contains('contenedor_producto') || clase.contains('nombre_producto')) {
    actualizarPersonalizado(btnMasOrMenos.closest('button'),"sumar")
  }
  
  if(clase.contains('eliminar_producto_personalizado')){
    actualizarPersonalizado(btnMasOrMenos.closest('button'),"restar")
  
  }
  
  if(clase.contains('agregar_producto_por_defecto')){
    agregarPorDefecto(btnMasOrMenos.closest('button'),btnMasOrMenos)  
  }

}

query("#precio").addEventListener("change",function(){
  barrer("porDefectoSabor") 
})
 
function agregarPorDefecto(producto,estrella){

  const tipoHelado = query("#precio").options[query("#precio").selectedIndex]
  const tipo = producto.querySelector("[data-tipo]").dataset.tipo;
  if(Number(producto.querySelector(".cant_producto").textContent) == 0) return;
  let aux = "no"
  
 
 if(tipoHelado !== undefined){
 
  if(tipoHelado.text.indexOf("TULIP") == 0){
   
    if(tipo == "sabor"){
  
      if(query(".porDefectoSabor")){ 
  
        if(estrella.classList.contains("porDefectoSabor"))
               {estrella.classList.remove('porDefectoSabor');  sumarPersonalizados();return}
  
  
        if( query(".sabor_per_cont").getElementsByClassName('porDefectoSabor').length >= 2){
        
        } else estrella.classList.add("porDefectoSabor")
        
  
      }else estrella.classList.add("porDefectoSabor")

      aux = "si"
      
    }

    
   }
 
 }


  if(tipo == "sabor" && aux == "no"){

    aux = "si"

   if(query(".porDefectoSabor"))query(".porDefectoSabor").classList.remove("porDefectoSabor")

    if(estrella.classList.contains("porDefectoSabor")) estrella.classList.remove("porDefectoSabor") 
     else estrella.classList.add("porDefectoSabor")
       
      
  } 

  if(tipo == "salsa"){

    if(query(".porDefectoSalsa")) query(".porDefectoSalsa").classList.remove("porDefectoSalsa")
    
    if(estrella.classList.contains("porDefectoSalsa")) estrella.classList.remove("porDefectoSabor") 
    else estrella.classList.add("porDefectoSalsa")   
  }

  if(tipo == "ingrediente"){

    if(query(".porDefectoIngrediente")){

      if(estrella.classList.contains("porDefectoIngrediente"))
             {estrella.classList.remove('porDefectoIngrediente');  sumarPersonalizados();return}


      if( query(".ingrediente_per_cont").getElementsByClassName('porDefectoIngrediente').length >= 2){
      
      } else estrella.classList.add("porDefectoIngrediente")
      

    }else estrella.classList.add("porDefectoIngrediente")
    
  }
  
  sumarPersonalizados()
} 
   
function actualizarPersonalizado(producto,accion){

  console.log(producto)
 
  let patron = /^\d+$/;
  let btnCantidadproducto = producto.querySelector(".cant_producto")
  let reg = new RegExp(patron);
  let cantProducto = Number(btnCantidadproducto.textContent);

  accion == "sumar" ? cantProducto++ : cantProducto--;

  switch(accion){
    case "sumar": sumarProductoPersonalizado(producto,cantProducto,btnCantidadproducto,reg);break
    case "restar": restarProductoPersonalizado(producto,cantProducto,btnCantidadproducto,reg);break
  }

}

function sumarProductoPersonalizado(producto,cantProducto,btnCantidadproducto,reg){

  if(reg.test(cantProducto)){
      
    btnCantidadproducto.textContent = cantProducto 
   
    cantProducto == 1 ? crearCajaEliminarPersonalizado(producto,cantProducto) : ''
    
    actualizarCantidadPersonalizado(producto,cantProducto) 
   }
}

function restarProductoPersonalizado(producto,cantProducto,btnCantidadproducto,reg){
 
  if( cantProducto >= 0 && reg.test(cantProducto)){
      
    btnCantidadproducto.textContent = cantProducto
   
    cantProducto == 0 ? eliminarCajaEliminarPersonalizado(producto,cantProducto) : "" 
    actualizarCantidadPersonalizado(producto,cantProducto)  
   }
} 
  
function eliminarCajaEliminarPersonalizado(producto){
   producto.querySelector(".caja_borrar_personalizados").style.display = "none"
   producto.querySelector(".cant_producto").classList.remove("agregado")
   producto.classList.remove("elegido_per")
   producto.querySelector(".agregar_producto_por_defecto").classList.remove("porDefectoSabor")
   producto.querySelector(".agregar_producto_por_defecto").classList.remove("porDefectoIngrediente") 
   producto.querySelector(".agregar_producto_por_defecto").classList.remove("porDefectoSalsa")
}

function crearCajaEliminarPersonalizado(producto){
  producto.querySelector(".caja_borrar_personalizados").style.display = "block"
  producto.querySelector(".cant_producto").classList.add("agregado")
  producto.classList.add("elegido_per")
}

function actualizarCantidadPersonalizado(producto) {

  const tipo = producto.querySelector("[data-tipo]").dataset.tipo;
  const nombre = producto.querySelector(".nombre_producto").textContent
  const cantidad = producto.querySelector(".cant_producto").textContent

  if(tipo == "sabor"){
  
    sabor[nombre] = cantidad != 0 ? '  '+nombre+': '+cantidad+'<br>' : ""
    query(".creado_sabor").innerHTML = '<span>'+Object.values(sabor).join("")+'</span>'
  
  }
  if(tipo == "ingrediente"){

    ingrediente[nombre] = cantidad != 0 ? '  '+nombre+': '+cantidad+'<br>' : ""
    query(".creado_ingrediente").innerHTML = '<span>'+Object.values(ingrediente).join("")+'</span>'
  }

  if(tipo == "salsa"){
   
   salsa[nombre] = cantidad != 0 ? '  '+nombre+': '+cantidad+'<br>' : ""
   query(".creado_salsa").innerHTML = '<span>'+Object.values(salsa).join("")+'</span>'
    
  } 

  sumarPersonalizados()

}

function sumarPersonalizados(){

  let personalizadoPrecio = 0;
  let cantidadPersonalizado = Number(query(".cantidad_personalizado_input").value) || 0
  let totalPersonalizado = 0;
  let precio = document.getElementById("precio");

  Array.from(query(".lista_personalizados").getElementsByClassName("elegido_per")).forEach(producto=>{

    if(producto.querySelector(".agregar_producto_por_defecto").classList.length > 2){
      personalizadoPrecio = personalizadoPrecio + (Number(producto.querySelector("[data-precio]").dataset.precio) * (Number(producto.querySelector(".cant_producto").textContent) -1))
    }else{
      personalizadoPrecio = personalizadoPrecio + (Number(producto.querySelector("[data-precio]").dataset.precio) * Number(producto.querySelector(".cant_producto").textContent))
    }

  }) 

  if(precio.selectedIndex == 0 || precio.selectedIndex == -1){
       totalPersonalizado = personalizadoPrecio * cantidadPersonalizado
  }else totalPersonalizado = Number(precio.options[precio.selectedIndex].text.split(":")[1]) + (personalizadoPrecio * cantidadPersonalizado)
   
  query(".recibe_valor_producto_creado").textContent = "$ "+totalPersonalizado.toFixed(2)
}  

function validarPersonalizado(){

   const precio = query("#precio").options[query("#precio").selectedIndex]

   if(document.getElementById("precio").selectedIndex == 0 || document.getElementById("precio").selectedIndex == -1){alerta("Tiene que agregar un precio.","error");return;}
   if(query(".lista-sabores .elegido_per") == null){alerta("Tiene que elegir un sabor.","error") ;return;}
   if(query(".lista-sabores .porDefectoSabor") == null){alerta("Tiene que elegir el sabor principal.","error"); return;}
   if(query(".lista-ingredientes .elegido_per") != null){
   
    const cant = query(".lista-ingredientes").getElementsByClassName("elegido_per").length

    if(precio.text.indexOf("CONO") != -1){
 
    }else{
      if(cant == 1){
        if(query(".lista-ingredientes .porDefectoIngrediente") == null){
          alerta("Tiene que elegir el ingrediente principal.","error");return;}}
  
      if(cant >= 2){
        const cant = query(".lista-ingredientes").getElementsByClassName("porDefectoIngrediente").length
        if(cant < 2){alerta("Tiene que elegir dos ingredientes como principales.","error");return;}}
      }
   
    }

    if(precio.text.indexOf("CONO") != -1){

    }else{
     
      if(query(".lista-salsas .elegido_per") != null){
        if(query(".lista-salsas .porDefectoSalsa") == null){alerta("Tiene que elegir la salsa principal.","error");return;}
      }
  
    }


  if(Number(query(".cantidad_personalizado_input").value) < 1){alerta("La cantidad debe ser mayor a cero.","error");return;}

  crearFilaPersonalizado()

}
 

function crearFilaPersonalizado(){

  let codigo = `${new Date().getMilliseconds()}`;
  let aux = document.getElementById("precio")
  let precio = aux.options[aux.selectedIndex];
  
  let fila = 
      '<tr style="background-color: #CEECF5;" class="filaVenta">' +
      '<td style="width: 10%;"> <button data-tamanoText='+precio.text+' data-tamano="'+precio.value+' "data-codigo="'+codigo+'" type="button" class="btn btn-link btn-sm  eliminarCarrito"><i class="material-icons">remove_shopping_cart</i></button></td>' +
      '<td style="text-align: left;" data-tipo-producto="personalizado"  data-id="sinId"  class="detalle_articulo">'+query(".creado_sabor").innerHTML+' '+query(".creado_ingrediente").innerHTML+' '+query(".creado_salsa").innerHTML+'</td>' +
      '<td><input   disabled min="0" pattern="^[0-9]+" type="number" value="'+Number(query(".recibe_valor_producto_creado").textContent.replace('$',''))+'" class="form-control detalle_precio"></td>' +
      '<td><input  min="1" pattern="^[1-9]+" type="number" value="'+Number(query(".cantidad_personalizado_input").value)+'" class="form-control detalle_cantidad"></td>' +
      '<td class="subTotalFila"></td>' +
      '</tr>';

  let posicion = query(".tabla_ventas_productos tbody").rows.length - 2
  query(".tabla_ventas_productos tbody").insertRow(posicion).innerHTML = fila 
  actualizarSubTotalFilas(query(".tabla_ventas_productos tbody").rows[posicion]) 

  
  recogerDatosDeLosPersonalizados(codigo);
  limpiarVentasPersonalizadas();

} 

function barrer(clase){
  Array.from(document.getElementsByClassName(clase)).forEach(fila=>{
    fila.classList.remove(clase);
  })
}

function limpiarVentasPersonalizadas(){


  Array.from(query(".lista_personalizados").getElementsByClassName("agregado")).forEach( producto => producto.textContent = 0)

  if(query(".personalizado .creado_salsa")) query(".personalizado .creado_salsa").innerHTML = ""
  if(query(".personalizado .creado_ingrediente"))query(".personalizado .creado_ingrediente").innerHTML = ""
  if(query(".personalizado .creado_sabor"))query(".personalizado .creado_sabor").innerHTML = ""

  query(".recibe_valor_producto_creado").textContent = "$0.00"
  query(".cantidad_personalizado_input").value = 1

  barrer("elegido_per")
  barrer("porDefectoSabor") 
  barrer("porDefectoIngrediente")
  barrer("porDefectoSalsa")

  Array.from(document.getElementsByClassName("caja_borrar_personalizados")).forEach(caja=>{
    caja.style.display = "none"
  })

  salsa = {};sabor = {};ingrediente = {}
  
  document.getElementById("precio").selectedIndex = 0;
  $("#precio").val('default').selectpicker("refresh");

}

function recogerDatosDeLosPersonalizados(codigo){

  let personalizado = []
 

  Array.from(query(".contiene_todo_personalizado_datos").getElementsByClassName("elegido_per")).forEach((fila,index)=>{
    personalizado.push( 
      {
         extra: fila.querySelector(".agregar_producto_por_defecto").classList.value.indexOf("porDefecto") != -1 ? false : true,
         id:fila.querySelector("[data-id]").dataset.id, 
         nombre: fila.querySelector(".nombre_producto").textContent,
         precio: fila.querySelector("[data-precio]").dataset.precio, 
         tipo: fila.querySelector("[data-tipo]").dataset.tipo, 
         cantidad: fila.querySelector(".cant_producto").textContent,
      });
  })

  ArrayPersonalizado.push({personalizado,codigo})

} 
 

//***************RECOMENDADO Y OTROS *********************//

function actualizarSubTotalFilas(e){

  let referencia = null;

  referencia = (e.target === undefined) ?  e : e.target.closest('tr') 

  let detalle_precio = Number(referencia.querySelector('.detalle_precio').value)
  let detalle_cantidad = Number(referencia.querySelector('.detalle_cantidad').value)

  if(valirdarValores(detalle_cantidad,detalle_precio)){

    let subTotalFila = detalle_precio * detalle_cantidad

    referencia.querySelector('.subTotalFila').textContent = "$ "+subTotalFila.toFixed(2)
  
    actualizarTotalVentas()
  }

} 
  
function actualizarTotalVentas(){

 let totalFilas = Array.from(query(".tabla_ventas_productos").getElementsByClassName('subTotalFila'));
 let totalVentaDecimales = totalFilas.length != 0 ? totalFilas.map(fila=>Number(fila.textContent.replace('$',''))).reduce((acumulador, valorActual) => acumulador + valorActual) : 0
 let totalVentas = totalVentaDecimales.toFixed(2)

 query(".total_parcial spam").textContent  = "$ "+totalVentas
 query(".total_neto spam").textContent = "$ "+totalVentas

}

function agregarOrQuitarFila(e){

  var clase = e.target.classList;
  var btnMasOrMenos = e.target; 

  if(clase.value.indexOf("agregar") != -1){actualizarFila(btnMasOrMenos.closest(".contenedor_producto"),"sumar");return}

  if(clase.value.indexOf("eliminar") != -1){actualizarFila(btnMasOrMenos.closest(".contenedor_producto"),"restar");return}
}

function actualizarFila(producto,accion){

    let patron = /^\d+$/;
     let btnCantidadproducto = producto.querySelector(".cant_producto")
     let reg = new RegExp(patron);
     let cantProducto = Number(btnCantidadproducto.textContent);

     accion == "sumar" ? cantProducto++ : cantProducto--;

     switch(accion){
       case "sumar": sumarProducto(producto,cantProducto,btnCantidadproducto,reg);break
       case "restar": restarProducto(producto,cantProducto,btnCantidadproducto,reg);break
     }
     
}

function sumarProducto(producto,cantProducto,btnCantidadproducto,reg){

  if(reg.test(cantProducto)){
      
    btnCantidadproducto.textContent = cantProducto
   
    cantProducto == 1 ? crearFila(producto,cantProducto) : actualizarCantidad(producto,cantProducto) 
   }
}

function restarProducto(producto,cantProducto,btnCantidadproducto,reg){
 
  if( cantProducto >= 0 && reg.test(cantProducto)){
      
    btnCantidadproducto.textContent = cantProducto
   
    cantProducto == 0 ? destruirFila(producto,cantProducto) : actualizarCantidad(producto,cantProducto) 
   }
}

function crearFila(producto,cantProducto){

  producto.querySelector(".cant_producto").classList.add("agregado")
  let nombre_producto = producto.querySelector(".nombre_producto").textContent;

  let fila = 
      '<tr style="background-color: #CEECF5;" class="filaVenta">' +
      '<td> <button  type="button" class="btn btn-link btn-sm eliminarCarrito"><i class="material-icons">remove_shopping_cart</i></button></td>' +
      '<td style="text-align: left;" data-tipo-producto='+producto.querySelector("span[data-tipo-producto]").dataset.tipoProducto+'  data-id='+producto.querySelector("span[data-id]").dataset.id+'  class="detalle_articulo">'+nombre_producto+'</td>' +
      '<td><input  disabled min="0" pattern="^[0-9]+" type="number" value="'+Number(producto.querySelector(".precio_producto").textContent.replace('$',''))+'" class="form-control detalle_precio"></td>' +
      '<td><input  min="1" pattern="^[1-9]+" type="number" value="'+cantProducto+'" class="form-control detalle_cantidad '+nombre_producto.replace(/ /g, "").replace("'","").replace("&","").replace("`","").replace(".","")+'"></td>' +
      '<td class="subTotalFila"></td>' +
      '</tr>';

  let posicion = query(".tabla_ventas_productos tbody").rows.length - 2
  query(".tabla_ventas_productos tbody").insertRow(posicion).innerHTML = fila 
  actualizarSubTotalFilas(query(".tabla_ventas_productos tbody").rows[posicion]) 
}

function eliminarFila(e){

  var clase = e.target.classList;
  var btnEliminar = e.target;


  if (clase.contains("material-icons")) btnEliminar = e.target.parentElement
  
  if(clase.contains("eliminarCarrito") || clase.contains("material-icons")){

    if(btnEliminar.closest('tr').querySelector("[data-tipo-producto]").dataset.tipoProducto != "personalizado"){
     
      let clase = btnEliminar.closest('tr').querySelector('.detalle_articulo').textContent.replace(/ /g, "").replace("&","").replace("'","").replace("`","").replace(".","");

      if(query(".actualizar_"+clase)){
        query(".actualizar_"+clase).textContent = 0
        query(".actualizar_"+clase).classList.remove('agregado')
      }
    }else{
      let codigo = btnEliminar.closest('tr').querySelector("[data-codigo]").dataset.codigo

      ArrayPersonalizado = ArrayPersonalizado.filter(personalizado => {
        return personalizado.codigo!=codigo
      })
    }
    btnEliminar.closest('tr').remove()  
    
    actualizarTotalVentas()
  }

} 

function destruirFila(producto,cantProducto){
  let nombre_producto = producto.querySelector(".nombre_producto").textContent.replace(/ /g, "").replace("'","").replace("`","").replace(".","");
  if(query('.'+nombre_producto)) {
    producto.querySelector(".cant_producto").classList.remove("agregado")
    query('.'+nombre_producto).closest('tr').remove() 
  }
  actualizarTotalVentas()
}

function actualizarCantidad(producto,cantProducto) {
  let nombre_producto = producto.querySelector(".nombre_producto").textContent.replace(/ /g, "").replace("&","").replace("'","").replace("`","").replace(".","");
  query('.'+nombre_producto).value = cantProducto
  actualizarSubTotalFilas(query('.'+nombre_producto).closest('tr'))
}

function valirdarValores(detalle_cantidad,detalle_precio){
 
 let valido = true;

 if(detalle_precio <= 0 && detalle_precio != ""){ alerta("El precio del producto debe ser mayor a cero.","error");valido = false;}
 if(detalle_cantidad <= 0 && detalle_cantidad != ""){alerta("La cantidad del producto debe ser mayor a cero.","error");valido = false;}
 if(!Number.isInteger(detalle_cantidad)){alerta("La cantidad tiene que ser un número entero.","error");valido = false;}

  return valido;
}
 
function  abrirModalProductos(){
  $('.personalizado').show() 
  $(".tipo_producto > .btn").removeClass('btn-info')
  $(".PERSONALIZADOelegir").addClass('btn-info')
  $('.recomendado,.adicionales,.extras').hide()   
  $("#precio").val('default').selectpicker("refresh"); 
 
}
 
function cancelarVenta(){

  let cantidadFilasVentas = document.querySelector(".tabla_ventas_productos tbody").rows.length - 2

  if(cantidadFilasVentas === 0) alerta("No hay productos a cancelar.","error")
  else{

  Swal.fire({
    title: '¿Estás seguro?',
    text: "Se perderán todos los productos seleccionados.",
    type: 'warning',
    showCancelButton: true,
    cancelButtonColor: '#00BCD4',
    confirmButtonColor: '#9C27B0',
    confirmButtonText: '¡Si, cancelar venta!'
  }).then((result) => {

    if (result.value) {
      alerta("La venta se canceló con éxito.","success");limpiarDetallesVenta(cantidadFilasVentas)
     }
   })
 }

}

function realizarVenta(){

  let cantidadFilasVentas = document.querySelector(".tabla_ventas_productos tbody").rows.length - 2

  if(query("#clientes_ventas").value === "0") {alerta("Agregue un cliente por favor.","error");return}

  
  if(cantidadFilasVentas === 0) alerta("No ha agregado productos para la venta.","error")
  else{

    $("#modalVender").modal("show");
    query("#pago").value = ""
    document.getElementById("forma_pago").selectedIndex = 1
    query('[data-id="forma_pago"] .filter-option-inner-inner').textContent = "CONTADO"
 }

} 


function confirmarBtnVentaTotal(){
   
   let pago = Number(query("#pago").value)
   let venta = Number(query(".total_neto spam").textContent.replace("$","").trim())

   if(!pago){ alerta("¡Debe ser un número!.","error");return}
   if(pago<=0){alert("El pago debe ser mayor a cero","error");return}
   if(pago<venta){alert("El pago debe ser igual o mayor a la venta","error");return}
   if(document.getElementById("forma_pago").selectedIndex == 0 || document.getElementById("forma_pago").selectedIndex == -1){alert("Tiene que agregar una forma de pago.","error");return;}

   ventaExitosa();

} 

function ventaExitosa(){ 
 
  let datos = new FormData();
  let cantidadFilasVentas = document.querySelector(".tabla_ventas_productos tbody").rows.length - 2
  let btnConfirmar = query(".confirmarBtnVentaTotal");
  let btbCancelar = query(".cancelarBtnVentaTotal")

  datos.append('venta',JSON.stringify(recogerDatosDelaVenta(cantidadFilasVentas)))
  datos.append('forma_pago',query("#forma_pago").options[query("#forma_pago").selectedIndex].value)
  datos.append('forma_cantidad',query("#pago").value)
  datos.append('cliente',query("#clientes_ventas").value)

  btnConfirmar.disabled = true
  btbCancelar.disabled = true
  btnConfirmar.textContent = "CONFIRMANDO..."

 fetch('enviardatosventas',{
    method: 'POST',
    body: datos 
  }).then(response => response.json()
  ).then(data => {

    btnConfirmar.disabled = false
    btbCancelar.disabled = false
    btnConfirmar.textContent = "CONFIRMAR"

    if(data.ok){
     
      $("#modalVender").modal("hide");

      Swal.fire({
        title: 'La venta se realizó correctamente.',
        text: "Desea imprimir comprobante de venta ?",
        type: 'success',
        showCancelButton: true,
        cancelButtonColor: '#00BCD4',
        confirmButtonColor: '#9C27B0',
        confirmButtonText: 'Si, imprimir!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) { 

          construirTicket(ticketDatos,data)
        }
 
         ticketDatos = []
         limpiarDetallesVenta(cantidadFilasVentas);
         limpiarVentasPersonalizadas()
         ArrayPersonalizado = []
         document.getElementById("clientes_ventas").selectedIndex = 1 
         query('[data-id="clientes_ventas"] .filter-option-inner-inner').textContent = "CONSUMIDOR FINAL / 0000000000"

        
      }) 

    }else{
      SnackAlerta({aviso:"errorServidor"})
    }

  }).catch(error => {
    btnConfirmar.disabled = false
    btbCancelar.disabled = false
    btnConfirmar.textContent = "CONFIRMAR"
    SnackAlerta({aviso:"errorServidor"})
  });
   
} 
 
function construirTicket(ticketDatos,data){

 let filas = ''
 let total = query(".total_neto spam").textContent;

  ticketDatos.forEach(data=>{
    filas = filas + `

  <tr style="text-align: center;">
       
     <td colspan="5" style="text-align: left;"> <small>${data.tipo_producto == "personalizado" ? '('+data.tamanoTexto+" <br>"+data.nombre.slice(0,-11)+').' : data.nombre}</small>
     <td colspan="1">${data.cantidad}</td>
     <td colspan="2">${Number(data.precio).toFixed(2)}</td>
     <td colspan="3">${data.subTotal}</td>
    
   </tr>`;
  })  

  let ticket = `

<section>

<table style="width:100%; font-size:14px;">
<div style="text-align: center;">
<span><b>ROLL FACTORY ICE CREAM</b></span><br>
<span>MANTA - ECUADOR</span><br>
<span>DENIS PONCE MOREIRA</span><br>
<span>RUC: 1314207349001</span><br> 
<span>AV. FLAVIO REYES, FRENTE A MANICENTRO</span><br>
<span>0969934490</span>
</div>
<hr>
<div style="text-align: left;">
<span>VENTA #: ${data.numero_venta}</span><br>
<span>FECHA: ${data.fecha}</span><br>
<span>CLIENTE: ${data.cliente}</span><br>
<span>VENDEDOR: ${data.vendedor}</span><br>
</div>
<hr>  
  
  <tr>
    <th colspan="5" style="text-align: left;">Producto</th>
    <th colspan="1">Cantidad</th>
    <th colspan="2">Precio</th>
    <th colspan="3">Total</th>
  </tr> 

  ${filas}
 

<div style="margin-bottom:20px">
  <small><b style="text-align: right;">SUBTOTAL: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> ${total}</small><br>
  <small><b style="text-align: right;">IVA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>$ 00.00</small><br>
  <small><b style="text-align: right;">TOTAL: </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${total}</small><br>
  <small><b style="text-align: right;">FORMA DE PAGO: </b>${data.forma_pago}</small><br>
  <small><b style="text-align: right;">ENTREGADO: </b>$ ${Number(query("#pago").value).toFixed(2)}</small><br>
  <small><b style="text-align: right;">CAMBIO: </b>${(Number(query(".pago").value) - Number(total.replace("$",""))).toFixed(2)}</small><br><br>
</div><br>.<br>.<br>.<br>.<br>.<br>.<br>.<br>.<br>.
</section>`;

  $.print(ticket)
}
 
  
function recogerDatosDelaVenta(cantidadFilasVentas){

  let ventaArray = [],recomendados = [],adicionales = [],ventaJson = {},personalizado = [],extras = []

  Array.from(query(".tabla_ventas_productos tbody").rows).forEach((fila,index) => {

    if(cantidadFilasVentas > index){
     
      ventaArray.push({
                             id:fila.querySelector("td[data-id]").dataset.id, 
                             nombre: fila.querySelector(".detalle_articulo").innerHTML,
                             precio: fila.querySelector(".detalle_precio").value,
                             cantidad: fila.querySelector(".detalle_cantidad").value,
                             subTotal: fila.querySelector(".subTotalFila").textContent.replace('$',''),
                             tipo_producto: fila.querySelector("[data-tipo-producto]").dataset.tipoProducto,
                             tamano: fila.querySelector("[data-tamano]") ?  fila.querySelector("[data-tamano]").dataset.tamano : "no tiene",
                             tamanoTexto: fila.querySelector("[data-tamanoText]") ?  fila.querySelector("[data-tamanoText]").dataset.tamanotext : "no tiene",
                             codigo: fila.querySelector("[data-codigo]") ? fila.querySelector("[data-codigo]").dataset.codigo : "no tiene"
                         });
    }
  })

  ventaArray.map(producto => {
     if(producto.tipo_producto == "recomendado")
        recomendados.push(producto)
     
     if(producto.tipo_producto == "adicionales")
       adicionales.push(producto)
     
     if(producto.tipo_producto == "personalizado"){
         producto.detalle = ArrayPersonalizado.filter(per => per.codigo == producto.codigo ? per.personalizado : "").map(per=>per.personalizado)[0]
         personalizado.push(producto)
     }

     if(producto.tipo_producto == "extras")
          extras.push(producto)
  })

  ventaJson.PERSONALIZADO = personalizado
  ventaJson.RECOMENDADO = recomendados 
  ventaJson.ADICIONAL = adicionales
  ventaJson.EXTRA = extras
  ventaJson.TOTAL = query(".total_neto spam").textContent
  ventaJson.ACCION = "registrar"

  ticketDatos = ventaArray

  return ventaJson
} 
 
function limpiarDetallesVenta(cantidadFilasVentas){
  Array.from(query(".tabla_ventas_productos tbody").rows).forEach((fila,index) => { (cantidadFilasVentas > index) ?  fila.remove() : '' })
  limpiarProductosSeleccionado()
  actualizarTotalVentas()
}

function limpiarProductosSeleccionado(){
  Array.from(document.getElementsByClassName("agregado")).forEach( producto => producto.textContent = 0)
}

query('#tipo_producto').addEventListener('click',function(e) {
  $(".tipo_producto > .btn").removeClass('btn-info').addClass('btn-secondary')
  if($(e.target).hasClass('btn')){
    $(e.target).addClass('btn-info')
  }
  switch(e.target.classList[0]) {
    case 'RECOMENDADOelegir':
      $('.recomendado').show()
      $('.personalizado,.adicionales,.extras').hide()
    break
    case 'PERSONALIZADOelegir':
      $('.personalizado').show()
      $('.recomendado,.adicionales,.extras').hide()
    break
    case 'ADICIONALESelegir':
      $('.adicionales').show()
      $('.recomendado,.personalizado,.extras').hide()
    break
    case 'EXTRASelegir':
      $('.extras').show() 
      $('.recomendado,.personalizado,.adicionales').hide()
    break
  }
})

$(".btn-group > .btn").click(function(){
  $(this).addClass("active").siblings().removeClass("active");
});

 
function tablaVentas(){
  tabla_ventas = TablasDatos({
    tabla       : 'tabla_ventas',
    url         : 'listarventas',
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
      { data: 'numero'},
      { data: 'cliente'},
      { data: 'vendedor'},
      { data: 'total'},
      { data: 'pagado'},
      { data: 'cambio'},
      { data: 'estado'},
      { data: 'accion', sClass: "centro"}
    ],
  });
}


$('#tabla_ventas tbody').on('click', 'td.details-control', function () {
  var tr  = $(this).closest('tr'),
      row = tabla_ventas.row(tr);
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
  '<tr><td><b>Creado:</b></td><td>'+(d.creado == '' || d.creado == null ?'No registrado': d.creado)+'</td></tr>'+
  '<tr><td><b>Modificado:</b></td><td>'+(d.modificado == '' || d.modificado == null ?'No registrado': d.modificado)+'</td></tr>'+
 '</table>';
}

 

