<?php include "templates/include/header.php" ?>

  <div data-role="page" id="options">

    <div data-role="header">
      <h1>Opciones</h1>
      <a href="<?php echo APP_URL?>?action=listTodos" data-icon="arrow-l"><?php echo BOTON_VOLVER ?></a>
    </div>

    <div data-role="content">

      <a href="<?php echo APP_URL?>?action=deleteCompleted" data-role="button" data-transition="fade" data-prefetch>Eliminar Tareas Completadas</a>
      <a href="<?php echo APP_URL?>?action=changePassword" data-role="button" data-transition="slide" data-prefetch>Cambiar la contraseña</a>
      <a href="<?php echo APP_URL?>?action=logout" data-role="button" data-ajax="false">Salir</a>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

