<?php
require_once 'db.modelo.php';
Class Login{
	private $pdo;
	public function __CONSTRUCT(){
		try{
			$this->pdo = Database::Conectar();
		}
		catch(Exception $e){
			die($e->getMessage());
		}
	}
	public function IniciarSesion(Login $x){
		try{
			$sql = "SELECT * FROM GWeb_con_cod_usuario_buque WHERE email = ?";
			$stm = $this->pdo->prepare($sql);
			$stm->execute(array($x->usuario));
			return $stm->fetchAll(PDO::FETCH_OBJ);
			$stm = null;
		} catch (Exception $e){
			die($e->getMessage());
		}
	}
}

?>
