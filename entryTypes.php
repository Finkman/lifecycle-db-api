<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php_class/SqlConnection.inc");

$sql = new SqlConnection("d029cff0");

$query = "SELECT * FROM entryTypes";

$result = $sql->SendQuery($query);

echo "<pre>";
var_dump($result);
echo "</pre>";

$response = array();

foreach($result as $value){
  array_push($response, $value["types"]);
}

echo "<pre>";
var_dump($response);
echo "</pre>";

$json = json_encode($response);
echo "<pre>";
echo $json;
echo "</pre>";

?>