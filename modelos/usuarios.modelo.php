<?php
require_once 'db.modelo.php';
Class Usuarios extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ListarUsuarios(){
        try {
            $sql = "SELECT * FROM vmostrarusuarios WHERE rol != 'SUPER USUARIO' ORDER BY apellidos,nombres;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarRoles(){
        try {
            $sql = "SELECT * FROM roles WHERE estado IS TRUE AND nombre != 'SUPER USUARIO';";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ActualizarUsuarios(Usuarios $x){
        try {
            $sql = "SELECT * FROM ActualizarUsuarios(?,?,?,?,?,?,?,?,?,?,?,?,?,?) AS resultado;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array(
                $x->id,
                $x->cedula,
                $x->apellidos,
                $x->nombres,
                $x->telefono,
                $x->correo,
                $x->contrasena,
                $x->direccion,
                $x->parroquia,
                $x->rol,
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

 

    public function ValidarExisteUsuario(Usuarios $x){
        try {
            $sql = "SELECT * FROM ValidarExisteUsuario(?,?) AS resultado;";
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