<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");

const NAME_KEY = "username";
const PW_KEY = "passwordHash";

$username = $_POST["username"];
$hash = $_POST["passwordHash"];

$userData = Auth::logon($username, $hash);

JsonProtocol::sendResponse($userData);

?>