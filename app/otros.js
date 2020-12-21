function TablasDatos(b){
  return t = $('#'+b.tabla).DataTable({
     // sScrollX    : true,

      responsive: true,
      bProcessing : true,
      bServerSide : false,
      autoWidth   : true,
      bAutoWidth  : false,
      pageLength  : 5,
      destroy : true,
      cache: true,
      columns: b.columnas,
      ajax: {
        url: b.url,
        type: "POST",
        data: b.data,
        beforeSend: function() {
          /*$cargando.waitMe({
            effect: 'win8_linear',
            text: 'Cargando...',
            bg: 'rgba(255,255,255,0.90)',
            color: '#555'
          });*///$('.progress-line').show();
        },
        error: function (xhr, error, thrown) {
          console.log(xhr.responseText);
          Snackbar.show({text: 'No se cargaron los datos correctamente en la tabla. Intente nuevamente.',showAction: false,pos: 'bottom-left'}); 
          t.clear().draw();
          $('.dataTables_processing,.progress-line').hide();
        }
      },
      "initComplete": function(settings, json) {
        if(json.obs){
          Snackbar.show({text: json.obs,showAction: false,pos: 'bottom-right',actionTextColor: '#ff0000'});
        }
        //$('.progress-line').hide();
        if(!$('#'+b.tabla).parent().hasClass('table-responsive')){
          $('#'+b.tabla+'_processing').after('<div class="table-responsive" id="res-'+b.tabla+'"></div>');
          $('.dataTables_length > label > .bootstrap-select').css({'width':'100px'})
          $('#'+b.tabla).appendTo($("#res-"+b.tabla));
        }
        if(b.scroll && t.data().count() > 0){
          var tableOptions = {
            'scrollY': "250px",
            'paging' : false
          };
          var table = $('#'+b.tabla);
          table.DataTable().destroy()
          table.DataTable(tableOptions);
        }
        //ExportarBotones(b.btn_exportar,b.encabezado,b.titulo,b.subtitulo,b.columnasexp);
      }
  })/*.on( 'processing.dt', function ( e, settings, processing ) {
      if (processing) {
        $cargando.waitMe({
          effect: 'win8_linear',
          text: 'Cargando...',
          bg: 'rgba(255,255,255,0.90)',
          color: '#555'
        });
        console.log('it is loadding');     // **I do not get this**
      } else {
        $cargando.waitMe('hide');
          console.log('it is not loadding'); // **I get this**
      }
    });*/
}

function ExportarBotones(boton,encabezado,titulo,subtitulo,datos_exportar) {
  var buttons = new $.fn.dataTable.Buttons(t, {
    buttons: [
        {
         extend: 'copyHtml5',
         title: titulo,
         messageTop: subtitulo,
         exportOptions: {
            columns: datos_exportar
          },
        },
        /*{
         extend: 'csvHtml5',
         title: titulo,
         messageTop: subtitulo,
         footer: true,
         filename: subtitulo,
         exportOptions: {
            columns: datos_exportar
          },
        }*/,
        {
         extend: 'excelHtml5',
         title: titulo,
         messageTop: subtitulo,
         footer: true,
         filename: subtitulo,
         exportOptions: {
              columns: datos_exportar
          },
        },
        /*{
          extend: 'pdfHtml5',
          header: true,
          orientation: orientacion,
          pageSize: 'A4',
          //download: 'open',
          filename: subtitulo,
          exportOptions: {
              columns: datos_exportar
          },
          customize: function(doc) {
            doc.content[1].table.widths = anchuracol;
            doc.content.splice(0, 1, {
              text: [{
                text: encabezado+'\n',
                bold: true,
                fontSize: 16
              }, {
                text: titulo+' \n',
                bold: true,
                fontSize: 13
              }, {
                text: subtitulo,
                bold: true,
                fontSize: 11
              }],
              margin: [0, 0, 0, 12],
              alignment: 'center'
            });
          }
        }*/,
        {
          extend: 'print',
          title: '',
          exportOptions: {
              columns: datos_exportar
          },
          //messageTop: 'This print was produced using the Print button for DataTables',
          //autoPrint: false,
          customize: function ( win ) {
            $(win.document.body)
                .css( 'font-size', '10pt' )
                .prepend(
                    '<center><span style="font-size:20px">'+encabezado+'</span><br><span style="font-size: 18px;">'+titulo+'</span><br><span>'+subtitulo+'</span></center>'
                );

            $(win.document.body).find( 'table' )
                .addClass( 'compact' )
                .css( 'font-size', 'inherit' );
          }
        },
      ]
  }).container().appendTo($(boton));
}

function handleEnter (field, event) {
  console.log(field)
  var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
  if (keyCode == 13) {
    var i;
    for (i = 0; i < field.form.elements.length; i++)
      if (field == field.form.elements[i])
        break;
      i = (i + 1) % field.form.elements.length;
      field.form.elements[i].focus();
      return false;
    } 
    else
      return true;
  }

  //VALIDA SI ES NUMÉRICO
  function isNumeric (evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode (key);
    var regex = /[0-9]|\./;
    if ( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
    }
  }
  function maxLengthCheck(object) {
    if (object.value.length > object.maxLength)
      object.value = object.value.slice(0, object.maxLength)
  }
  
//SÓLO PERMITE ESCRIBIR ENTEROS
$(".enteros").on("keypress keyup blur",function (event) {    
   $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});