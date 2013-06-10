<?php include "templates/include/header.php" ?>

  <div data-role="page" id="sendPasswordForm">

    <div data-role="header">
      <h1><?php echo NOMBRE_APP ?></h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2>Enviar Nueva Contraseña</h2>
        <p>Introduzca su dirección de correo electrónico, y le enviaremos una contraseña nueva.</p>
      </div>

      <form action="<?php echo APP_URL?>" method="post" data-transition="fade">
        <input type="hidden" name="action" value="sendPassword" />
        <input type="hidden" name="sendPassword" value="true" />

        <div data-role="fieldcontain">
          <input type="email" name="emailAddress" id="emailAddress" placeholder="Email" required autofocus maxlength="50">
        </div>

        <input type="submit" name="sendPassword" value="Enviar Contraseña" />
        <a href="<?php echo APP_URL?>" data-rel="back" data-role="button" data-theme="a" data-transition="slide" data-direction="reverse"><?php echo BOTON_CANCELAR ?></a>

      </form>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

