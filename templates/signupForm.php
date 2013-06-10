<?php include "templates/include/header.php" ?>

  <div data-role="page" id="signupForm">

    <div data-role="header">
      <h1><?php echo HEADER_REGISTRO ?></h1>
    </div>

    <div data-role="content">

      <div style="text-align: center;">
        <h2><?php echo NOMBRE_APP ?></h2>
      </div>

      <form action="<?php echo APP_URL?>" method="post" data-transition="fade">
        <input type="hidden" name="action" value="signup" />
        <input type="hidden" name="signup" value="true" />

        <div data-role="fieldcontain">
          <input type="email" name="emailAddress" id="emailAddress" placeholder="Email" required autofocus maxlength="50">
        </div>

        <div data-role="fieldcontain">
          <input type="password" name="password" id="password" placeholder="Contraseña" required maxlength="30">
        </div>

        <div data-role="fieldcontain">
          <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Repita la contraseña" required maxlength="30">
        </div>

        <input type="submit" name="signup" value="Registrar" />
        <a href="<?php echo APP_URL?>" data-role="button" data-rel="back" data-transition="slide" data-direction="reverse" data-theme="a"><?php echo BOTON_CANCELAR?></a>

      </form>

    </div>

  </div>

<?php include "templates/include/footer.php" ?>

