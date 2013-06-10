<?php include "templates/include/header.php" ?>

  <div data-role="dialog" id="errorDialog">

    <div data-role="header">
      <h1>Error</h1>
    </div>

    <div data-role="content">
<?php  // xdebug_var_dump($results); ?>
      <div style="text-align: center;">
        <h2><?php echo NOMBRE_APP ?></h2>
        <p><?php echo $results['errorMessage']?></p>
      </div>

      <a href="<?php echo APP_URL?>?action=<?php echo $results['errorReturnAction']?>" data-rel="back" data-transition="fade" data-role="button">OK</a>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

