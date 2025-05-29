<?php

class StatsController {
    private $mysql;
    
    public function __construct($conexion) {
        $this->mysql = $conexion;
    }
    

    // =============== DASHBOARD GENERAL ===============
    
    public function getTotalRegistros() {
        $query = "SELECT COUNT(*) as total FROM datos_salud";
        $result = $this->mysql->query($query);
        return $result->fetch_assoc()['total'];
    }
    
    public function getPorcentajeAnemia() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 ELSE 0 END) as con_anemia
                  FROM datos_salud";
        $result = $this->mysql->query($query);
        $data = $result->fetch_assoc();
        return $data['total'] > 0 ? round(($data['con_anemia'] / $data['total']) * 100, 2) : 0;
    }
    
    public function getPromedioHemoglobina() {
        $query = "SELECT AVG(hemoglobina) as promedio FROM datos_salud WHERE hemoglobina IS NOT NULL";
        $result = $this->mysql->query($query);
        return round($result->fetch_assoc()['promedio'], 2);
    }
    
    public function getRangoAlturas() {
        $query = "SELECT MIN(altura_msnm) as minimo, MAX(altura_msnm) as maximo FROM datos_salud WHERE altura_msnm IS NOT NULL";
        $result = $this->mysql->query($query);
        return $result->fetch_assoc();
    }
    
    public function getTendenciaHemoglobinaPorEdad() {
        $query = "SELECT 
                    YEAR(CURDATE()) - YEAR(fecha_nacimiento) as edad,
                    AVG(hemoglobina) as promedio_hb
                  FROM datos_salud 
                  WHERE fecha_nacimiento IS NOT NULL AND hemoglobina IS NOT NULL
                  GROUP BY YEAR(CURDATE()) - YEAR(fecha_nacimiento)
                  ORDER BY edad";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getProporcionSexos() {
        $query = "SELECT sexo, COUNT(*) as cantidad FROM datos_salud GROUP BY sexo";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getUltimosRegistrosPorProvincia() {
        $query = "SELECT provincia, COUNT(*) as registros FROM datos_salud GROUP BY provincia ORDER BY registros DESC LIMIT 10";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getRelacionAnemiaHemoglobina() {
        $query = "SELECT 
                    CASE 
                        WHEN hemoglobina >= 12 THEN 'Normal'
                        WHEN hemoglobina >= 11 THEN 'Leve'
                        WHEN hemoglobina >= 8 THEN 'Moderada'
                        ELSE 'Severa'
                    END as rango_hb,
                    COUNT(*) as cantidad,
                    AVG(hemoglobina) as promedio_hb
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL
                  GROUP BY rango_hb";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // =============== DEMOGRAFÍA ===============
    
    public function getPersonasPorSexo() {
        $query = "SELECT sexo, COUNT(*) as cantidad FROM datos_salud GROUP BY sexo";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getHemoglobinaPorSexo() {
        $query = "SELECT sexo, hemoglobina FROM datos_salud WHERE hemoglobina IS NOT NULL ORDER BY sexo, hemoglobina";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getEdadVsHemoglobina() {
        $query = "SELECT 
                    YEAR(CURDATE()) - YEAR(fecha_nacimiento) as edad,
                    hemoglobina,
                    sexo
                  FROM datos_salud 
                  WHERE fecha_nacimiento IS NOT NULL AND hemoglobina IS NOT NULL";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPorcentajeAnemiaPorSexo() {
        $query = "SELECT 
                    sexo,
                    COUNT(*) as total,
                    SUM(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 ELSE 0 END) as con_anemia
                  FROM datos_salud 
                  GROUP BY sexo";
        $result = $this->mysql->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach($data as &$row) {
            $row['porcentaje_anemia'] = $row['total'] > 0 ? round(($row['con_anemia'] / $row['total']) * 100, 2) : 0;
        }
        
        return $data;
    }
    
    // =============== ESTABLECIMIENTOS ===============
    
    public function getRegistrosPorEstablecimiento() {
        $query = "SELECT establecimiento, COUNT(*) as registros FROM datos_salud WHERE establecimiento IS NOT NULL GROUP BY establecimiento ORDER BY registros DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPorcentajeAnemiaPorEstablecimiento() {
        $query = "SELECT 
                    establecimiento,
                    COUNT(*) as total,
                    SUM(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 ELSE 0 END) as con_anemia
                  FROM datos_salud 
                  WHERE establecimiento IS NOT NULL
                  GROUP BY establecimiento";
        $result = $this->mysql->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach($data as &$row) {
            $row['porcentaje_anemia'] = $row['total'] > 0 ? round(($row['con_anemia'] / $row['total']) * 100, 2) : 0;
        }
        
        return $data;
    }
    
    public function getRankingEstablecimientosAnemia() {
        $query = "SELECT 
                    establecimiento,
                    COUNT(*) as total,
                    SUM(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 ELSE 0 END) as con_anemia,
                    ROUND((SUM(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as porcentaje_anemia
                  FROM datos_salud 
                  WHERE establecimiento IS NOT NULL
                  GROUP BY establecimiento
                  ORDER BY porcentaje_anemia DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // =============== GEOGRAFÍA ===============
    
    public function getCasosPorDepartamento() {
        $query = "SELECT departamento, COUNT(*) as casos FROM datos_salud GROUP BY departamento ORDER BY casos DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCasosPorProvincia() {
        $query = "SELECT departamento, provincia, COUNT(*) as casos FROM datos_salud GROUP BY departamento, provincia ORDER BY casos DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCasosPorDistrito() {
        $query = "SELECT departamento, provincia, distrito, COUNT(*) as casos FROM datos_salud GROUP BY departamento, provincia, distrito ORDER BY casos DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDistritoVsHemoglobina() {
        $query = "SELECT 
                    distrito,
                    AVG(hemoglobina) as promedio_hb,
                    COUNT(*) as casos
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL
                  GROUP BY distrito
                  ORDER BY promedio_hb DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAlturaVsHemoglobina() {
        $query = "SELECT altura_msnm, hemoglobina FROM datos_salud WHERE altura_msnm IS NOT NULL AND hemoglobina IS NOT NULL";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // =============== SALUD ===============
    
    public function getEvolucionHemoglobinaPorEdad() {
        $query = "SELECT 
                    YEAR(CURDATE()) - YEAR(fecha_nacimiento) as edad,
                    AVG(hemoglobina) as promedio_hb,
                    COUNT(*) as casos
                  FROM datos_salud 
                  WHERE fecha_nacimiento IS NOT NULL AND hemoglobina IS NOT NULL
                  GROUP BY edad
                  ORDER BY edad";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDistribucionHemoglobinaPorDistrito() {
        $query = "SELECT distrito, hemoglobina FROM datos_salud WHERE distrito IS NOT NULL AND hemoglobina IS NOT NULL ORDER BY distrito";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPromedioHemoglobinaPorSexoUbicacion() {
        $query = "SELECT 
                    sexo,
                    departamento,
                    provincia,
                    AVG(hemoglobina) as promedio_hb,
                    COUNT(*) as casos
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL
                  GROUP BY sexo, departamento, provincia
                  ORDER BY promedio_hb DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // =============== RANGOS ===============
    
    public function getClasificacionHemoglobina() {
        $query = "SELECT 
                    CASE 
                        WHEN hemoglobina >= 12 THEN 'Normal'
                        WHEN hemoglobina >= 11 THEN 'Anemia Leve'
                        WHEN hemoglobina >= 8 THEN 'Anemia Moderada'
                        ELSE 'Anemia Severa'
                    END as clasificacion,
                    COUNT(*) as cantidad
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL
                  GROUP BY clasificacion
                  ORDER BY 
                    CASE clasificacion
                        WHEN 'Normal' THEN 1
                        WHEN 'Anemia Leve' THEN 2
                        WHEN 'Anemia Moderada' THEN 3
                        WHEN 'Anemia Severa' THEN 4
                    END";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getClasificacionPorSexo($sexo = null) {
        $whereClause = $sexo ? "WHERE hemoglobina IS NOT NULL AND sexo = '$sexo'" : "WHERE hemoglobina IS NOT NULL";
        
        $query = "SELECT 
                    sexo,
                    CASE 
                        WHEN hemoglobina >= 12 THEN 'Normal'
                        WHEN hemoglobina >= 11 THEN 'Anemia Leve'
                        WHEN hemoglobina >= 8 THEN 'Anemia Moderada'
                        ELSE 'Anemia Severa'
                    END as clasificacion,
                    COUNT(*) as cantidad
                  FROM datos_salud 
                  $whereClause
                  GROUP BY sexo, clasificacion
                  ORDER BY sexo, 
                    CASE clasificacion
                        WHEN 'Normal' THEN 1
                        WHEN 'Anemia Leve' THEN 2
                        WHEN 'Anemia Moderada' THEN 3
                        WHEN 'Anemia Severa' THEN 4
                    END";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // =============== ESTADÍSTICAS ===============
    
    public function getEstadisticosGenerales() {
        $query = "SELECT 
                    AVG(hemoglobina) as media,
                    COUNT(*) as total_registros,
                    MIN(hemoglobina) as minimo,
                    MAX(hemoglobina) as maximo,
                    STDDEV(hemoglobina) as desviacion_estandar
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL";
        $result = $this->mysql->query($query);
        $data = $result->fetch_assoc();
        
        // Calcular mediana
        $queryMediana = "SELECT hemoglobina FROM datos_salud WHERE hemoglobina IS NOT NULL ORDER BY hemoglobina";
        $resultMediana = $this->mysql->query($queryMediana);
        $valores = $resultMediana->fetch_all(MYSQLI_ASSOC);
        $count = count($valores);
        
        if ($count > 0) {
            if ($count % 2 == 0) {
                $data['mediana'] = ($valores[$count/2 - 1]['hemoglobina'] + $valores[$count/2]['hemoglobina']) / 2;
            } else {
                $data['mediana'] = $valores[floor($count/2)]['hemoglobina'];
            }
        } else {
            $data['mediana'] = 0;
        }
        
        return $data;
    }
    
    public function getComparacionDepartamentos() {
        $query = "SELECT 
                    departamento,
                    AVG(hemoglobina) as promedio_hb,
                    MIN(hemoglobina) as minimo_hb,
                    MAX(hemoglobina) as maximo_hb,
                    STDDEV(hemoglobina) as desviacion_hb,
                    COUNT(*) as casos
                  FROM datos_salud 
                  WHERE hemoglobina IS NOT NULL
                  GROUP BY departamento
                  ORDER BY promedio_hb DESC";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getRegresionAlturaHemoglobina() {
        $query = "SELECT altura_msnm, hemoglobina FROM datos_salud WHERE altura_msnm IS NOT NULL AND hemoglobina IS NOT NULL";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getBoxplotPorSexo() {
        $query = "SELECT sexo, hemoglobina FROM datos_salud WHERE hemoglobina IS NOT NULL ORDER BY sexo, hemoglobina";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getBoxplotPorUbicacion() {
        $query = "SELECT departamento, hemoglobina FROM datos_salud WHERE hemoglobina IS NOT NULL ORDER BY departamento, hemoglobina";
        $result = $this->mysql->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>