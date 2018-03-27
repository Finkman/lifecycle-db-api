<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/config/config.php");
const FW_TYPE_ID = 2;
const HW_TYPE_ID = 1;

function getLatestDeviceEntry($connection, $deviceId, $typeId, $defaultValue = ""){
  $query =  "SELECT data FROM `deviceEntries` as e WHERE type = ".$typeId." AND device = ".$deviceId." ORDER BY date DESC LIMIT 1";
  $nestedResult = mysqli_query($connection,$query);
  $nestedRow = mysqli_fetch_array($nestedResult, MYSQLI_ASSOC);
  mysqli_free_result($nestedResult);
  if($nestedRow){
    return $nestedRow["data"];
  }else{
    return $defaultValue;
  }
}


if(isset($_GET['device'])){
  $id = urldecode($_GET['device']);
  $whereClause = "WHERE id = ".$id;
}else{
  $project = urldecode($_GET['project']);
  if(isset($_GET['project'])){
    $whereClause = "WHERE project = ".$project;
  }
  else{
    $whereClause = "WHERE 1";
  }
}

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);

$query = "SELECT * FROM devices ".$whereClause;

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

  $row["fwVersion"] = getLatestDeviceEntry($sql, $row["id"], FW_TYPE_ID);
  $row["hwVersion"] = getLatestDeviceEntry($sql, $row["id"], HW_TYPE_ID);

  array_push($response, $row);
}

mysqli_free_result($result);

$json = json_encode($response);
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
echo $json;

mysqli_close($sql);
?>