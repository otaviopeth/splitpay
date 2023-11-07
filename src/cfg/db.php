<?php

class Db{


private $host = 'localhost';
private $user = 'root';
private $password = '';
private $schema = 'splitpayapp';


//Faz a conexão com o banco de dados via PDO
public function connect(){
  $mysql_connect_str = "mysql:host=$this->host;dbname=$this->schema";
  $connection = new PDO($mysql_connect_str, $this->user, $this->password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $connection;

}



}