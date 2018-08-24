<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");

Auth::restrictAccess();

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);
$typeName = urldecode($_GET["type"]);
$projectId = 0 + $_GET["projectId"];
// $query = "SELECT t.type, e.data, count(e.data) as count FROM `deviceEntries` as e INNER JOIN `entryTypes` as t on e.type = t.id  WHERE t.type = \"".$typeName."\" GROUP BY data ORDER BY COUNT DESC LIMIT 100";
$query = "SELECT
t.type, e.data, count(e.data) as count
FROM `deviceEntries` as e 
INNER JOIN `entryTypes` as t on e.type = t.id 
INNER JOIN `devices` as d on d.id = e.device
WHERE t.type = \"".$typeName."\" AND d.project = ".$projectId."
GROUP BY data ORDER BY COUNT DESC 
LIMIT 100";

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
  array_push($response, $row);
}

mysqli_free_result($result);

JsonProtocol::sendResponse($response);

mysqli_close($sql);
?>