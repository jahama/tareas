<?php include "templates/include/header.php" ?>

  <div data-role="page" id="loginForm">

    <div data-role="header">
      <h1>Login</h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2><?php echo NOMBRE_APP ?></h2>
      </div>

      <form action="<?php echo APP_URL?>" method="post" data-transition="fade">
        <input type="hidden" name="action" value="login" />
        <input type="hidden" name="login" value="true" />

        <div data-role="fieldcontain">
          <input type="email" name="emailAddress" id="emailAddress" placeholder="Email" required autofocus maxlength="50">
        </div>

        <div data-role="fieldcontain">
          <input type="password" name="password" id="password" placeholder="Contraseña" required maxlength="30">
        </div>

        <input type="submit" name="login" value="Login" />
        <a href="<?php echo APP_URL?>?action=signup" data-role="button" data-transition="slide">Registrate</a>
        <a href="<?php echo APP_URL?>?action=sendPassword" data-role="button" data-transition="slide">¿Olvidó su contraseña?</a>

      </form>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

