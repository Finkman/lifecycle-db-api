<?php
require_once($_SERVER['DOCUMENT_ROOT']."/api/components/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/helpers/jwt.php");

class Auth{

  public static function fetchUserByName($nickname){
    $query = 'SELECT * FROM user WHERE username = "'.$nickname.'" LIMIT 1';

    $sql = mysqli_connect(config\DB_HOST, config\DB_USER, config\DB_PASS, config\DB_NAME);

    $result = mysqli_query($sql, $query);
        
    $response = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    mysqli_free_result($result);
    
    mysqli_close($sql);

    return $response;
  }
  

  public static function logon($user, $passHash){
    // first get username
    $userData = self::fetchUserByName($user);
    if(!isset($userData) || ($userData["password"] != $passHash)){
      return NULL;
    }

    // generate token!

    $payload = array();
    $payload["id"] = $userData["id"];
    //$payload["user"] = $userData["user"];
    $payload["level"] = $userData["level"];
    $payload["nbf"] = time();
    $payload["exp"] = time() + config\TOKEN_EXPIRY;

    //$userData["tp"] = $payload;
    $key = config\TOKEN_SECRET;
    $token = JWT::encode($payload, $key);

    $userData["token"] = $token;

    return $userData;
  }

  /**
   * This function will cause 403, if no valid
   * token has been send.
   */
  public static function restrictAccess(){
    if($_SERVER['REQUEST_METHOD'] == "OPTIONS"){
      header("HTTP/1.1 200 OK");
      header("Access-Control-Allow-Headers: Content-Type, Authorization, Post, Get");
      header("Access-Control-Allow-Origin: *");
      exit();
    }
    if(!self::checkToken()){
      header("HTTP/1.1 401 Unauthorized" );
      die('Unauthorized');
    }
  }

  /**
   * This function will check token and re-new it
   */
  public static function checkToken(){
    try{
      $tokenLoad = (array)self::getUserFromToken();
      //var_dump($tokenLoad);
      if($tokenLoad == NULL) return false;
      // check if token is to old, or to young
      if($tokenLoad["nbf"] > time()){
        return false;
      }

      if($tokenLoad["exp"] < time()){
        return false;
      }

    }catch(Throwable $t){
      return false;
    }

    return true;
  }

  /**
   * Get the user data from token, if sent.
   * Otherwise, null
   */
  public static function getUserFromToken(){
    $token = self::getBearerToken();
    if($token == null){
      return null;
    }

    // try{
    $key = config\TOKEN_SECRET;
    $decoded = JWT::decode($token, $key, true);
    // $decoded = JWT::decode($token, "huhu", true);
    return $decoded;
    // }
    // catch(Throwable $t){
    //   return null;
    // }
    // catch(Exception $e){
    //   return null;
    // }
    
  }

  /** 
   * Get hearder Authorization
   * */
  private static function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
  }

  /**
   * get access token from header
   * */
  private static function getBearerToken() {
    $headers = self::getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
      }
    }
    return null;
  }
}

?>