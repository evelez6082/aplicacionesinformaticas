
$("#usuario,#contrasena").keypress(function(e) {
  $(this).removeClass('error')
   $('._iniciar_sesion').hide();
   if(e.which == 13) {
        IniciarSesion();
   }
});
//INICIAR SESIÓN
function IniciarSesion() {
  var usuario = $('#usuario').val().trim();
  var contrasena = $('#contrasena').val().trim();
  if(usuario == '' && contrasena == ''){
    $('#usuario').focus();
      $('#usuario,#contrasena').addClass('error');
  }else if(usuario == ''){
      $('#usuario').addClass('error').focus();
  }else if(contrasena == ''){
      $('#contrasena').addClass('error').focus();
  }else{
    let datos = new FormData();
    datos.append('usuario', usuario);
    datos.append('contrasena', contrasena);
    $.ajax({
      url: 'iniciar_sesion',
      type: 'POST',
      contentType: false,
      data: datos,
      processData: false,
      cache: false, 
      beforeSend: function() {
        $(".btn_iniciar_sesion").html("INICIANDO...");
        $('#usuario,#contrasena,.btn_iniciar_sesion').prop('disabled',true);
      },
      success: function(data) {
        $(".btn_iniciar_sesion").prop('disabled',false).html("INICIAR");
        $('#usuario,#contrasena,.btn_iniciar_sesion').prop('disabled',false);
        try{
          let datas = JSON.parse(data)
          if(datas.ok){
            $('._iniciar_sesion').removeClass('alert-danger').addClass('alert-success alert-dismissible').show().html(datas.observacion);
            location.reload();
          }else{
            $('#usuario,#contrasena,.btn_iniciar_sesion').prop('disabled',false);
            $('._iniciar_sesion').addClass('alert-danger').show().html(datas.observacion);
            $('#usuario').focus();
          }
        }catch(e){
          $('#usuario').focus();
          $('._iniciar_sesion').addClass('alert-danger').show().html('Sucedió un error inesperado al intentar iniciar sesión.');
        }
      },
      error: function(xhr) {
        $(".btn_iniciar_sesion").html("INICIAR");
        $('#usuario,#contrasena,.btn_iniciar_sesion').prop('disabled',false);
          $('._iniciar_sesion').addClass('alert-danger').show().html('No se pudo realizar la petición. Verifique su conexión a internet e inténtelo nuevamente.');
      }
    }) 
  }
}

$('.btn_iniciar_sesion').click(function(){
  $('._iniciar_sesion').hide();
  IniciarSesion();
});
