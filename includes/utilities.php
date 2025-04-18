<?php

if (!function_exists('tableExists')) {
    function tableExists($tableName = null) {
        
        $args = func_get_args();
        if (count($args) > 1 && is_object($args[0]) && $args[0] instanceof PDO) {
            $pdo = $args[0];
            $tableName = $args[1];
        } else {
            global $pdo;
        }
        
        if (!$tableName) return false;
        
        try {
            $result = $pdo->query("SHOW TABLES LIKE '{$tableName}'");
            return ($result && $result->rowCount() > 0);
        } catch (Exception $e) {
            error_log("Error checking if table {$tableName} exists: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('safeQueryFetch')) {
    function safeQueryFetch($sql, $params = []) {
        global $pdo;
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in safeQueryFetch: " . $e->getMessage());
            return [];
        }
    }
}

?>