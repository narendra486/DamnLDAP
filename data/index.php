<?php

session_start();

const LDAP_HOST = "openldap-container";
const LDAP_PORT = 389;
const LDAP_DC = "dc=example,dc=org";
const LDAP_DN = "cn=admin,dc=example,dc=org";
const LDAP_PASS = "admin";

if (isset($_POST["logout"])) {
    session_destroy();
    header('Location: /', true , 301);
    exit;
}

if (isset($_POST["login"])) {
    $userId = $_POST['user_id'];
    $password = $_POST['password'];

    $ldapConn = ldap_connect(LDAP_HOST, LDAP_PORT);
    if (!$ldapConn) {
        exit('ldap_conn');
    }

    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapBind = ldap_bind($ldapConn, LDAP_DN,LDAP_PASS);
    if ($ldapBind) {

        $filter = '(&(cn=' . $userId . ')(userPassword=' . $password . '))'; 
        $ldapSearch = ldap_search($ldapConn, LDAP_DC, $filter);
        $getEntries = ldap_get_entries($ldapConn, $ldapSearch);
        if ($getEntries['count'] > 0) {
            // 成功
            $_SESSION["USERID"] = $userId;
            header('Location: /', true , 301);
            exit;
        }
    } else {
        
    }
}

?>

<html>
<?= $_SESSION["USERID"] ?>
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
