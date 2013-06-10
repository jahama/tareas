
// Add the "toggleTodo()" tap handler to to-do list items
$('#listTodos').live( 'pageinit', function() {
  $('li a[href="#"]').bind( 'tap', toggleTodo );
} );


/**
* Toggles a to-do item:
*
* - Toggles the list item's "data-completed" attribute
* - Toggles the checkbox image
* - Toggles the to-do's status on the server via Ajax
*
* @param Event The tap event
*/

function toggleTodo( e ) {

  // Get the to-do list item
  var todoItem = $(this).parent().parent().parent();

  // Get the checkbox image inside the item
  var todoCheckbox = todoItem.find('img');

  // Toggle the "data-completed" attribute and the checkbox image
  
  if ( todoItem.attr('data-completed') == 'true' ) {
    todoItem.attr('data-completed', 'false');
    todoCheckbox.attr('src', "images/check-off.png");
  } else {
    todoItem.attr('data-completed', 'true');
    todoCheckbox.attr('src', "images/check-on.png");
  }

  // Set the new to-do status in the database
  
  $.ajax( {

    type: "POST",
    url: appUrl + "?action=changeTodoStatus",

    data: {
      todoId: todoItem.attr('data-id'),           // The to-do ID
      newStatus: todoItem.attr('data-completed'), // The new status ('true' or 'false')
      authToken: $('#authToken').val(),           // The CSRF token
    },

    // Success handler
    
    success: function( data, textStatus, jqXHR ) {

      // If the PHP script returned "success", it's all good
      if ( data == "success" ) return;

      // There was a problem updating the to-do on the server.
      // Undo the toggle action on the to-do item, and display an error message.

      if ( todoItem.attr('data-completed') == 'true' ) {
        var message = "checked";
        todoItem.attr('data-completed', 'false');
        todoItem.find('img').attr('src', "images/check-off.png");
      } else {
        var message = "unchecked";
        todoItem.attr('data-completed', 'true');
        todoItem.find('img').attr('src', "images/check-on.png");
      }

      alert( 'The to-do could not be ' + message + ' because there was a problem with the server. Please try again, or contact the server administrator for assistance.' );
    },

    // Error handler
     
    error: function( jqXHR, textStatus, errorThrown ) {

      // The server couldn't be contacted.
      // Undo the toggle action on the to-do item, and display an error message.

      if ( todoItem.attr('data-completed') == 'true' ) {
        var message = "checked";
        todoItem.attr('data-completed', 'false');
        todoItem.find('img').attr('src', "images/check-off.png");
      } else {
        var message = "unchecked";
        todoItem.attr('data-completed', 'true');
        todoItem.find('img').attr('src', "images/check-on.png");
      }

      alert( 'The to-do could not be ' + message + ' because there was a problem contacting the server. Please check your network connection and try again.' );
    }

  } );

  e.preventDefault();
  e.stopPropagation();
  return false;
}

