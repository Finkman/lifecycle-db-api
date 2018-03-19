<?php

//This class connects to the ThyCry DataSQL-Server

class SqlConnection{
      public $Link;

       function __construct($DB){
               if(!$this->Link = mysql_connect("localhost", "dd34624", "r2EhmNFTY97fPYkX"))
                   throw new Exception("SQLCONNECTION: Can not establish a connection to the sql server");

               if(!mysql_select_db($DB))
                   throw new Exception("SQLCONNECTION: Can not select database: \"".$DB."\"");
                  }
       function __destruct(){
               /* schliessen der Verbinung */
               #mysql_close($this -> Link);
       }

       function SwitchDB($DB){
                   /*
                   if(!mysql_select_db($DB))
                   throw new Exception("SQLCONNECTION: Can not select database: \"".$DB."\"");
                   */
       }

       function SendQuery($Query, $feedback = true){
               $result = mysql_query($Query);
               $i = 0;
      #echo var_dump($result);
               if($feedback){
               while ($Line = mysql_fetch_array($result, MYSQL_ASSOC)){
                       $List[$i] = $Line;
                       $i++;
               }
                mysql_free_result($result); }

                return $List;
       }

}

?>