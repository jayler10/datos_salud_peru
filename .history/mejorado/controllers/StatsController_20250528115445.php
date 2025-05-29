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
        $query = "
            SELECT 
                provincia,
                COUNT(*) as cantidad,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
            FROM datos_salud 
            GROUP BY provincia
            ORDER BY cantidad DESC
            LIMIT ?
        ";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Top distritos
     */
    public function getTopDistricts($limit = 15) {
        $query = "
            SELECT 
                distrito,
                COUNT(*) as cantidad,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
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
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
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
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud WHERE hemoglobina IS NOT NULL), 2) as porcentaje
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
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud WHERE altura_msnm IS NOT NULL), 2) as porcentaje
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
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM datos_salud), 2) as porcentaje
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
     * Distribución por grupos etarios
     */
    public function getAgeGroups() {
        $query = "
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 1 THEN '0-1 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 1 AND 5 THEN '1-5 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 6 AND 12 THEN '6-12 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 30 THEN '18-30 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 31 AND 50 THEN '31-50 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 51 AND 65 THEN '51-65 años'
                    ELSE '65+ años'
                END as grupo_edad,
                COUNT(*) as cantidad,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud WHERE fecha_nacimiento IS NOT NULL), 2) as porcentaje,
                ROUND(AVG(hemoglobina), 2) as hemoglobina_promedio
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY grupo_edad
            ORDER BY MIN(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()))
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Análisis detallado por sexo
     */
    public function getDetailedGenderAnalysis() {
        $query = "
            SELECT 
                sexo,
                COUNT(*) as total_casos,
                ROUND(COUNT() * 100.0 / (SELECT COUNT() FROM datos_salud), 2) as porcentaje_total,
                ROUND(AVG(hemoglobina), 2) as hemoglobina_promedio,
                MIN(hemoglobina) as hemoglobina_minima,
                MAX(hemoglobina) as hemoglobina_maxima,
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) as casos_anemia,
                ROUND(COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) * 100.0 / COUNT(*), 2) as porcentaje_anemia
            FROM datos_salud 
            GROUP BY sexo
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Distribución por rangos de edad específicos
     */
    public function getSpecificAgeRanges() {
        $query = "
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) < 6 THEN '0-6 meses'
                    WHEN TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) BETWEEN 6 AND 11 THEN '6-11 meses'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 1 AND 2 THEN '1-2 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 3 AND 5 THEN '3-5 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 6 AND 11 THEN '6-11 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 12 AND 17 THEN '12-17 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 29 THEN '18-29 años'
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 30 AND 59 THEN '30-59 años'
                    ELSE '60+ años'
                END as rango_edad,
                sexo,
                COUNT(*) as cantidad,
                ROUND(AVG(hemoglobina), 2) as hemoglobina_promedio
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY rango_edad, sexo
            ORDER BY MIN(TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE())), sexo
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Análisis de natalidad por año
     */
    public function getBirthYearAnalysis() {
        $query = "
            SELECT 
                YEAR(fecha_nacimiento) as año_nacimiento,
                COUNT(*) as nacimientos,
                ROUND(AVG(hemoglobina), 2) as hemoglobina_promedio,
                COUNT(CASE WHEN sexo = 'M' THEN 1 END) as masculino,
                COUNT(CASE WHEN sexo = 'F' THEN 1 END) as femenino
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY YEAR(fecha_nacimiento)
            ORDER BY año_nacimiento DESC
            LIMIT 20
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Patrones demográficos por mes de nacimiento
     */
    public function getBirthMonthPatterns() {
        $query = "
            SELECT 
                MONTH(fecha_nacimiento) as mes,
                MONTHNAME(fecha_nacimiento) as nombre_mes,
                COUNT(*) as nacimientos,
                ROUND(AVG(hemoglobina), 2) as hemoglobina_promedio,
                COUNT(CASE WHEN dx_anemia IS NOT NULL AND dx_anemia != '' THEN 1 END) as casos_anemia
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
            GROUP BY MONTH(fecha_nacimiento), MONTHNAME(fecha_nacimiento)
            ORDER BY mes
        ";
        return $this->mysqli->query($query);
    }
    
    /**
     * Estadísticas de edad actual
     */
    public function getCurrentAgeStats() {
        $query = "
            SELECT 
                ROUND(AVG(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())), 2) as edad_promedio,
                MIN(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_minima,
                MAX(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) as edad_maxima,
                COUNT(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18 THEN 1 END) as menores_edad,
                COUNT(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 18 THEN 1 END) as mayores_edad
            FROM datos_salud 
            WHERE fecha_nacimiento IS NOT NULL
        ";
        return $this->mysqli->query($query)->fetch_assoc();
    }



public function getAnemiaByGender() {
    $query = "
        SELECT 
            sexo,
            dx_anemia,
            COUNT(*) AS cantidad,
            ROUND((COUNT(*) / SUM(COUNT(*)) OVER(PARTITION BY sexo)) * 100, 2) AS porcentaje_por_sexo,
            ROUND((COUNT(*) / SUM(COUNT(*)) OVER()) * 100, 2) AS porcentaje_total
        FROM datos_salud
        WHERE dx_anemia IS NOT NULL AND sexo IS NOT NULL
        GROUP BY sexo, dx_anemia
        ORDER BY sexo, FIELD(dx_anemia, 'Sin Anemia', 'Leve', 'Moderada', 'Severa', 'Grave', 'Otro', 'Desconocido', 'No evaluado'), dx_anemia
    ";

    return $this->mysqli->query($query);
}

public function getAnemiaByAltitudeRange() {
    $query = "
        SELECT
            CASE
                WHEN altura_msnm < 500 THEN '< 500 msnm'
                WHEN altura_msnm BETWEEN 500 AND 1499 THEN '500 - 1499 msnm'
                WHEN altura_msnm BETWEEN 1500 AND 2499 THEN '1500 - 2499 msnm'
                WHEN altura_msnm BETWEEN 2500 AND 3499 THEN '2500 - 3499 msnm'
                WHEN altura_msnm >= 3500 THEN '≥ 3500 msnm'
                ELSE 'Sin dato'
            END AS rango_altitud,
            dx_anemia,
            COUNT(*) AS cantidad
        FROM datos_salud
        WHERE dx_anemia IS NOT NULL
        GROUP BY rango_altitud, dx_anemia
        ORDER BY FIELD(rango_altitud, '< 500 msnm', '500 - 1499 msnm', '1500 - 2499 msnm', '2500 - 3499 msnm', '≥ 3500 msnm', 'Sin dato'),
                 FIELD(dx_anemia, 'Sin Anemia', 'Leve', 'Moderada', 'Severa', 'Grave', 'Otro', 'Desconocido', 'No evaluado')
    ";
    return $this->mysqli->query($query);
}






public function getHemoglobinRangesBySex() {
    $query = "
        SELECT 
            sexo,
            CASE
                WHEN hemoglobina < 11 THEN '< 11 g/dL'
                WHEN hemoglobina BETWEEN 11 AND 11.9 THEN '11-11.9 g/dL'
                WHEN hemoglobina BETWEEN 12 AND 13.9 THEN '12-13.9 g/dL'
                WHEN hemoglobina >= 14 THEN '>= 14 g/dL'
                ELSE 'Sin dato'
            END AS rango_hb,
            COUNT(*) AS cantidad,
            ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM datos_salud WHERE sexo = ds.sexo AND hemoglobina IS NOT NULL), 2) AS porcentaje
        FROM datos_salud ds
        WHERE hemoglobina IS NOT NULL
        GROUP BY sexo, rango_hb
        ORDER BY sexo, rango_hb
    ";

    return $this->mysqli->query($query);
}



public function getAnemiaByAgeRanges() {
    $query = "
        SELECT 
            CASE
                WHEN edad BETWEEN 0 AND 4 THEN '0-4 años'
                WHEN edad BETWEEN 5 AND 9 THEN '5-9 años'
                WHEN edad BETWEEN 10 AND 14 THEN '10-14 años'
                WHEN edad BETWEEN 15 AND 19 THEN '15-19 años'
                WHEN edad BETWEEN 20 AND 29 THEN '20-29 años'
                WHEN edad BETWEEN 30 AND 39 THEN '30-39 años'
                WHEN edad BETWEEN 40 AND 49 THEN '40-49 años'
                WHEN edad >= 50 THEN '50+ años'
                ELSE 'Sin edad'
            END AS rango_edad,
            dx_anemia,
            COUNT(*) AS cantidad,
            ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM datos_salud WHERE edad IS NOT NULL), 2) AS porcentaje
        FROM datos_salud
        WHERE dx_anemia IS NOT NULL AND edad IS NOT NULL
        GROUP BY rango_edad, dx_anemia
        ORDER BY rango_edad
    ";

    return $this->mysqli->query($query);
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
}
?>