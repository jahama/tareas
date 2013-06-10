<?php

/**
 * Class to handle to-dos
 */

class Todo
{
  // Properties

  /**
  * @var int The to-do ID from the database
  */
  public $id = null;

  /**
  * @var int The creator of the to-do
  */
  public $userId = null;

  /**
  * @var int When the to-do was created
  */
  public $createdTime = null;

  /**
  * @var boolean Whether the to-do is completed or not
  */
  public $completed = null;

  /**
  * @var string The title of the to-do
  */
  public $title = null;

  /**
  * @var string Any notes associated with the to-do
  */
  public $notes = null;


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
    if ( isset( $data['userId'] ) ) $this->userId = (int) $data['userId'];
    if ( isset( $data['createdTime'] ) ) $this->createdTime = (int) $data['createdTime'];
    if ( isset( $data['completed'] ) ) $this->completed = $data['completed'] ? 1 : 0;
    if ( isset( $data['title'] ) ) $this->title = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['title'] );
    if ( isset( $data['notes'] ) ) $this->notes = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['notes'] );
  }


  /**
  * Returns a Todo object matching the given ID
  *
  * @param int The to-do ID
  * @return Todo|null The Todo object, or null if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "SELECT *, UNIX_TIMESTAMP(createdTime) AS createdTime FROM todos WHERE id = :id" );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Todo( $row );
  }


  /**
  * Returns all Todo objects for a given user 
  *
  * @param User The user
  * @param string Optional column by which to order the to-dos
  * @return Array A list of Todo objects
  */

  public static function getListForUser( $user, $order="createdTime DESC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "SELECT *, UNIX_TIMESTAMP(createdTime) AS createdTime
            FROM todos
            WHERE userId = :userId
            ORDER BY " . mysql_escape_string($order) );

    $st->bindValue( ":userId", $user->id, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $list[] = new Todo( $row );
    }

    $conn = null;
    return $list;
  }


  /**
  * Inserts the current Todo object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the Todo object already have an ID?
    if ( !is_null( $this->id ) ) trigger_error ( "Todo::insert(): Attempt to insert a Todo object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Todo
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "INSERT INTO todos ( userId, createdTime, completed, title, notes ) VALUES ( :userId, FROM_UNIXTIME(:createdTime), :completed, :title, :notes )" );
    $st->bindValue( ":userId", $this->userId, PDO::PARAM_INT );
    $st->bindValue( ":createdTime", $this->createdTime, PDO::PARAM_INT );
    $st->bindValue( ":completed", $this->completed, PDO::PARAM_INT );
    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );
    $st->bindValue( ":notes", $this->notes, PDO::PARAM_STR );
    if ( !$st->execute() ) trigger_error( "Todo:insert(): Couldn't execute query" );
    $this->id = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Todo object in the database.
  */

  public function update() {

    // Does the Todo object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "Todo::update(): Attempt to update a Todo object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Todo
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "UPDATE todos SET userId=:userId, createdTime=FROM_UNIXTIME(:createdTime), completed=:completed, title=:title, notes=:notes WHERE id = :id" );
    $st->bindValue( ":userId", $this->userId, PDO::PARAM_INT );
    $st->bindValue( ":createdTime", $this->createdTime, PDO::PARAM_INT );
    $st->bindValue( ":completed", $this->completed, PDO::PARAM_INT );
    $st->bindValue( ":title", $this->title, PDO::PARAM_STR );
    $st->bindValue( ":notes", $this->notes, PDO::PARAM_STR );
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Todo object from the database.
  */

  public function delete() {

    // Does the Todo object have an ID?
    if ( is_null( $this->id ) ) trigger_error ( "Todo::delete(): Attempt to delete a Todo object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Todo
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "DELETE FROM todos WHERE id = :id LIMIT 1" );
    $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes all completed to-dos for a given user.
  *
  * @param User The user to delete to-dos for
  */

  static public function deleteCompletedForUser( $user ) {

    // Delete the to-dos
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare( "DELETE FROM todos WHERE userId = :userId AND completed = 1" );
    $st->bindValue( ":userId", $user->id, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
