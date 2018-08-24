<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");

$rawPostData = file_get_contents('php://input');


$isOnPostMode = $rawPostData ? true : false;

// if(!$isOnPostMode)
// {
//   header("HTTP/1.1 404 Not Found" );
//   die('Not Found');
// }

Auth::restrictAccess();

if(!isset($_GET['projectId'])){
  header("HTTP/1.1 400 Bad Request" );
  die('Bad Request');
}

$date = date("Y-m-d");

$projectId = 0 + $_GET['projectId'];

$query = 'INSERT INTO devices(project, sn, production_date) 
SELECT
'.$projectId.',
MAX(sn) + 1,
"'.$date.'"
FROM `devices` WHERE project = '.$projectId;

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);
$result = mysqli_query($sql, $query);

if($result){
   JsonProtocol::sendResponse(true);
}else{
   JsonProtocol::sendResponse(false);
}

?>