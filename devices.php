<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");

Auth::restrictAccess();

const FW_TYPE_ID = 2;
const HW_TYPE_ID = 1;
const SHIP_TYPE_ID = 4;
const DELIV_TYPE_ID = 6;

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

function getLocation($connection, $deviceId){
  $query = "SELECT `type` FROM `deviceEntries` as e WHERE (`type` = 4 OR `type` = 6) AND `device` = ".$deviceId." ORDER BY `date` DESC, `id` DESC  LIMIT 1";
  $nestedResult = mysqli_query($connection,$query);
  $nestedRow = mysqli_fetch_array($nestedResult, MYSQLI_ASSOC);
  mysqli_free_result($nestedResult);
  if($nestedRow){
    return ($nestedRow["type"] == SHIP_TYPE_ID) ? "shipped" : "local";
  }else{
    return "?";
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

$query = "SELECT id, sn, project as projectId, production_date FROM devices ".$whereClause;

$result = mysqli_query($sql, $query);

$response = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

  $row["hwVersion"] = getLatestDeviceEntry($sql, $row["id"], HW_TYPE_ID);
  $row["fwVersion"] = getLatestDeviceEntry($sql, $row["id"], FW_TYPE_ID);
  $row["location"] = getLocation($sql, $row["id"]);

  array_push($response, $row);
}

mysqli_free_result($result);

JsonProtocol::sendResponse($response);

mysqli_close($sql);
?>
