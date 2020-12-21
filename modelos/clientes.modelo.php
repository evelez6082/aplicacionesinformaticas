<?php
require_once 'db.modelo.php';
Class Clientes extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ListarClientes(){
        try {
            $sql = "SELECT * FROM vmostrarclientes WHERE cedula != '1111111111' ORDER BY apellidos,nombres;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ActualizarClientes(Clientes $x){
        try {
            $sql = "SELECT * FROM ActualizarClientes(?,?,?,?,?,?,?,?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array(
                $x->id,
                $x->cedula,
                $x->apellidos,
                $x->nombres,
                $x->telefono,
                $x->correo,
                $x->direccion,
                $x->parroquia,
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

    public function CargarClientes(){
        try {
            $sql = "SELECT * FROM vmostrarclientes WHERE estado IS TRUE";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage()); 
        }
    }

 

    public function ValidarExisteCliente(Clientes $x){
        try {
            $sql = "SELECT * FROM ValidarExisteCliente(?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array(
                $x->cedula,
                $x->usuario
            )); 
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
	}
}
?>