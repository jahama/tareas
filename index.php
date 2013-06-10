<?php

require( "config.php" );
session_start();

$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
if ( !$action ) $action = isset( $_POST['action'] ) ? $_POST['action'] : "";

//xdebug_var_dump(" -- ACCION -- " . $action);
//xdebug_var_dump(" -- SESION -- " . $_SESSION);

// Si el usuario no ha iniciado sesión — y está intentando acceder a una función para la que necesite haberla iniciado —
//  el script llamará a login() para mostrar el formulario de inicio de sesión, y salir
if ( !User::getLoggedInUser() && $action != "login" && $action != "logout" && $action != "signup" && $action != "sendPassword" ) {
  login();
  exit;
}

// Llevar a cabo la acción apropiada

/*
 *  Asumiendo que el usuario ha iniciado sesión, el script revisa que el parámetro
    $action sea un valor válido, y si no es así lo establece a 'listTodos' por
    defecto. Luego llama a la función apropiada basándose en el valor de $action.
 */
if ( !in_array( $action, array( 'login', 'logout', 'signup', 'sendPassword', 'newTodo', 'editTodo', 'deleteTodo',
                                'changeTodoStatus', 'options', 'deleteCompleted', 'changePassword' ), true ) ) $action = 'listTodos';
// Llamada a la función apropiada basándose en el valor de $action.(Por defecto va a el Listado de Tareas)
$action();



/**
 *  Muestra el formulario de Login
 *  Si el usuario ha enviado el formulario de inicio, esta función recibe el registro
    del usuario con la dirección de correo facilitada, y luego revisa la contraseña
    facilitada con la contraseña del usuario.
 *  Si todo va bien, la función inicia la sesión del usuario llamando al método createLoginSession() del
    usuario, luego redirige al script controlador para mostrar la lista de tareas.
 *  Si el inicio falla, se muestra un mensaje de error — almacenado en $results
    ['errorMessage'] — usando la plantilla errorDialog.php.
 *  Si el usuario no ha enviado el formulario de inicio de sesión, la función
    simplemente mostrará el formulario, que está en la plantilla loginForm.php.
 */

function login() {

  $results = array();
  // La variable $results['errorReturnAction'] es usada por errorDialog.php al crear el enlace para el botón OK,
  //  para que el usuario vuelva a la pantalla correcta tras pulsar OK.
  $results['errorReturnAction'] = "login";
  $results['errorMessage'] = "Email o contraseña incorrecta. Por favor, inténtelo de nuevo";

  if ( isset( $_POST['login'] ) ) {

    // User has posted the login form: attempt to log the user in
    // El usuario enviado el formulario de acceso: el usuario intento de iniciar sesión

    if ( $user = User::getByEmailAddress( $_POST['emailAddress'] ) ) {

      if ( $user->checkPassword( $_POST['password'] ) ) {

        // Ingresar éxito: Crear una sesión y redirigir a la lista tareas
        $user->createLoginSession();
        header( "Location: " . APP_URL );

      } else {
        // Error de inicio: mostrar un mensaje de error al usuario
        require( TEMPLATE_PATH . "/errorDialog.php" );
      }

    } else {

      // Error de inicio: mostrar un mensaje de error al usuario
      require( TEMPLATE_PATH . "/errorDialog.php" );
    }

  } else {

    // Si el usuario no ha enviado el formulario de inicio de sesión, la función simplemente mostrará el formulario, que está en la plantilla loginForm.php.
    require( TEMPLATE_PATH . "/loginForm.php" );
  }
}


/**
* Logs the user out
*/

function logout() {
  User::destroyLoginSession();
  header( "Location: " . APP_URL );
}


/**
* Muestra el formulario de registro
*   Si el usuario ha enviado el formulario de alta, la función intenta registrar al usuario.
*   Primero revisa campos vacíos o campos de contraseñas que no coinciden,
*   y revisa si ya existe algún usuario con la dirección de correo facilitada ya existe.
*   Si hay un problema, se mostrará el mensaje apropiado de error.
*   Si todo va bien, el usuario es creado, su contraseña es encriptada, y el registro es insertado en la tabla users.
*   La función también inicia la sesión del nuevo usuario llamando a la función createLoginSession(),
*   luego lo redirige al script controlador para mostrar la lista (vacía) de tareas.
*
*   Si el usuario no ha enviado aun el formulario de alta, la función muestra el formulario que está en la plantilla signupForm.php.
*/

