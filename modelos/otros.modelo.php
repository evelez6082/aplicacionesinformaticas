<?php
require_once 'db.modelo.php';
Class Otros extends database {
    private $pdo;
    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }
    public function CargarProvincias(){
        try {
            $sql = "SELECT * FROM provincias ORDER BY nombre;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarCantones(Otros $x){
        try {
            $sql = "SELECT * FROM cantones WHERE provincias_id = ? ORDER BY nombre;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($x->idprovincia));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarParroquias(Otros $x){
        try {
            $sql = "SELECT * FROM parroquias WHERE cantones_id = ? ORDER BY nombre;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($x->idcanton));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
?>