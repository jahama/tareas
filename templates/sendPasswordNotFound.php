<?php include "templates/include/header.php" ?>

  <div data-role="dialog" id="sendPasswordNotFound">

    <div data-role="header">
      <h1>Error</h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2><?php echo NOMBRE_APP ?></h2>
        <p>No hemos podido encontrar su dirección de correo electrónico en la base de datos. Usted puede registrarse usando esta dirección de correo electrónico si lo desea.</p>
      </div>

      <a href="<?php echo APP_URL?>?action=signup" data-role="button"><?php echo BOTON_REGISTRAR ?></a>
      <a href="<?php echo APP_URL?>?action=sendPassword" data-role="button">Pruebe otra dirección</a>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

