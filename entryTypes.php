<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config/config.php");

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);

$query = "SELECT * FROM entryTypes";

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  array_push($response, $row["type"]);
}

mysqli_free_result($result);

$json = json_encode($response);
echo $json;

mysqli_close($sql);
?>