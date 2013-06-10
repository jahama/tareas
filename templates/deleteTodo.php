<?php include "templates/include/header.php" ?>

  <div data-role="dialog" id="deleteTodo">

    <div data-role="header">
      <h1><?php echo ELIMINAR_TAREA ?></h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2><?php echo ELIMINAR_TAREA ?></h2>
        <p>¿Quiere eliminar esta Tarea?<br><br><strong><?php echo $results['todo']->title?></strong></p>
      </div>

      <form action="<?php echo APP_URL?>" method="post" data-transition="slide" data-direction="reverse">
        <input type="hidden" name="action" value="deleteTodo" />
        <input type="hidden" name="todoId" value="<?php echo $results['todo']->id?>" />
        <input type="hidden" name="confirm" value="true" />
        <input type="hidden" name="authToken" value="<?php echo $_SESSION['authToken']?>" />
        <input type="submit" name="delete" data-theme="f" value="<?php echo ELIMINAR_TAREA ?>" />
        <a href="<?php echo APP_URL?>?action=listTodos" data-rel="back" data-role="button" data-theme="a"><?php echo BOTON_CANCELAR ?></a>
      </form>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>
