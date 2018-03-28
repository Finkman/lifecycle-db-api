<?php

class JsonProtocol{
  public static function sendResponse($data){
    $json = json_encode($data);
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');
    echo $json;
  }
}

?>