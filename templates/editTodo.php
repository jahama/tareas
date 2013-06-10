<?php include "templates/include/header.php" ?>

  <div data-role="page" id="todoForm">

    <div data-role="header">
      <h1><?php echo $results['pageTitle']?></h1>
    </div>

    <div data-role="content">

      <form action="<?php echo APP_URL?>" method="post" data-transition="slide">
        <input type="hidden" name="action" value="<?php echo $results['formAction']?>" />
        <input type="hidden" name="todoId" value="<?php echo htmlspecialchars($results['todo']->id)?>" />
        <input type="hidden" name="authToken" value="<?php echo $_SESSION['authToken']?>" />

        <div data-role="fieldcontain">
          <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($results['todo']->title)?>" placeholder="Titulo" required maxlength="255">
        </div>

        <div data-role="fieldcontain">
          <textarea name="notes" id="notes" placeholder="Notas" maxlength="65535"><?php echo htmlspecialchars($results['todo']->notes)?></textarea>
        </div>

        <input type="submit" name="saveChanges" value="Guardar" />
<?php if ( $results['todo']->id ) { ?>        
        <a href="<?php echo APP_URL?>?action=deleteTodo&amp;todoId=<?php echo htmlspecialchars($results['todo']->id)?>" data-role="button" data-theme="f" data-transition="fade"><?php echo ELIMINAR_TAREA ?></a>
<?php } ?>
        <a href="<?php echo APP_URL?>?action=listTodos" data-rel="back" data-role="button" data-transition="slide" data-direction="reverse" data-theme="a"><?php echo BOTON_CANCELAR ?></a>

      </form>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

