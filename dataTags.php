<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config/config.php");

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);
$typeName = $_GET["type"];
$query = "SELECT t.type, e.data, count(e.data) as count FROM `deviceEntries` as e INNER JOIN `entryTypes` as t on e.type = t.id  WHERE t.type = \"".$typeName."\" GROUP BY data ORDER BY COUNT DESC LIMIT 100";

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  array_push($response, $row["data"]);
}

mysqli_free_result($result);

$json = json_encode($response);
echo $json;

mysqli_close($sql);
?>