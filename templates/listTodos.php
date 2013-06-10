<?php include "templates/include/header.php";?>

  <div data-role="page" id="listTodos">

    <div data-role="header">
      <h1><?php echo NOMBRE_APP ?></h1>
      <a href="<?php echo APP_URL?>?action=newTodo" data-role="button" data-icon="plus" data-prefetch><?php echo BOTON_NUEVA ?></a>
      <a href="<?php echo APP_URL?>?action=options" data-icon="gear" data-prefetch><?php echo BOTON_OPCIONES ?></a>
    </div>

    <div data-role="content">

      <input type="hidden" name="authToken" id="authToken" value="<?php echo $_SESSION['authToken']?>"/>

      <ul data-role="listview" data-split-theme="d">

<?php foreach ( $results['todos'] as $todo ) { ?>

        <li data-id="<?php echo htmlspecialchars($todo->id)?>" data-completed="<?php echo $todo->completed ? "true" : "false"?>">
          <a href="#">
            <img src="images/check-<?php echo $todo->completed ? "on" : "off"?>.png" alt="checkbox" class="ui-li-icon" />
            <?php echo $todo->title?>
          </a>
          <a href="<?php echo APP_URL?>?action=editTodo&amp;todoId=<?php echo $todo->id?>">Ver Tarea</a>
        </li>

<?php } ?>

      </ul>

<?php if ( count( $results['todos'] ) == 0 ) { ?>
      <p>Pulse el botón "Nuevo" para agregar un nuevo tarea ...</p>
<?php } ?>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

