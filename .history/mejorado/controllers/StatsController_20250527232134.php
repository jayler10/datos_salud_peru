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