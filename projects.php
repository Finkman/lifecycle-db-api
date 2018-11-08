<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");

Auth::restrictAccess();

if(isset($_GET['id'])){
  $id = urldecode($_GET['id']);
  $whereClause = "WHERE id = ".$id;
}else{
  $whereClause = "WHERE 1";
}

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);

$query = "SELECT * FROM projects ".$whereClause;

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  array_push($response, $row);
}

mysqli_free_result($result);

JsonProtocol::sendResponse($response);

mysqli_close($sql);
?>