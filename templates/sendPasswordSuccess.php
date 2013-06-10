<?php include "templates/include/header.php" ?>

  <div data-role="dialog" id="sendPasswordSuccess">

    <div data-role="header">
      <h1>Contraseña enviada</h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2>Contraseña enviada</h2>
        <p>Su nueva contraseña ha sido enviada a su dirección de correo electrónico.</p>
      </div>

      <a href="<?php echo APP_URL?>?action=login" data-role="button">OK</a>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

