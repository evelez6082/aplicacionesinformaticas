<?php
require_once 'db.modelo.php';
Class Acceso extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }
        catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function IniciarSesion(Acceso $x){
        try{
            $sql = "SELECT * FROM IniciarSesion(?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($x->usuario,$x->contrasena,$x->ip,$x->navegador,$x->proveedor_internet));
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = null;
        } catch (Exception $e){
            die($e->getMessage());
        }
    }
}
?>
