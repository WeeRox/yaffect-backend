# yaffect-backend
This is the REST backend for the [Yaffect Android application](https://github.com/WeeRox/yaffect-android).
To test this project locally with a database, you have to include a file named `config.php` containing information about the database connection, for example:
```
<?php
return array(
  'hostname' => '[hostname]',
  'username' => '[username]',
  'password' => '[password]',
  'database' => '[database-name]',
  'debug' => true
);
?>
```
