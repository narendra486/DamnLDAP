<?php

session_start();

// LDAP connection information
const LDAP_HOST = "openldap-container";
const LDAP_PORT = 389;
const LDAP_DC = "dc=example,dc=org";
const LDAP_DN = "cn=admin,dc=example,dc=org";
const LDAP_PASS = "admin";

function highlightQueryInput($query, $input)
{
    return str_replace($input, '<span style="color: red; font-weight: bold;">' . $input . '</span>', $query);
}

if (isset($_POST["logout"])) {
    session_destroy();
    header('Location: /', true, 301);
    exit;
}

$loginError = '';
$ldapQuery = '';

if (isset($_POST["login"])) {
    $userId = $_POST['user_id'];
    $password = $_POST['password'];

    // Store password in the session
    $_SESSION["PASSWORD"] = $password;

    // LDAP connection
    $ldapConn = ldap_connect(LDAP_HOST, LDAP_PORT);
    if (!$ldapConn) {
        exit('ldap_conn');
    }

    // Bind
    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapBind = ldap_bind($ldapConn, LDAP_DN, LDAP_PASS);
    if ($ldapBind) {

        // Login process
        $filter = '(&(cn=' . $userId . ')(userPassword=' . $password . '))';

        // Store the LDAP query
        $ldapQuery = highlightQueryInput("LDAP Query: $filter", $userId);

        $ldapSearch = ldap_search($ldapConn, LDAP_DC, $filter);
        $getEntries = ldap_get_entries($ldapConn, $ldapSearch);

        if ($getEntries['count'] > 0) {
            // Success
            $_SESSION["USERID"] = $userId;
            // Redirect to prevent resubmission on page refresh
            header('Location: /', true, 301);
            exit;
        } else {
            // Login failed
            $loginError = "Username or password is incorrect";
        }
    } else {
        // Failure
    }
}

?>

<html>
<head>
    <title>Welcome to Ldap Login Page</title>
    <style>
        .ldap-query-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 20px auto;
            max-width: 400px;
            text-align: center;
        }
    </style>
</head>
<body style="text-align: center;">

<h1>Welcome to Users Portal</h1>

<?php if (isset($_SESSION["USERID"])) : ?>
    <p style="font-size: 24px; color: green;">You have logged in successfully.</p>
    <p>Logged in as: <?= htmlspecialchars($_SESSION["USERID"]) ?></p>
    <form action="/" method="POST">
        <input type="hidden" name="logout" value="1"/>
        <input type="submit" value="Logout"/>
    </form>
    <?php
    // Set LDAP query for display
    $userId = $_SESSION["USERID"];
    $password = $_SESSION["PASSWORD"]; // Retrieve password from the session
    $filter = '(&(cn=' . $userId . ')(userPassword=' . $password . '))';
    $ldapQuery = highlightQueryInput("LDAP Query: $filter", $userId);
    ?>
    <div class="ldap-query-box">
        <p><?php echo $ldapQuery; ?></p>
    </div>
<?php else : ?>
    <?= $loginError ? "<p style='color: red;'>$loginError</p>" : "" ?>
    <form action="/" method="POST" style="display: inline-block; text-align: left;">
        <label>User ID: </label><br/>
        <input type="text" name="user_id" style="margin-bottom: 10px;"/><br/>
        <label>Password: </label><br/>
        <input type="password" name="password" style="margin-bottom: 10px;"/><br/>
        <input type="hidden" name="login" value="1"/>
        <input type="submit" name="submit" value="Submit"/>
    </form>

    <?php if ($ldapQuery): ?>
        <div class="ldap-query-box">
            <p><?php echo $ldapQuery; ?></p>
        </div>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>
