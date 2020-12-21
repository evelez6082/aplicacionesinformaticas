<?php
require_once 'db.modelo.php';
Class Reportes extends database {
    private $pdo;

    public function __CONSTRUCT(){
        try {
            $this->pdo = DataBase::Conectar();
        }catch (Exception $e) {
            die($e->getMessage());
        }    
    }

    public function ReporteGeneral(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        SUM(precio) ingresos,
                        SUM(costo) costos,
                        (SUM(precio) - SUM(costo)) utilidad,
                        ((SUM(precio) - SUM(costo)) / SUM(precio)) margen
                        FROM vmostrarreportesgenerales WHERE $agrupar BETWEEN ? AND ? GROUP BY $agrupar ORDER BY $agrupar DESC";
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        SUM(precio) ingresos,
                        SUM(costo) costos,
                        (SUM(precio) - SUM(costo)) utilidad,
                        ((SUM(precio) - SUM(costo)) / SUM(precio)) margen
                        FROM vmostrarreportesgenerales GROUP BY $agrupar ORDER BY $agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute();
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoTipoProducto(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                if($x->tipo_producto === '2'){
                    $sql = "
                    SELECT $agrupar fecha,
                        tipo_producto,
                        SUM(cantidad) cantidad,
                        SUM(costo) costos,
                        SUM(precio) ventas,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportestiposproductospersonalizados WHERE idtipo_producto = ? AND $agrupar BETWEEN ? AND ? GROUP BY $agrupar,tipo_producto ORDER BY $agrupar DESC";
                }else{
                    $sql = "
                    SELECT $agrupar fecha,
                        tipo_producto,
                        SUM(cantidad) cantidad,
                        SUM(costo) costos,
                        SUM(precio) ventas,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportestiposproductosnopersonalizados WHERE idtipo_producto = ? AND $agrupar BETWEEN ? AND ? GROUP BY $agrupar,tipo_producto ORDER BY $agrupar DESC";
                }

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto,$x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                if($x->tipo_producto === '2'){
                    $sql = "
                    SELECT $agrupar fecha,
                        tipo_producto,
                        SUM(cantidad) cantidad,
                        SUM(costo) costos,
                        SUM(precio) ventas,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportestiposproductospersonalizados WHERE idtipo_producto = ? GROUP BY $agrupar,tipo_producto ORDER BY $agrupar DESC";
                }else{
                    $sql = "
                    SELECT $agrupar fecha,
                        tipo_producto,
                        SUM(cantidad) cantidad,
                        SUM(costo) costos,
                        SUM(precio) ventas,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportestiposproductosnopersonalizados WHERE idtipo_producto = ? GROUP BY $agrupar,tipo_producto ORDER BY $agrupar DESC";
                }
                
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoProductosIndividuales(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(costo) costo,
                        SUM(precio) precio,
                        (SUM(costo)/SUM(1)) costo_real,
                        (SUM(precio)/SUM(1)) precio_real,
                        SUM(1) cantidad,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportesproproductos WHERE idproducto = ? AND $agrupar BETWEEN ? AND ? GROUP BY producto,$agrupar ORDER BY $agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->producto,$x->desde,$x->hasta));
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(costo) costo,
                        SUM(precio) precio,
                        (SUM(costo)/SUM(1)) costo_real,
                        (SUM(precio)/SUM(1)) precio_real,
                        SUM(1) cantidad,
                        (SUM(precio) - SUM(costo)) ganancia
                        FROM vmostrarreportesproproductos WHERE idproducto = ? GROUP BY producto,$agrupar ORDER BY $agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->producto));
            }
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoTamanoProductos(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        precio,
                        costo,
                        SUM(costo) costo_total,
                        SUM(precio_total) venta,                        
                        SUM(1) cantidad
                        FROM vmotrarreportestamanosproductos WHERE idtamano_producto = ? AND $agrupar BETWEEN ? AND ? GROUP BY producto,costo,precio,$agrupar ORDER BY cantidad DESC,$agrupar DESC";
                        
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tamano_producto,$x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        precio,
                        costo,
                        SUM(costo) costo_total,
                        SUM(precio_total) venta,                        
                        SUM(1) cantidad
                        FROM vmotrarreportestamanosproductos WHERE idtamano_producto = ? GROUP BY producto,costo,precio,$agrupar ORDER BY cantidad DESC,$agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tamano_producto));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoMasVendidos(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        idproducto,
                        producto,
                        precio,
                        costo,
                        SUM(costo) costo_total,
                        SUM(precio_total) venta,                        
                        SUM(1) cantidad
                        FROM vmotrarreportesproductosmasvendidos WHERE $agrupar BETWEEN ? AND ? GROUP BY idproducto,producto,costo,precio,$agrupar ORDER BY cantidad DESC,$agrupar DESC,producto";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        idproducto,
                        producto,
                        precio,
                        costo,
                        SUM(costo) costo_total,
                        SUM(precio_total) venta,                        
                        SUM(1) cantidad
                        FROM vmotrarreportesproductosmasvendidos GROUP BY idproducto,producto,costo,precio,$agrupar ORDER BY cantidad DESC,$agrupar DESC,producto";

                $stm = $this->pdo->prepare($sql);
                $stm->execute();
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoOtros(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(precio) precio,
                        SUM(venta) venta,
                        SUM(cantidad) cantidad
                        FROM vmostrarotrosreportes WHERE tipo_producto = ? AND $agrupar BETWEEN ? AND ? GROUP BY producto,$agrupar ORDER BY cantidad DESC,$agrupar DESC";
                    
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto,$x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(precio) precio,
                        SUM(venta) venta,
                        SUM(cantidad) cantidad
                    FROM vmostrarotrosreportes WHERE tipo_producto = ? GROUP BY producto,$agrupar ORDER BY cantidad DESC,$agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ReporteEspecificoOtrosAdicionales(Reportes $x){
        try {
            if($x->fechas === '1'){
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(precio) precio,
                        SUM(venta) venta,
                        SUM(cantidad) cantidad
                        FROM vmostrarotrosreportes WHERE tipo_producto = ? AND idproducto = ? AND $agrupar BETWEEN ? AND ? GROUP BY producto,$agrupar ORDER BY cantidad DESC,$agrupar DESC";
                    
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto,$x->idproducto,$x->desde,$x->hasta));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }else{
                $agrupar = $this->Agrupar($x->agrupar);
                $sql = "
                    SELECT $agrupar fecha,
                        producto,
                        SUM(precio) precio,
                        SUM(venta) venta,
                        SUM(cantidad) cantidad
                    FROM vmostrarotrosreportes WHERE tipo_producto = ? AND idproducto = ? GROUP BY producto,$agrupar ORDER BY cantidad DESC,$agrupar DESC";

                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($x->tipo_producto,$x->idproducto));
                return $stm->fetchAll(PDO::FETCH_OBJ);
                $stm = NULL;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarTamanosProductos(){
        try {
            $sql = "SELECT *  FROM  vmostrartamanosproductos ORDER BY id";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function CargarProductosAdicionales(){
        try {
            $sql = "SELECT * FROM vmostrarproductos WHERE tipo_producto = 'ADICIONALES' ORDER BY nombre DESC;";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
            $stm = NULL;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    private function Agrupar($agrupar){
        if($agrupar === '1'){
            return "date_trunc('minute',fecha)";
        }elseif($agrupar === '2'){
            return "date_trunc('hour',fecha)";
        }elseif($agrupar === '3'){
            return "DATE(date_trunc('day',fecha))";
        }elseif($agrupar === '4'){
            return "DATE(date_trunc('week',fecha))";
        }elseif($agrupar === '5'){
            return "DATE(date_trunc('month',fecha))";
        }else{
            return 'fecha';
        }
    }
}
?>