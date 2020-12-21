<?php
require_once 'db.modelo.php';
Class TamanosProductos extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ListarTamanosProductos(){
        try {
            $sql = "SELECT * FROM vmostrartamanosproductos;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarTamanosProductos(){
        try {
            $sql = "SELECT * FROM vmostrartamanosproductos WHERE estado IS TRUE;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ActualizarTamanosProductos(TamanosProductos $x){
        try {
            $sql = "SELECT * FROM ActualizarTamanosProductos(?,?,?,?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array(
                $x->id,
                $x->nombre,
                $x->costo,
                $x->precio,
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
}
?>