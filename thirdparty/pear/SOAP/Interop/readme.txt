Round 2 Interop Test files

Resources:
http://www.whitemesa.com/interop.htm
http://www.whitemesa.com/r3/interop3.html
http://www.pocketsoap.com/registration/

Requires an SQL database, schema for MySQL is in interop_database.sql.

Run interop_client_run.php to store test results.
View index.php to see test results.

To setup an interop server:

1. Copy config.php.dist to config.php.
2. Web server must alias url /soap_interop/ to the pear/SOAP_Interop
   directory. The alias url can be set in config.php.
3. index.php should be set for the default document.
4. MySQL should be set up, with a database called interop, schema is in
   interop_database.sql. Database name, user, and password can be set in
   config.php.
5. interop_client_run.php should not be left under the web root, it is
   available for manual testing.
