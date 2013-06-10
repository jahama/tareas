<?php

/**
 * Class to handle app users
 */

class User
{
  // Properties

  /**
  * @var int The user ID from the database
  */
  public $id = null;

  /**
  * @var string The user's email address
  */
  public $emailAddress = null;

  /**
  * @var string The user's plaintext password
  */
  public $plaintextPassword = null;

  /**
  * @var string The user's encrypted password
  */
  public $encryptedPassword = null;


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
    if ( isset( $data['emailAddress'] ) ) $this->emailAddress = preg_replace ( "/[^\.\-\_\@a-zA-Z0-9]/", "", $data['emailAddress'] );
    if ( isset( $data['plaintextPassword'] ) ) $this->plaintextPassword = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['plaintextPassword'] );
    if ( isset( $data['encryptedPassword'] ) ) $this->encryptedPassword = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$\/ a-zA-Z0-9()]/", "", $data['encryptedPassword'] );
  }


  /**
  * Encrypts the plaintext password in the plaintextPassword property, and stores the result in encryptedPassword.
  */

  public function encryptPassword() {
    $this->encryptedPassword = crypt ( $this->plaintextPassword );
  }


  /**
  * Checks that the supplied plaintext password is correct for this user
  *
  * @param string The plaintext password
  */

  public function checkPassword( $password ) {
    return ( $this->encryptedPassword == crypt ( $password, $this->encryptedPassword ) );
  }


  /**
  * Generates a new random password for the user.
  */

  public function generatePassword() {
    srand();
    $pass = "";

    for ( $i=1; $i <= 8; $i++ ) {
      $char = 58;

      while ( $char > 57 and $char < 65 or strpos ( "1l0ouvi4589", strtolower( chr( $char ) ) ) !== false ) {
        $char = rand( 0, 42 ) + 48;
      }

      $pass .= chr( $char );
    }

    $pass = strtolower ( $pass );
    $this->plaintextPassword = $pass;
  }


  /**
  * Sends the user's plaintext password to their email address.
  */

  public function sendPassword() {
    if ( !$this->emailAddress ) return;
    $headers = "From: " . PASSWORD_EMAIL_FROM_NAME . " <" . PASSWORD_EMAIL_FROM_ADDRESS . ">";
    ob_start();
    require( TEMPLATE_PATH . "/passwordEmail.php" );
    $message = ob_get_contents();
    ob_clean();
    $success = mail( $this->emailAddress, PASSWORD_EMAIL_SUBJECT, $message, $headers );
  }


  /**
  * Get the logged-in user (if any).
  *
  * @return User|false The logged-in user, or false if no login session could be found
  */

  static function getLoggedInUser() {
    if ( !isset( $_SESSION['userId']) ) return false;
    return User::getById( (int)$_SESSION['userId'] );
  }
  

  /**
  * Creates a valid login session for this user, logging them in.
  */

  function createLoginSession() {
    session_regenerate_id( true );
    $_SESSION['userId'] = $this->id;
    srand();
    $_SESSION['authToken'] = rand( 10e16, 10e20 );
  }


  /**
  * Destroy a login session, logging the user out.
  */

  function destroyLoginSession() {
    unset( $_SESSION['userId'] );
    session_destroy();

    if ( isset( $_COOKIE[session_name()] ) ) {
      setcookie( session_name(), "", time()-3600, "/" );
    }
  }


  /**
  * Returns a User object matching the given ID.
  *
  * @param int The user ID
  * @return User|null The User object, or null if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "SELECT * FROM users WHERE id = :id" );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new User( $row );
  }


  /**
  * Returns a User object matching the given email address.
  *
  * @param int The email address
  * @return User|null The User object, or null if the record was not found or there was a problem
  */

  public static function getByEmailAddress( $emailAddress ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "SELECT * FROM users WHERE emailAddress = :emailAddress" );
    $st->bindValue( ":emailAddress", $emailAddress, PDO::PARAM_STR );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new User( $row );
  }


  /**
  * Inserts the current User object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the User object already have an ID?
    if ( !is_null( $this->id ) ) trigger_error ( "User::insert(): Attempt to insert a User object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the User
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "INSERT INTO users ( emailAddress, encryptedPassword ) VALUES ( :emailAddress, :encryptedPassword )" );
    $st->bindValue( ":emailAddress", $this->emailAddress, PDO::PARAM_STR );
    $st->bindValue( ":encryptedPassword", $this->encryptedPassword, PDO::PARAM_STR );
    $st->execute();
    $this->id = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current User object in the database.
  */

  public function update() {

    // Does the User object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "User::update(): Attempt to update a User object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the User
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "UPDATE users SET emailAddress=:emailAddress, encryptedPassword=:encryptedPassword WHERE id = :id" );
    $st->bindValue( ":emailAddress", $this->emailAddress, PDO::PARAM_STR );
    $st->bindValue( ":encryptedPassword", $this->encryptedPassword, PDO::PARAM_STR );
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current User object from the database.
  */

  public function delete() {

    // Does the User object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "User::delete(): Attempt to delete a User object that does not have its ID property set.", E_USER_ERROR );

    // Delete the User
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM users WHERE id = :id LIMIT 1" );
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
