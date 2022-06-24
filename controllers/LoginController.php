<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login( Router $router ){
        $alertas = [];

        $auth = new Usuario();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);

                if ($usuario) {
                    //Verificar el Password
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ){
                        //Autenticar el Usuario
                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        $_SESSION["id"] = $usuario->id; 
                        $_SESSION["nombre"] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        //Redireccionamiento
                        if($usuario->admin === "1"){
                            $_SESSION["admin"] = $usuario->admin ?? null;

                            header("Location: /admin");
                        }else {
                            header("Location: /cita");
                        }
                    }
                }else{
                    Usuario::setAlerta("error", "Usuario No Encontrado");
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/login", [
            "alertas" => $alertas,
            "auth" => $auth
        ]);
    }

    public static function logout(){
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION = [];

        header("Location: /");
    }

    public static function olvide( Router $router ){
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)){
                $usuario = Usuario::where("email", $auth->email);

                if( $usuario && $usuario->confirmado === "1" ){
                    //generar un Token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar el Email
                    $email = new Email( $usuario->email, $usuario->nombre, $usuario->token );
                    $email->enviarInstrucciones();

                    //Alerta de éxito
                    Usuario::setAlerta("exito", "Revisa tu Email y sigue las instrucciones para restablecer tu contraseña");
                }else {
                    Usuario::setAlerta("error", "El Usuario no existe o No está Confirmado");
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/olvide-password",[
            "alertas" => $alertas
        ]);
    }

    public static function recuperar( Router $router ){
        $alertas = [];
        $error = false;

        $token = s($_GET["token"]);

        //Buscar Usuario por su Token
        $usuario = Usuario::where("token", $token);

        if(empty($usuario)){
            Usuario::setAlerta("error", "Token No Válido");
            $error = true;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            //Leer y Guardar Nuevo Password
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado) {
                    Usuario::setAlerta("exito", "Password Actualizado Correctamente. En breve sera redireccionado al Login");

                    header("Refresh: 4; url=/"); //Redireccionas al Usuario tras 4 segundos
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/recuperar-password", [
            "alertas" => $alertas,
            "error" => $error
        ]);
    }

    public static function crear( Router $router ){

        $usuario = new Usuario();

        //Alertas vacias
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alerta este vacio
            if( empty($alertas) ){
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear el Password
                    $usuario->hashPassword();

                    //Generar un Token único
                    $usuario->crearToken();

                    //Enviar el Email
                    $email = new Email( $usuario->email, $usuario->nombre, $usuario->token );
                    $email->enviarConfirmacion();

                    //Crear el Usuario
                    $resultado = $usuario->guardar();

                    if($resultado){
                        header("Location: /mensaje");
                    }
                }
            }
        }

        $router->render("auth/crear-cuenta", [
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function mensaje( Router $router ){
        $router->render("auth/mensaje");
    }

    public static function confirmar( Router $router ){
        $alertas = [];
        $token = s($_GET["token"]);

        $usuario = Usuario::where("token", $token);

        if (empty($usuario) || $usuario->token == null) {
            Usuario::setAlerta("error", "Token No Válido");
        }else{
            $usuario->confirmado = 1;
            $usuario->token = NULL;
            $usuario->guardar();

            Usuario::setAlerta("exito", "Cuenta Comprobada Correctamente");
        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }
}