function signup() {

  $results = array();
  $results['errorReturnAction'] = "signup";

  if ( isset( $_POST['signup'] ) ) {
    
    // Si el usuario ha enviado el formulario de registro : se intenta registrar la usuario
    $emailAddress = isset( $_POST['emailAddress'] ) ? $_POST['emailAddress'] : "";
    $password = isset( $_POST['password'] ) ? $_POST['password'] : "";
    $passwordConfirm = isset( $_POST['passwordConfirm'] ) ? $_POST['passwordConfirm'] : "";

    // Revisar campos vacíos
    if ( !$emailAddress || !$password || !$passwordConfirm ) {
      $results['errorMessage'] = "Por favor, rellene todos los campos del formulario.";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }
    // Revisar campos de contraseñas que no coinciden,
    if ( $password != $passwordConfirm ) {
      $results['errorMessage'] = "Las dos contraseñas que ha introducido no coinciden. Por favor, inténtalo de nuevo.";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }

    // Revisar si ya existe algún usuario con la dirección de correo facilitada ya existe.
    if ( User::getByEmailAddress( $emailAddress ) ) {
      $results['errorMessage'] = "Usted ya se ha registrado usando esa dirección de correo electrónico!";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }

    // Si todo va bien, el usuario es creado,
    $user = new User( array( 'emailAddress' => $emailAddress, 'plaintextPassword' => $password ) );
    // su contraseña es encriptada,
    $user->encryptPassword();
    // y el registro es insertado en la tabla users.
    $user->insert();
    // Se inicia la sesión del nuevo usuario
    $user->createLoginSession();
    // Redireccion al controlador para mostrar la lista (vacía) de tareas.
    header( "Location: " . APP_URL );

  } else {

    // Si el usuario no ha enviado aun el formulario de alta, la función muestra el formulario que está en la plantilla signupForm.php.
    require( TEMPLATE_PATH . "/signupForm.php" );
  }
}


/**
 * Muestra el formulario de "Enviar Contraseña"
 * Si el formulario ha sido enviado, envía al usuario la nueva contreseña
 *  Si el usuario ha enviado el formulario de “enviar contraseña”, la función
    revisa primero que el formulario estaba relleno y la dirección de correo
    facilitada existe en la base de datos, mostrando los mensajes de error
    apropiados si hubo algún problema. Si todo fue bien, crea y encripta una
    nueva contraseña, actualiza el registro del usuario y envía la nueva contraseña
    a la dirección de correo del usuario, antes de enviar un mensaje de “éxito” al
    usuario.
 *  Si el usuario no ha enviado aun el formulario de “enviar contraseña” la función
    muestra el formulario, que está en la plantilla sendPasswordForm.php
 */

function sendPassword() {

  $results = array();
  $results['errorReturnAction'] = "sendPassword";

  // Si el usuario ha enviado el formulario de “enviar contraseña”
  if ( isset( $_POST['sendPassword'] ) ) {

    // Si el usuario ha enviado el formulario : intento de enviar una nueva contraseña:
    $emailAddress = isset( $_POST['emailAddress'] ) ? $_POST['emailAddress'] : "";

    //  revisa primero que el formulario estaba relleno y la dirección de correo  facilitada existe en la base de datos,
    //   mostrando los mensajes de error apropiados si hubo algún problema
    if ( !$emailAddress ) {
      $results['errorMessage'] = "Introduzca su dirección de correo electrónico";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }

    if ( !$user = User::getByEmailAddress( $emailAddress ) ) {
      require( TEMPLATE_PATH . "/sendPasswordNotFound.php" );
      return;
    }

    // Genera y envia la contraseña
    $user->generatePassword();
    $user->encryptPassword();
    $user->update();
    $user->sendPassword();
    require( TEMPLATE_PATH . "/sendPasswordSuccess.php" );

  } else {
    // El usuario no ha enviado aún le formulario:  mostrar el formulario
    require( TEMPLATE_PATH . "/sendPasswordForm.php" );
  }
}


/**
* Muestra el formulario de nueva tarea
* Si el formulario ha sido enviado, salva a la nueva tarea
*/
/*
 *  Si el usuario ha enviado el formulario “nueva tarea”, esta función revisa que el
    token anti-CSRF es válido y, si es así, crea un nuevo objeto Todo, establece la
    propiedad userId del objeto a la del usuario que ha iniciado sesión,
    establece las propiedades createdTime y completed del objeto, e inserta
    la tarea en la base de datos antes de redirigir a la lista de tareas. Si el usuario
    canceló pulsando el botón Cancel, la función redirigirá a la lista de tareas sin
    hacer nada más.
 */

