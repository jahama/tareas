<?php include "templates/include/header.php" ?>

  <div data-role="dialog" id="changePasswordSuccess">

    <div data-role="header">
      <h1>Password Changed</h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2>Password Changed</h2>
        <p>Your password has been changed.</p>
      </div>

      <a href="<?php echo APP_URL?>?action=options" data-role="button">OK</a>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

