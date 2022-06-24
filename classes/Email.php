<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;

    public function __construct( $email, $nombre, $token ){
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        //Crear el objeto de Mail
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '8d538b01315eac';
        $mail->Password = 'f9584d3e2f82d5';

        $mail->setFrom("cuentas@appsalon.com");
        $mail->addAddress("cuentas@appsalon.com", "AppSalon.com");
        $mail->Subject = "Confirma tu Cuenta";

        //Set HTML
        $mail->isHTML( TRUE );
        $mail->CharSet = "UTF-8";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> has creado tu cuenta en AppSalón, solo debes confirmarla presionando el siguiente enlace<p/>";
        $contenido .= "<p>Presiona aquí: <a href='http:localhost:5000/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta<a/></p>";
        $contenido .= "<p>Si tu no realizaste esta cuenta, ignora el mensaje.</p>";

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function enviarInstrucciones(){
        //Crear el objeto de Mail
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '8d538b01315eac';
        $mail->Password = 'f9584d3e2f82d5';

        $mail->setFrom("cuentas@appsalon.com");
        $mail->addAddress("cuentas@appsalon.com", "AppSalon.com");
        $mail->Subject = "Reestablece tu Password";

        //Set HTML
        $mail->isHTML( TRUE );
        $mail->CharSet = "UTF-8";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo.<p/>";
        $contenido .= "<p>Presiona aquí: <a href='http:localhost:5000/recuperar?token=" . $this->token . "'>Restablecer Password<a/></p>";
        $contenido .= "<p>Si tu no realizaste esta cuenta, ignora el mensaje.</p>";

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }
}