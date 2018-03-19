<?php

//This class connects to the ThyCry DataSQL-Server

class SqlConnection{
      public $Link;

       function __construct($DB){
               if(!$this->Link = mysqli_connect("localhost", "d029cff0", "r2EhmNFTY97fPYkX", $DB))
                   throw new Exception("SQLCONNECTION: Can not establish a connection to the sql server");


               #if(!mysql_select_db($DB))
               #    throw new Exception("SQLCONNECTION: Can not select database: \"".$DB."\"");
        }

       function __destruct(){
               /* schliessen der Verbinung */
               #mysql_close($this -> Link);
       }

       function SendQuery($Query, $feedback = true){
               $result = mysqli_query($this->Link, $Query);
               $i = 0;
      #echo var_dump($result);
               if($feedback){
               while ($Line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                       $List[$i] = $Line;
                       $i++;
               }
                mysqli_free_result($result); }

                return $List;
       }

}

?>