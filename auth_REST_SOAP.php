<?php
/*
This authentication file is used to switch between SOAP and REST on the one hand,
and between the two servers on the other.
*/
// technology
$soap   = true;  // SOAP
//$soap   = false; // REST

// Cascade instance, uncomment one of them when needed
$sandbox = false; // production
//$sandbox = true;  // sandbox

$techName = "rest";

if( $soap )
{
    $techName = "soap";
}

// Change this!!!
$server     = "http://mydomain.edu:1234";
$serverName = "production";

if( isset( $sandbox ) && $sandbox )
{
    $server     = "http://sandbox.mydomain.edu:1234";
    $serverName = "sandbox";
}

echo "<p style='color:red;font-weight:bold'>$techName, $serverName</p>";
require_once( "auth_user.php" );
?>