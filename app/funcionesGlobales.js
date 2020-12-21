

function alerta(mensaje,tipo){
  Swal.fire({
    type: tipo,
    title: mensaje,
    showConfirmButton: false,
    timer: 1500
  })
} 

// MENSAJES DE ERRORES DE FORMULARIO


function SnackAlerta(inf = {}){

  switch(inf["aviso"]){
    case "errorServidor":
        Snackbar.show({text: 'No se pudo realizar la petición. Verifique su conexión a internet e inténtelo nuevamente.',pos: 'bottom-left',showAction: false});
     break;
     
    case "olvido":
            Snackbar.show({text: inf["mensaje"],pos: 'bottom-left',showAction: false});
     break;
     
     case "ok":
      Snackbar.show({text: inf["mensaje"],pos: 'bottom-left',showAction: false});
    
      break;

    default:
        Snackbar.show({text: 'Algo no va bien.',pos: 'bottom-left',showAction: false});
     break 
     
 }
  
}

function erroresFormulario(inf){
    
    var datos = inf || {}
    var tiempo = 2000 
    var mensaje = query(`${datos["formulario"]} .notificacion-ingreso`)
    
    mensaje.classList.add("errorMensaje");  

    
    if(query(".boton_iniciar_sesion")){
        query(".boton_iniciar_sesion").style.display = "none";
        query(".boton_cancelar_registro").style.display = "block"   
    }
    
     
    switch(datos["error"]){
       
         case "vacioCampo":  
            mensaje.innerHTML = "<b>Rellena los campos del formulario</b>" 
            break  
       
        case "contrasena":
            mensaje.innerHTML = "<b>Las contraseñas deben ser iguales</b>" 
            query("#repetir_contrasena").focus()
            break
        case "errorServidor":
            mensaje.innerHTML = "<b>No se pudo conectar con el servidor. Verifique su conexión a internet y vuelva a realizar la petición.</b>" 
            break
        default:
            mensaje.innerHTML = "<b>Lo sentimos :C</b>";
    }
      
    setTimeout(function() {  
         mensaje.innerHTML = ""
         mensaje.classList.remove("errorMensaje")
    },tiempo); 
     
}

// MENSAJES DE EXITOS DE FORMULARIO

function exitoFormulario(inf){
    
    var datos = inf || {}
    var mensaje = query(`${datos["formulario"]} .notificacion-ingreso`)
    var tiempo = 2000
    
    mensaje.classList.add("correctoMensaje");
    
    switch(datos["exito"]){
    
         case "restablerCorrecto":  
            mensaje.innerHTML = "<b>"+datos["mensaje"]+"</b>"
            break   
        case "loginCorrecto":  
            mensaje.innerHTML = "<b>Ingreso correcto. Redireccionando...</b>"
            break  
        case "registroCorrecto":  
            mensaje.innerHTML = "<b>Registro exitoso. Ingresando...</b>"
            query(".boton_cancelar_registro").style.display = "none"
            break 
        case "contrasenaActualizada":  
            mensaje.innerHTML = "<b>"+datos["mensaje"]+"</b>"
            break 
        default:
            mensaje.innerHTML = "<b>Algo no anda bien</b>"
            break 
      }
     
    setTimeout(function() {  
         mensaje.innerHTML = ""
         mensaje.classList.remove("correctoMensaje")
    },tiempo); 
    
}

// AVISOS GENERALES PARA FORMULARIOS

function avisoFormulario(inf){
     var datos =  inf || {}
     
     switch(datos["aviso"]){
          case "obligatorio":    
            $.notify(`<span class=${datos["alerta"]}>Los campos con * son abligatorios.</span>`); 
            break
          case "errorServidor":
            $.notify(`<span class=${datos["alerta"]}>No se pudo conectar con el servidor. Verifique su conexión a internet y vuelva a realizar la petición.</span>`);
            break
     }                      
} 

// VALIDACION DE FORMULARIO PARA CAMPOS VACIOS

function camposVaciosFormularios(inf){

     let datos =  inf || {}
     let formulario = datos["formulario"].getElementsByClassName("campo");
     let vacio = false;
     
     Array.from(formulario).forEach(function(campo,index){

       if(!datos["excepto"].includes(campo.id)){
           
         if(campo.nodeName === "SELECT" && campo.classList.contains("selectpicker")){

            let select = $(`#${campo.id}`).val();

             if(select == "0" || select == null || select == 0 ){ 
                
                 query(`.${campo.id}_`).classList.add("errorForm")

                 vacio = true;  
             }
         }else{
            if(campo.nodeName !== "DIV")
               if(campo.value.trim() === "" || campo.value === "0" || campo.value === null){ 
                  
                query(`.${campo.id}_`).classList.add("errorForm")
                 
                 vacio = true;    
              }
         }   
       }
       
          
     })
    
    return vacio;
}

// QUITAR ERROR EN LOS FORMULARIOS VACIOS

function removerClassErrorForm(inf){
     var datos =  inf || {}
     var formulario = datos["formulario"].getElementsByClassName("alertaError");
  
     Array.from(formulario).forEach(function(campo){
                campo.classList.remove("errorForm")  
     })
}


// FUNCION OBTENER NODO REDUCIDA

function query(consulta){
    return document.querySelector(consulta);
} 


// EXPORTAR FUNCIONES A ARCHIVOS QUE LA REQUIERAN
export {erroresFormulario,camposVaciosFormularios,
        query,exitoFormulario,removerClassErrorForm,avisoFormulario,SnackAlerta,alerta}; 
 