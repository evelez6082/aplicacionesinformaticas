<?php
 class Database{   
  public static function Conectar(){
    try {
	  	$pdo = new PDO('pgsql:host=ec2-18-233-83-165.compute-1.amazonaws.com;port=5432;dbname=d5bs7kbtqra7a;', 'dwpxscotfqlhre', '566767d0f6184fed0fca8af08430a13315d3d458f48f878294cef994a9ef9a5d',array(PDO::ATTR_PERSISTENT => true));
      // $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=heladeriadb;', 'postgres', 'similaresoldofa',array(PDO::ATTR_PERSISTENT => true));
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	return $pdo;  
      #return true; 
      $pdo = null;
  	} catch (Exception $e) {
      echo "Error de conexión: " . $e->getMessage();
  	}
  } 
} 
 
?> 