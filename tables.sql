
######################################################################
######################################################################
#
# tables.sql
#
# This file contains the table schema for the Task Tango web app
#
######################################################################
######################################################################


DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id                              int unsigned NOT NULL auto_increment,
  emailAddress                    varchar(50) NOT NULL UNIQUE,          # The email address of the user
  encryptedPassword               varchar(50) NOT NULL,                 # The encrypted password

  PRIMARY KEY                     (id),
  KEY                             (emailAddress)
);

DROP TABLE IF EXISTS todos;
CREATE TABLE todos (
  id                              int unsigned NOT NULL auto_increment,
  userId                          int unsigned NOT NULL,                # The creator of the to-do
  createdTime                     datetime NOT NULL,                    # When the to-do was created
  completed                       tinyint(1) NOT NULL,                  # Whether the to-do is completed or not
  title                           varchar(255) NOT NULL,                # The title of the to-do
  notes                           text,                                 # Any notes associated with the to-do

  PRIMARY KEY                     (id),
  KEY                             (userId),
  FULLTEXT                        (title,notes)
);



