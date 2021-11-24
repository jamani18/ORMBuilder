# ORMBuilder
Personal ORM that build Class and PDO connections

## Install

**Manual**

Just download conf/SqlConnector file from https://github.com/jamani18/SqlConnector and set in same folder.

## Requirements

**Server**

Only need a PHP server with version 7 minimum.

## Start up

Open the SqlConnector.php file and modify the values of the connection data to the database so that it connects.

For more information, see SqlConnector repository: https://github.com/jamani18/SqlConnector

## Usage

This structure is divided into two builders:

1. buildClass: Allows to build the class based on the table in the databases.
2. buildPDO: It allows to build the PDO file with the associated CRUD for each field.

For its use, once the configuration indicated in the * Requirements * has been made, we just have to open the *classPdoBuilder.php* file on navigator and use the form to generate the different built files.
