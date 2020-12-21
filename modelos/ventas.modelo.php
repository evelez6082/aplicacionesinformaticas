<?php
require_once 'db.modelo.php';
Class Ventas extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ListarVentas(){
        try {
            $sql = "SELECT * FROM vmostrarventas;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CargarFormasPago(){
        try {
            $sql = "SELECT * FROM formas_pagos;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ActualizarVentas(Ventas $x){
        try {
            $sql = "SELECT * FROM ActualizarVentas(?,?,?,?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($x->cliente,$x->vendedor,$x->forma_pago,$x->valor_pago,$x->tipo_documento,$x->productos,$x->usuario,$x->accion));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }    
}
?>