function newTodo() {

  $results = array();
  $results['pageTitle'] = "Nueva Tarea";
  $results['formAction'] = "newTodo";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the to-do edit form: save the new to-do
    //  El usuario ha enviado el formulario de la nueva tarea:  guardar la nueva tarea

    // Revisa que el token anti-CSRF es válido
    if ( !checkAuthToken() ) return;
    // crea un nuevo objeto Todo
    $todo = new Todo ( $_POST );
    // Establecer la propiedad userId del objeto a la del usuario que ha iniciado sesión
    $todo->userId = User::getLoggedInUser()->id;
    //  establece las propiedades createdTime y completed del objeto
    $todo->createdTime = time();
    $todo->completed = false;
    // insertar la tarea en la base de datos
    $todo->insert();
    //  redirigir a la lista de tareas
    header( "Location: " . APP_URL . "?action=listTodos" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has canceled their edits: return to the to-do list
    header( "Location: " . APP_URL . "?action=listTodos" );
  } else {

    // Si el usuario aun no envió el formulario de “nueva tarea”: muestra el formulario
    // Si el usuario aun no envió el formulario de “nueva tarea”, la función crea un objeto Todo vacío para editar el formulario
    // y muestra el formulario de edición, que está en la plantilla editTodo.php
    $results['todo'] = new Todo;
    require( TEMPLATE_PATH . "/editTodo.php" );
  }
}


/**
* Displays the "edit to-do" form
* If the form has been posted, saves the changes
*/

function editTodo() {

  $results = array();
  $results['pageTitle'] = "Editar Tarea";
  $results['formAction'] = "editTodo";
  $results['errorReturnAction'] = "listTodos";
  $results['errorMessage'] = "To-do not found. Please try again.";

  if ( isset( $_POST['saveChanges'] ) ) {

    // User has posted the to-do edit form: save the changes
    if ( !checkAuthToken() ) return;

    if ( $todo = Todo::getById( (int)$_POST['todoId'] ) ) {
      if ( $todo->userId == User::getLoggedInUser()->id ) {
        $todo->__construct( $_POST );
        $todo->update();
        header( "Location: " . APP_URL . "?action=listTodos" );
      } else {
        require( TEMPLATE_PATH . "/errorDialog.php" );
      }
      
    } else {
      require( TEMPLATE_PATH . "/errorDialog.php" );
    }

  } elseif ( isset( $_POST['cancel'] ) ) {

    // User has canceled their edits: return to the to-do list
    header( "Location: " . APP_URL . "?action=listTodos" );

  } else {

    // User has not posted the to-do edit form yet: display the form
    if ( $results['todo'] = Todo::getById( (int)$_GET['todoId'] ) ) {
      if ( $results['todo']->userId == User::getLoggedInUser()->id ) {
        require( TEMPLATE_PATH . "/editTodo.php" );
      } else {
        require( TEMPLATE_PATH . "/errorDialog.php" );
      }

    } else {
      require( TEMPLATE_PATH . "/errorDialog.php" );
    }
  }
}


/**
* Muestra el cuadro de dialogo de confirmacion "Eliminar Tarea"
* Si el usuario ha comfirmado la eliminacion, se borra la tarea
*/

/*
 *  Si el usuario ha enviado el formulario “borrar tarea” contenido en el diálogo de
    confirmación, la función revisa que el token anti-CSRF es válido y, si es así,
    obtiene la tarea de la base de datos, la borra llamando a su método delete
    (), y redirige a la lista de tareas. No obstante, si la tarea con la ID facilitada no
    se puede encontrar, se muestra un mensaje de error.
 *
 *  Si el usuario no ha enviado el formulario de “confirmar borrado” la función
    obtiene el objeto Todo que tiene la ID facilitada en el parámetro todoId, y
    muestra el diálogo de confirmación, que se encuentra en la plantilla
    deleteTodo.php.
 *
 */

function deleteTodo() {

  $results = array();
  $results['errorReturnAction'] = "listTodos";
  $results['errorMessage'] = "La Tarea no se encuentra. Por favor, inténtelo de nuevo.";

  if ( isset( $_POST['confirm'] ) ) {

    // El usuario ha confirmado la eliminacion : Borrar la tarea

    //  revisa que el token anti-CSRF es válido
    if ( !checkAuthToken() ) return;
    //  obtiene la tarea de la base de datos
    if ( $todo = Todo::getById( (int)$_POST['todoId'] ) ) {
      if ( $todo->userId == User::getLoggedInUser()->id ) {
        // la borra llamando a su método delete
        $todo->delete();
        // redirige a la lista de tareas
        header( "Location: " . APP_URL . "?action=listTodos" );
      } else {
        require( TEMPLATE_PATH . "/errorDialog.php" );
      }
      
    } else {
      require( TEMPLATE_PATH . "/errorDialog.php" );
    }

  } else {

    // User has not confirmed deletion yet: display the confirm dialog
    // Si el usuario no ha confirmado la eliminacion en el cuadro de dialogo: Mostrar el cuadro de dialogo
    if ( $results['todo'] = Todo::getById( (int)$_GET['todoId'] ) ) {
      if ( $results['todo']->userId == User::getLoggedInUser()->id ) {
        require( TEMPLATE_PATH . "/deleteTodo.php" );
      } else {
        require( TEMPLATE_PATH . "/errorDialog.php" );
      }

    } else {
      require( TEMPLATE_PATH . "/errorDialog.php" );
    }
  }
}


