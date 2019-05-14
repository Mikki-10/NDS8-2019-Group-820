<?php

var_dump(ldap_escape(trim($_GET["username"])));

var_dump(ldap_escape(trim($_GET["username"]), null, LDAP_ESCAPE_FILTER));

var_dump(ldap_escape(trim($_GET["username"]), null, LDAP_ESCAPE_DN));

?>