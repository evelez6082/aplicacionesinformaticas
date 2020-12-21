<?php
require_once 'db.modelo.php';
Class Productos extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ListarProductos(){
        try {
            $sql = "SELECT * FROM vmostrarproductos;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ActualizarProductos(Productos $x){
        try {
            $sql = "SELECT * FROM ActualizarProductos(?,?,?,?,?,?,?,?,?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array(
                $x->id,
                $x->nombre,
                $x->costo,
                $x->precio,
                $x->tipo_producto,
                $x->categoria,
                $x->sabores,
                $x->ingredientes,
                $x->salsas,
                $x->observacion, 
                $x->estado,
                $x->usuario,
                $x->accion
            ));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarTiposProductos(){
        try {
            $sql = "SELECT * FROM tipos_productos;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarProductosPorTipo(Productos $x){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE idtipo_producto = ?;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($x->tipo_producto));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarProductos(){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE estado IS TRUE ORDER BY tipo_producto DESC,categoria,nombre;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CargarProductosExtras(){
        try { 
            $sql = "SELECT * FROM vmostrarproductos WHERE estado IS TRUE and tipo_producto = 'PERSONALIZADO';";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function CargarRecomendados(){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE estado IS TRUE and tipo_producto = 'RECOMENDADO';";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CargarAdicionales(){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE estado IS TRUE and tipo_producto = 'ADICIONALES';";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TraerProducto(Productos $x){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE estado IS TRUE and id = $x->idProducto;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CargarSabores(){
        try {
            $sql = "SELECT * FROM vmostrarsabores WHERE estado IS TRUE;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarIngredientes(){
        try {
            $sql = "SELECT * FROM vmostraringredientes WHERE estado IS TRUE;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarSalsas(){
        try {
            $sql = "SELECT * FROM vmostrarsalsas;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarCategorias(){
        try {
            $sql = "SELECT * FROM categorias;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
?>