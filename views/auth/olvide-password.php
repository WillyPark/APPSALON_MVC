<h1 class="nombre-pagina">Olvide Password</h1>

<p class="descripcion-pagina">Reestablece tu Password escribiendo tu Email a continuación</p>

<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<form action="/olvide" method="POST" class="formulario">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Tu Email">
    </div>

    <input type="submit" value="Enviar Instrucciones" class="boton">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una Cuenta? ¡Inicia Sesión!</a>
    <a href="/crear-cuenta">¿Aún no tienes una Cuenta? ¡Crea Una!</a>
</div>