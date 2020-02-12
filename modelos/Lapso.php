<?php

#Se incluye la conexión a la base de datos
require_once '../config/conexion.php';

/**
 * Modelo de Lapso
 */
class Lapso
{

    #Constructor de la clase
    public function __construct()
    { }

    #Método para insertar registros
    function insertar($lapso, $estatus)
    {
        $sql = "INSERT INTO lapso (lapso, estatus) VALUES('$lapso', '$estatus')";

        return ejecutarConsulta($sql);
    }

    #Método para editar lapsos
    function editar($idlapso, $lapso, $estatus)
    {
        $sql = "UPDATE lapso SET lapso='$lapso', estatus = '$estatus' WHERE id = '$idlapso'";

        return ejecutarConsulta($sql);
    }

    #Método para listar todos los lapsos
    function listar()
    {
        $sql = "SELECT * FROM lapso ORDER BY lapso";

        return ejecutarConsulta($sql);
    }

    #Método para mostrar un lapso
    function mostrar($idlapso)
    {
        $sql = "SELECT * FROM lapso WHERE id = '$idlapso'";

        return ejecutarConsultaSimpleFila($sql);
    }

    #Método para desactivar lapso
    function desactivar($idlapso)
    {
        $sql = "UPDATE lapso SET estatus = '0' WHERE id = '$idlapso'";

        return ejecutarConsulta($sql);
    }

    #Método para activar lapso
    function activar($idlapso)
    {
        $sql = "UPDATE lapso SET estatus = '1' WHERE id = '$idlapso'";

        return ejecutarConsulta($sql);
    }

    #Método para comprobar si existe el Lapso
    function comprobarlapso($lapso)
    {
        $sql = "SELECT lapso FROM lapso WHERE lapso = '$lapso'";
        return ejecutarConsulta($sql);
    }
}
