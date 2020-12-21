<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
  if(isset($_GET['index'])){

    switch ($_GET['index']) {
      //LOGIN
      case 'iniciar_sesion':
        require 'controladores/acceso.controlador.php';
        $acceso = new AccesoControlador();
        $acceso->IniciarSesion();
      break;
      case 'salir':
        require 'controladores/acceso.controlador.php';
        $acceso = new AccesoControlador();
        $acceso->CerrarSesion();
      break;
 
      //VENTAS
      case 'ventas':
        require 'controladores/ventas.controlador.php';
        $ventas = new VentasControlador();
        $ventas->FrmVentas();
      break;
      case 'listarventas':
        require 'controladores/ventas.controlador.php';
        $ventas = new VentasControlador();
        $ventas->ListarVentas();
      break;
      case 'cargarformaspagos':
        require 'controladores/ventas.controlador.php';
        $ventas = new VentasControlador();
        $ventas->CargarFormasPago();
      break;

      case 'enviardatosventas':
        require 'controladores/ventas.controlador.php';
        $ventas = new VentasControlador();
        $ventas->ActualizarVentas();
      break;


      //CLIENTES
      case 'clientes':
        require 'controladores/clientes.controlador.php';
        $clientes = new ClientesControlador();
        $clientes->FrmClientes();
      break;
      case 'listarclientes':
        require 'controladores/clientes.controlador.php';
        $clientes = new ClientesControlador();
        $clientes->ListarClientes();
      break;
      case 'actualizarclientes':
        require 'controladores/clientes.controlador.php';
        $clientes = new ClientesControlador();
        $clientes->ActualizarClientes(); 
      break;

      case 'validarexistecliente':
        require 'controladores/clientes.controlador.php';
        $clientes = new ClientesControlador();
        $clientes->ValidarExisteCliente();
      break;
      
      case 'cargarclientes':
        require 'controladores/clientes.controlador.php';
        $clientes = new ClientesControlador();
        $clientes->CargarClientes();
      break;


      //USUARIOS
      case 'usuarios':
        require 'controladores/usuarios.controlador.php';
        $usuarios = new UsuariosControlador();
        $usuarios->FrmUsuarios();
      break;
      case 'listarusuarios':
        require 'controladores/usuarios.controlador.php';
        $usuarios = new UsuariosControlador();
        $usuarios->ListarUsuarios();
      break;
      case 'actualizarusuarios':
        require 'controladores/usuarios.controlador.php';
        $usuarios = new UsuariosControlador();
        $usuarios->ActualizarUsuarios(); 
      break;
      case 'validarexisteusuario':
        require 'controladores/usuarios.controlador.php';
        $clientes = new UsuariosControlador();
        $clientes->ValidarExisteUsuario();
      break;
      case 'cargarroles':
        require 'controladores/usuarios.controlador.php';
        $clientes = new UsuariosControlador();
        $clientes->CargarRoles();
      break;

      //PRODUCTOS
      case 'productos':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->FrmProductos();
      break;
      case 'actualizarproductos':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->ActualizarProductos();
      break;
      case 'listarproductos':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->ListarProductos();
      break;
      case 'cargartiposproductosjson':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarTiposProductosJson();
      break;
       case 'cargartiposproductos':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarTiposProductos();
      break;
      case 'cargarproductosextras':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarProductosExtras();
      break;         
      case 'cargarsabores':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarSabores();
      break;
      case 'cargarsaboresjson':
      require 'controladores/productos.controlador.php';
      $productos = new ProductosControlador();
      $productos->CargarSaboresJson();
     break;
     case 'cargaringredientesjson':
     require 'controladores/productos.controlador.php';
     $productos = new ProductosControlador();
     $productos->CargarIngredientesJson();
    break;

    case 'cargarAdicionalesjson':
    require 'controladores/productos.controlador.php';
    $productos = new ProductosControlador();
    $productos->CargarAdicionalesJson();
   break;
    

    case 'cargarrecomendadosjson':
    require 'controladores/productos.controlador.php'; 
    $productos = new ProductosControlador();
    $productos->CargarRecomendadosJson();
   break;

    case 'cargarsalsasjson':
    require 'controladores/productos.controlador.php';
    $productos = new ProductosControlador();
    $productos->CargarSalsasJson();
   break;
      case 'cargaringredientes':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarIngredientes();
      break;
      case 'cargarsalsas':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarSalsas();
      break;
      case 'cargarcategorias':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarCategorias();
      break;
      case 'cargarproductos':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarProductos();
      break;
      case 'cargarproductosportipo':
        require 'controladores/productos.controlador.php';
        $productos = new ProductosControlador();
        $productos->CargarProductosPorTipo();
      break;

      //tamanos
      case 'tamanos':
        require 'controladores/tamanos.controlador.php';
        $tamanos = new TamanosProductosControlador();
        $tamanos->FrmTamanosProductos();
      break;
      case 'listartamanos':
        require 'controladores/tamanos.controlador.php';
        $tamanos = new TamanosProductosControlador();
        $tamanos->ListarTamanosProductos();
      break;
      case 'cargartamanos':
        require 'controladores/tamanos.controlador.php';
        $tamanos = new TamanosProductosControlador();
        $tamanos->CargarTamanosProductos();
      break;
      case 'actualizartamanos':
        require 'controladores/tamanos.controlador.php';
        $tamanos = new TamanosProductosControlador();
        $tamanos->ActualizarTamanosProductos();
      break;

      //REPORTES
      case 'reportes':
        require 'controladores/reportes.controlador.php';
        $reportes = new ReportesControlador();
        $reportes->FrmReportes();
      break;
      case 'generarreportes':
        require 'controladores/reportes.controlador.php';
        $reportes = new ReportesControlador();
        $reportes->GenerarReportes();
      break;

      //OTRAS FUNCIONALIDADES
      case 'cargarprovincias':
        require 'controladores/otros.controlador.php';
        $otros = new OtrosControlador();
        $otros->CargarProvincias();
      break;
      case 'cargarcantones':
        require 'controladores/otros.controlador.php';
        $otros = new OtrosControlador();
        $otros->CargarCantones();
      break;
      case 'cargarparroquias':
        require 'controladores/otros.controlador.php';
        $otros = new OtrosControlador();
        $otros->CargarParroquias();
      break;

      default:
        require 'controladores/acceso.controlador.php';
        $acceso = new AccesoControlador();
        $acceso->Principal();
      break;
    }
  }else{
    require 'controladores/acceso.controlador.php';
    $acceso = new AccesoControlador();
    $acceso->Principal();
  }
?>
