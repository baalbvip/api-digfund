<?php

namespace Connection;

use PDO;
use PDOException;
use Exception;

class DB
{

    protected static $conn;

    private function __construct()
    {
        $servername = "tcp:serverdigf.database.windows.net,1433";
        $username = "adminsite";
        $password = "Desazure1+";
        $database = "DIGFUNDES";

        try {
            $conn = new PDO("sqlsrv:Server=$servername;Database=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn = $conn;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }

    public static function getConnection()
    {
        if (!self::$conn) {
            new self();
        }
        return self::$conn;
    }

    public static function query($query, $values = [])
    {
        $prepare = self::getConnection()->prepare($query);

        if (count($values) == 0) {
            $prepare->execute();
        } else {
            $prepare->execute($values);
        }

        return $prepare->fetchAll();
    }

    public static function procedure($procedure)
    {
        $prepare = self::getConnection()->prepare($procedure);
        $exec = $prepare->execute();

        // Verificar si hay filas retornadas

        try {

            $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows)) {
                return $rows;
            } else {
                return $exec; // O cualquier valor que desees devolver si no hay filas
            }
        } catch (Exception $err) {
            return $exec;
        }
    }

    public static function insert($query, $values)
    {

        try {
            $prepare = self::getConnection()->prepare($query);
            $exec = $prepare->execute($values);

            if ($exec) {
                return self::getConnection()->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $err) {
            print $err;
        }
    }
}
