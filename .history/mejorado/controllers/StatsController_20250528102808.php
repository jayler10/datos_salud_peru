<?php
// controllers/StatsController.php - Controlador principal para estadísticas

class StatsController {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    /**
     * Obtener total de registros
     */
    public function getTotalRecords() {
        $result = $this->mysqli->query("SELECT COUNT(*) as total FROM datos_salud");
        return $result->fetch_assoc()['total'];
    }
    
    /**
     * Estadísticas por sexo
     */
    public function getGenderStats() {
    $query = "
        SELECT 
            sexo,
            COUNT(*) as cantidad,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
        FROM datos_salud 
        GROUP BY sexo
        ORDER BY cantidad DESC
    ";
    return $this->mysqli->query($query);
}

    
    /**
     * Estadísticas por departamento
     */
  public function getDepartmentStats() {
    $query = "
        SELECT 
            departamento,
            COUNT(*) as cantidad,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
        FROM datos_salud 
        GROUP BY departamento
        ORDER BY cantidad DESC
    ";
    return $this->mysqli->query($query);
}

    
    /**
     * Top provincias
     */
public function getTopProvinces($limit = 10) {
    // Validar que el límite sea un número entero seguro
    $limit = intval($limit); // Sanear el valor

    $query = "
        SELECT 
            provincia,
            COUNT(*) as cantidad,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
        FROM datos_salud 
        GROUP BY provincia
        ORDER BY cantidad DESC
        LIMIT $limit
    ";

    $result = $this->mysqli->query($query);

    if (!$result) {
        die('Error en la consulta: ' . $this->mysqli->error);
    }

    return $result;
}

    
    /**
     * Top distritos
     */
    public function getTopDistricts($limit = 15) {
        $query = "
            SELECT 
                distrito,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje
            FROM datos_salud 
            WHERE distrito != '' AND distrito IS NOT NULL
            GROUP BY distrito
            ORDER BY cantidad DESC
            LIMIT ?
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Estadísticas de anemia
     */
    public function getAnemiaStats() {
        $query = "
            SELECT 
                dx_anemia,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje
            FROM datos_salud 
            WHERE dx_anemia != '' AND dx_anemia IS NOT NULL
            GROUP BY dx_anemia
            ORDER BY cantidad DESC
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Estadísticas de hemoglobina
     */
    public function getHemoglobinStats() {
        $query = "
            SELECT 
                MIN(hemoglobina) as hb_min,
                MAX(hemoglobina) as hb_max,
                AVG(hemoglobina) as hb_promedio,
                VARIANCE(hemoglobina) as hb_varianza,
                STDDEV(hemoglobina) as hb_desviacion_tipica,
                COUNT(hemoglobina) as total_con_hb
            FROM datos_salud 
            WHERE hemoglobina IS NOT NULL AND hemoglobina > 0
        ";
        return $this->mysqli->query($query)->fetch_assoc();
    }
    
    /**
     * Estadísticas de altura
     */
    public function getAltitudeStats() {
        $query = "
            SELECT 
                MIN(altura_msnm) as alt_min,
                MAX(altura_msnm) as alt_max,
                AVG(altura_msnm) as alt_promedio,
                VARIANCE(altura_msnm) as alt_varianza,
                STDDEV(altura_msnm) as alt_desviacion_tipica,
                COUNT(altura_msnm) as total_con_altura
            FROM datos_salud 
            WHERE altura_msnm IS NOT NULL AND altura_msnm > 0
        ";
        return $this->mysqli->query($query)->fetch_assoc();
    }
    
    /**
     * Estadísticas de edad
     */
    public function getAgeStats() {
        $query = "
            SELECT 
                MIN(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_min,
                MAX(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_max,
                AVG(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_promedio,
                VARIANCE(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_varianza,
                STDDEV(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_desviacion_tipica,
                COUNT(*) as total_con_fecha
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
        ";
        return $this->mysqli->query($query)->fetch_assoc();
    }
    
    /**
     * Rangos de hemoglobina
     */
    public function getHemoglobinRanges() {
        $query = "
            SELECT 
                CASE 
                    WHEN hemoglobina < 7 THEN 'Muy Baja (< 7.0)'
                    WHEN hemoglobina >= 7 AND hemoglobina < 10 THEN 'Baja (7.0 - 9.9)'
                    WHEN hemoglobina >= 10 AND hemoglobina < 12 THEN 'Normal Baja (10.0 - 11.9)'
                    WHEN hemoglobina >= 12 AND hemoglobina < 15 THEN 'Normal (12.0 - 14.9)'
                    WHEN hemoglobina >= 15 THEN 'Alta (≥ 15.0)'
                    ELSE 'No Clasificado'
                END as rango_hb,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud WHERE hemoglobina IS NOT NULL), 2) as porcentaje
            FROM datos_salud 
            WHERE hemoglobina IS NOT NULL
            GROUP BY rango_hb
            ORDER BY 
                CASE 
                    WHEN rango_hb = 'Muy Baja (< 7.0)' THEN 1
                    WHEN rango_hb = 'Baja (7.0 - 9.9)' THEN 2
                    WHEN rango_hb = 'Normal Baja (10.0 - 11.9)' THEN 3
                    WHEN rango_hb = 'Normal (12.0 - 14.9)' THEN 4
                    WHEN rango_hb = 'Alta (≥ 15.0)' THEN 5
                    ELSE 6
                END
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Rangos de altitud
     */
    public function getAltitudeRanges() {
        $query = "
            SELECT 
                CASE 
                    WHEN altura_msnm < 1000 THEN 'Costa (< 1000m)'
                    WHEN altura_msnm >= 1000 AND altura_msnm < 2500 THEN 'Yunga (1000-2499m)'
                    WHEN altura_msnm >= 2500 AND altura_msnm < 3500 THEN 'Quechua (2500-3499m)'
                    WHEN altura_msnm >= 3500 AND altura_msnm < 4000 THEN 'Suni (3500-3999m)'
                    WHEN altura_msnm >= 4000 AND altura_msnm < 4800 THEN 'Puna (4000-4799m)'
                    WHEN altura_msnm >= 4800 THEN 'Janca (≥ 4800m)'
                    ELSE 'No Clasificado'
                END as rango_altura,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud WHERE altura_msnm IS NOT NULL), 2) as porcentaje
            FROM datos_salud 
            WHERE altura_msnm IS NOT NULL
            GROUP BY rango_altura
            ORDER BY 
                CASE 
                    WHEN rango_altura = 'Costa (< 1000m)' THEN 1
                    WHEN rango_altura = 'Yunga (1000-2499m)' THEN 2
                    WHEN rango_altura = 'Quechua (2500-3499m)' THEN 3
                    WHEN rango_altura = 'Suni (3500-3999m)' THEN 4
                    WHEN rango_altura = 'Puna (4000-4799m)' THEN 5
                    WHEN rango_altura = 'Janca (≥ 4800m)' THEN 6
                    ELSE 7
                END
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Top establecimientos
     */
    public function getTopEstablishments($limit = 10) {
        $query = "
            SELECT 
                establecimiento,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje
            FROM datos_salud 
            WHERE establecimiento != '' AND establecimiento IS NOT NULL
            GROUP BY establecimiento
            ORDER BY cantidad DESC
            LIMIT ?
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener resumen general para dashboard
     */
    public function getDashboardSummary() {
        $summary = [];
        
        // Total de registros
        $summary['total_records'] = $this->getTotalRecords();
        
        // Distribución por sexo
        $genderResult = $this->getGenderStats();
        $summary['gender'] = [];
        while($row = $genderResult->fetch_assoc()) {
            $summary['gender'][] = $row;
        }
        
        // Estadísticas de hemoglobina
        $summary['hemoglobin'] = $this->getHemoglobinStats();
        
        // Estadísticas de anemia (solo las principales)
        $anemiaResult = $this->getAnemiaStats();
        $summary['anemia'] = [];
        $count = 0;
        while($row = $anemiaResult->fetch_assoc() && $count < 5) {
            $summary['anemia'][] = $row;
            $count++;
        }
        
        return $summary;
    }
    
    // ============= NUEVAS FUNCIONES AGREGADAS =============
    
    /**
     * Obtener total de establecimientos únicos
     */
    public function getTotalEstablishments() {
        $result = $this->mysqli->query("SELECT COUNT(DISTINCT establecimiento) as total FROM datos_salud WHERE establecimiento IS NOT NULL AND establecimiento != ''");
        return $result->fetch_assoc()['total'];
    }
    
    /**
     * Obtener porcentaje total de anemia
     */
public function getTotalAnemiaPercentage() {
    $query = "
        SELECT 
            ROUND(
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' AND LOWER(dx_anemia) != 'normal' THEN 1 END) 
                * 100.0 / COUNT(*), 2
            ) as porcentaje_anemia
        FROM datos_salud
    ";
    return $this->mysqli->query($query)->fetch_assoc()['porcentaje_anemia'];
}

    /**
     * Grupos etarios para población
     */
    public function getAgeGroups() {
        $query = "
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 1 THEN '0-11 meses'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 1 AND 4 THEN '1-4 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 5 AND 9 THEN '5-9 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 10 AND 14 THEN '10-14 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 15 AND 19 THEN '15-19 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 20 AND 24 THEN '20-24 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 25 AND 29 THEN '25-29 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 30 AND 34 THEN '30-34 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 35 AND 39 THEN '35-39 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 40 AND 44 THEN '40-44 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 45 AND 49 THEN '45-49 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 50 AND 54 THEN '50-54 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 55 AND 59 THEN '55-59 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 60 THEN '60+ años'
                    ELSE 'Sin datos'
                END as grupo_edad,
                sexo,
                COUNT(*) as cantidad
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY grupo_edad, sexo
            ORDER BY 
                CASE 
                    WHEN grupo_edad = '0-11 meses' THEN 1
                    WHEN grupo_edad = '1-4 años' THEN 2
                    WHEN grupo_edad = '5-9 años' THEN 3
                    WHEN grupo_edad = '10-14 años' THEN 4
                    WHEN grupo_edad = '15-19 años' THEN 5
                    WHEN grupo_edad = '20-24 años' THEN 6
                    WHEN grupo_edad = '25-29 años' THEN 7
                    WHEN grupo_edad = '30-34 años' THEN 8
                    WHEN grupo_edad = '35-39 años' THEN 9
                    WHEN grupo_edad = '40-44 años' THEN 10
                    WHEN grupo_edad = '45-49 años' THEN 11
                    WHEN grupo_edad = '50-54 años' THEN 12
                    WHEN grupo_edad = '55-59 años' THEN 13
                    WHEN grupo_edad = '60+ años' THEN 14
                    ELSE 15
                END, sexo
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Distribución geográfica completa
     */
    public function getGeographicDistribution() {
        $query = "
            SELECT 
                departamento,
                provincia,
                distrito,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje,
                AVG(altura_msnm) as altura_promedio
            FROM datos_salud 
            GROUP BY departamento, provincia, distrito
            ORDER BY departamento, provincia, distrito
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Relación hemoglobina vs altitud
     */
    public function getHemoglobinAltitudeRelation() {
        $query = "
            SELECT 
                CASE 
                    WHEN altura_msnm < 1000 THEN 'Costa (< 1000m)'
                    WHEN altura_msnm >= 1000 AND altura_msnm < 2500 THEN 'Yunga (1000-2499m)'
                    WHEN altura_msnm >= 2500 AND altura_msnm < 3500 THEN 'Quechua (2500-3499m)'
                    WHEN altura_msnm >= 3500 AND altura_msnm < 4000 THEN 'Suni (3500-3999m)'
                    WHEN altura_msnm >= 4000 AND altura_msnm < 4800 THEN 'Puna (4000-4799m)'
                    WHEN altura_msnm >= 4800 THEN 'Janca (≥ 4800m)'
                    ELSE 'No Clasificado'
                END as rango_altura,
                COUNT(*) as total_casos,
                AVG(hemoglobina) as hb_promedio,
                MIN(hemoglobina) as hb_min,
                MAX(hemoglobina) as hb_max,
                STDDEV(hemoglobina) as hb_desviacion,
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) as casos_anemia,
                ROUND(COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) * 100.0 / COUNT(*), 2) as porcentaje_anemia
            FROM datos_salud 
            WHERE altura_msnm IS NOT NULL AND hemoglobina IS NOT NULL
            GROUP BY rango_altura
            ORDER BY 
                CASE 
                    WHEN rango_altura = 'Costa (< 1000m)' THEN 1
                    WHEN rango_altura = 'Yunga (1000-2499m)' THEN 2
                    WHEN rango_altura = 'Quechua (2500-3499m)' THEN 3
                    WHEN rango_altura = 'Suni (3500-3999m)' THEN 4
                    WHEN rango_altura = 'Puna (4000-4799m)' THEN 5
                    WHEN rango_altura = 'Janca (≥ 4800m)' THEN 6
                    ELSE 7
                END
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Anemia por sexo y grupo etario
     */
    public function getAnemiaBySexAndAge() {
        $query = "
            SELECT 
                sexo,
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 1 THEN '0-11 meses'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 1 AND 4 THEN '1-4 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 5 AND 11 THEN '5-11 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 12 AND 17 THEN '12-17 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 29 THEN '18-29 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 30 AND 59 THEN '30-59 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 60 THEN '60+ años'
                    ELSE 'Sin datos'
                END as grupo_edad,
                COUNT(*) as total_casos,
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) as casos_anemia,
                ROUND(COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) * 100.0 / COUNT(*), 2) as porcentaje_anemia,
                AVG(hemoglobina) as hb_promedio
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY sexo, grupo_edad
            ORDER BY sexo, 
                CASE 
                    WHEN grupo_edad = '0-11 meses' THEN 1
                    WHEN grupo_edad = '1-4 años' THEN 2
                    WHEN grupo_edad = '5-11 años' THEN 3
                    WHEN grupo_edad = '12-17 años' THEN 4
                    WHEN grupo_edad = '18-29 años' THEN 5
                    WHEN grupo_edad = '30-59 años' THEN 6
                    WHEN grupo_edad = '60+ años' THEN 7
                    ELSE 8
                END
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Top establecimientos con indicadores de salud
     */
    public function getEstablishmentsHealthIndicators($limit = 15) {
        $query = "
            SELECT 
                establecimiento,
                COUNT(*) as total_casos,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje_casos,
                AVG(hemoglobina) as hb_promedio,
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) as casos_anemia,
                ROUND(COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) * 100.0 / COUNT(*), 2) as porcentaje_anemia,
                AVG(altura_msnm) as altura_promedio,
                departamento,
                provincia
            FROM datos_salud 
            WHERE establecimiento IS NOT NULL AND establecimiento != ''
            GROUP BY establecimiento, departamento, provincia
            ORDER BY total_casos DESC
            LIMIT ?
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Resumen de cobertura geográfica
     */
    public function getGeographicCoverage() {
        $query = "
            SELECT 
                COUNT(DISTINCT departamento) as total_departamentos,
                COUNT(DISTINCT provincia) as total_provincias,
                COUNT(DISTINCT distrito) as total_distritos,
                COUNT(DISTINCT establecimiento) as total_establecimientos
            FROM datos_salud 
            WHERE departamento IS NOT NULL AND departamento != ''
        ";
        return $this->mysqli->query($query)->fetch_assoc();
    }
}
?>