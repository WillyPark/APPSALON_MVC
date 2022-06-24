<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController{
    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios); //Enviamos los datos convertidos a JSON
    }

    public static function guardar(){
        //Almacena la Cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado["id"];

        //Almacena la Cita y el Servicio
        $idServicios = explode(",", $_POST["servicios"]);

        //Almacena los servicios con el Id de la cita
        foreach( $idServicios as $idServicio ){
            $args = [
                "citaId" => $id,
                "servicioId" => $idServicio
            ];

            $citaServicio = new CitaServicio( $args );
            $citaServicio->guardar();
        }

        //Retornamos una respuesta
        // $respuesta = [
        //     "resultado" => $resultado
        // ]; Este código se puede simplificar con el del argumento de abajo

        echo json_encode(["resultado" => $resultado]);
    }

    public static function eliminar(){
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];

            $cita = Cita::find($id);
            $cita->eliminar();
            //TODO: Añadir notificación de eliminado correctamente
            header("Location:" . $_SERVER["HTTP_REFERER"]);
        }
    }
}