/**
* Cambia un tarea especifica a "terminado" o "no se completó"
*/

function changeTodoStatus() {

  // revisa el token anti-CSRF
  if ( !checkAuthToken() ) return;
  // obtiene los parámetros todoId y newStatus de la petición Ajax.
  $todoId = isset( $_POST['todoId'] ) ? (int)$_POST['todoId'] : ""; 
  $newStatus = isset( $_POST['newStatus'] ) ? $_POST['newStatus'] : ""; 

  if ( !$todoId || !$newStatus ) {
    echo "error";
    return;
  }

  // Si todo está bien, obtiene la tarea de la base de datos, cambia su propiedad $completed, y
  // la guarda de nuevo en la base de datos llamando al método update() del método, antes de devolver un mensaje de éxito al JavaScript.
  if ( $todo = Todo::getById( (int)$_POST['todoId'] ) ) {
    if ( $todo->userId == User::getLoggedInUser()->id ) {
      $todo->completed = ( $newStatus == "true" ) ? 1 : 0;
      $todo->update();
      echo "success";
    } else {
      echo "error";
    }
    
  } else {
    echo "error";
  }
}


/**
* Displays the options page
*/

function options() {
  require( TEMPLATE_PATH . "/options.php" );
}


/**
* Muestra el cuadro diálogo de confirmación "eliminar las tareas completadas"
* Si el usuario ha confirmado la eliminación, borra las tareas completadas
*/

function deleteCompleted() {

  if ( isset( $_POST['confirm'] ) ) {

    // User has confirmed deletion: delete the to-dos
    if ( !checkAuthToken() ) return;

    Todo::deleteCompletedForUser( User::getLoggedInUser() );
    header( "Location: " . APP_URL . "?action=listTodos" );

  } else {
    // Si el usuario no ha confirmado la eliminacion: muestra el cuadro de dialogo
    require( TEMPLATE_PATH . "/deleteCompleted.php" );
  }
}


/**
* Displays the "change password" form
* If the form has been posted, changes the user's password
*/

function changePassword() {

  $results = array();
  $results['errorReturnAction'] = "changePassword";

  if ( isset( $_POST['saveChanges'] ) ) {

    // El usuario ha enviado el formulario

    // Revisar el token anti-CSRF, así como los campos del formulario.
    if ( !checkAuthToken() ) return;
    $currentPassword = isset( $_POST['currentPassword'] ) ? $_POST['currentPassword'] : "";
    $newPassword = isset( $_POST['newPassword'] ) ? $_POST['newPassword'] : "";
    $newPasswordConfirm = isset( $_POST['newPasswordConfirm'] ) ? $_POST['newPasswordConfirm'] : "";
    
    // Se tienen que haber rellenado todos los campos del formulario
    if ( !$currentPassword || !$newPassword || !$newPasswordConfirm ) {
      $results['errorMessage'] = "Por favor, rellene todos los campos del formulario.";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }
    // Las 2 nuevas contraseñas deben de coincidir
    if ( $newPassword != $newPasswordConfirm ) {
      $results['errorMessage'] = "Las dos nuevas contraseñas que has introducido no coinciden. Por favor, inténtelo de nuevo.";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }

    $user = User::getLoggedInUser();
    if ( !$user->checkPassword( $currentPassword ) ) {
      $results['errorMessage'] = "La contraseña actual es incorrecta. Por favor, inténtelo de nuevo.";
      require( TEMPLATE_PATH . "/errorDialog.php" );
      return;
    }

    // All OK: change password
    $user->plaintextPassword = $newPassword;
    $user->encryptPassword();
    $user->update();
    require( TEMPLATE_PATH . "/changePasswordSuccess.php" );

  } else {

    // Si el usuario no ha enviado el formulario de “cambiar contraseña”, la función muestra el formulario,
    // que se encuentra en la plantilla "changePasswordForm.php".
    require( TEMPLATE_PATH . "/changePasswordForm.php" );
  }
}


/**
* Listado las tareas del usuario
*/

function listTodos() {
  $results = array();
  $results['todos'] = Todo::getListForUser( User::getLoggedInUser() );
  require( TEMPLATE_PATH . "/listTodos.php" );
}


/**
* Checks the supplied anti-CSRF token is valid
* If it isn't, the user is logged out
*/

function checkAuthToken() {
  if ( !isset( $_POST['authToken'] ) || $_POST['authToken'] != $_SESSION['authToken'] ) {
    logout();
    return false;
  } else {
    return true;
  }
}

?>
