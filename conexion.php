<?php

function getConnection() {
    //$conn=pg_connect("host=10.65.140.67 dbname=actividades_asesores user=postgres password=Animo");
    //$conn=pg_connect("host=127.0.0.1 dbname=actividades_reparaciones user=postgres password=Animo");
    $conn=pg_connect("host=10.65.164.18 dbname=seguimiento user=postgres password=Animo");
    if (!$conn) {
        return false;
    }
    return $conn;
}


?>

