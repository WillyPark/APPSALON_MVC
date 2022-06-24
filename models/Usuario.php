<?php

namespace Model;

class Usuario extends ActiveRecord{
    //Base de Datos
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id", "nombre", "apellido", "email", "password", "telefono", "admin", "confirmado", "token"];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct( $args = [] ){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->apellido = $args["apellido"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->telefono = $args["telefono"] ?? "";
        $this->admin = $args["admin"] ?? 0;
        $this->confirmado = $args["confirmado"] ?? 0;
        $this->token = $args["token"] ?? "";
    }

    //Mensajes de Validación para la Creación de una Cuenta
    public function validarNuevaCuenta(){
        if (!$this->nombre) {
            self::$alertas["error"][] = "El Nombre es Obligatorio";
        }

        if (!$this->apellido) {
            self::$alertas["error"][] = "El Apellido es Obligatorio";
        }

        if (strlen($this->telefono) <= 9 || strlen($this->telefono) >= 11) {
            self::$alertas["error"][] = "El número de teléfono debe contener 10 dígitos";
        }

        if (!$this->email) {
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if (!$this->password) {
            self::$alertas["error"][] = "El Password es Obligatorio";
        }
        
        if ( strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password debe contener al menos 6 caracteres";
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if (!$this->email) {
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if (!$this->password) {
            self::$alertas["error"][] = "El Password es Obligatorio";
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if (!$this->email) {
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        return self::$alertas;
    }
    
    public function validarPassword(){
        if (!$this->password) {
            self::$alertas["error"][] = "El Password es Obligatorio";
        }

        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password debe tener al menos 6 caracteres";
        }
    
        return self::$alertas;
    }

    //Revisa si el Usuario ya existe
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query( $query );

        if ( $resultado->num_rows ) {
            self::$alertas["error"][] = "Ya existe un Usuario con ese correo";
        }
        
        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash( $this->password, PASSWORD_BCRYPT );
    }

    public function crearToken(){
        $this->token = uniqid(); //13 digitos
    }

    public function comprobarPasswordAndVerificado($password){
        $resultado = password_verify($password, $this->password);
        
        if (!$resultado || !$this->confirmado) {
            self::$alertas["error"][] = "Password Incorrecto o tu Cuenta no ha sido Confirmada";
        }else {
            return true;
        }
    }
}