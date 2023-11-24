<?php

session_start();

const LDAP_HOST = "ldap-server";
const LDAP_PORT = 389;
const LDAP_DC = "dc=myorg,dc=local";
const LDAP_DN = "cn=admin,dc=myorg,dc=local";
const LDAP_PASS = "admin_password";

// Your existing PHP code here...

?>

<html>
<?= htmlspecialchars($_SESSION["USERID"]) ?>
<form action="/" method="POST">
    <label>User ID: </label><input type="text" name="user_id"/>
    <label>Password: </label><input type="password" name="password"/>
    <input type="hidden" name="login" value="1"/>
    <input type="submit" name="submit" value="Submit"/>
</form>

<form action="/" method="POST">
    <input type="hidden" name="logout" value="1"/>
    <input type="submit" value="Logout"/>
</form>
</html>

