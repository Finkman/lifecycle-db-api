<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/response.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/authorization.php");

$rawPostData = file_get_contents('php://input');

Auth::restrictAccess();

$isOnPostMode = $rawPostData ? true : false;

$sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);


if(!$isOnPostMode)
{
  if(isset($_GET['device'])){
    $id = urldecode($_GET['device']);
    $whereClause = "WHERE d.device = ".$id;
  }else{
    $whereClause = "WHERE 1";
  }

  $query = "SELECT d.id, t.type, d.date, d.data FROM `deviceEntries` AS d INNER JOIN `entryTypes` as t on d.type = t.id ".$whereClause;

  $result = mysqli_query($sql, $query);

  $response = array();

  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    array_push($response, $row);
  }

  mysqli_free_result($result);

  JsonProtocol::sendResponse($response);

}
else
{
  $postObj = json_decode($rawPostData);

  // first, resolve type-name to type-id
  $query =  'SELECT id FROM entryTypes WHERE type = "'.$postObj->type.'"';
  $result =  mysqli_query($sql, $query);
  if(!$row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    die("Could not resolve type-name");
  }

  mysqli_free_result($result);


  $typeId = $row["id"];

  $date = mysqli_real_escape_string($sql, $postObj->date);
  $data = mysqli_real_escape_string($sql, $postObj->data);

  $query = "INSERT INTO `deviceEntries` (`device`, `type`, `date`, `data`) VALUES ('".$postObj->device."', '".$typeId."', '".$date."', '".$data."')";
  $resulst = mysqli_query($sql, $query);

  if($result){
    JsonProtocol::sendResponse($rawPostData);
  }else{
    JsonProtocol::sendResponse(false);
  }
}

mysqli_close($sql);

?>