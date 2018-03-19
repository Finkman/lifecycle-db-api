<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/config/config.php");

if(isset($_GET['device'])){
  $id = urldecode($_GET['device']);
  $whereClause = "WHERE d.device = ".$id;
}else{
  $whereClause = "WHERE 1";
}

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);

$query = "SELECT d.id, t.type, d.date, d.data FROM `deviceEntries` AS d INNER JOIN `entryTypes` as t on d.type = t.id ".$whereClause;

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  array_push($response, $row);
}

mysqli_free_result($result);

$json = json_encode($response);
header('Content-Type: application/json');
echo $json;

mysqli_close($sql);
?>