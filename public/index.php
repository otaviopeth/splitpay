<?php

use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../vendor/autoload.php';
require '../src/cfg/db.php';

$app = AppFactory::create();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

// Add Slim routing middleware
$app->addRoutingMiddleware();

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

// Retorna os registros paginados
function getData ($countsql, $datasql, $page, $limit, $input){
  try{
      $offset = ($page-1) * $limit; 

      $db = new Db();
      $db = $db->connect();
      $countQuery = $db->prepare( $countsql );
      $dataQuery = $db->prepare( $datasql );
      $dataQuery->bindParam(':limit', $limit, \PDO::PARAM_INT);
      $dataQuery->bindParam(':offset', $offset, \PDO::PARAM_INT);

      while(sizeof($input)){
          $curr = array_pop($input);
          $dataQuery->bindParam($curr["key"], $curr["keyvalue"]);
          $countQuery->bindParam($curr["key"], $curr["keyvalue"]);
      }

      $dataQuery->execute();
      $countQuery->execute();
      $db = null; 

      $count = $countQuery->fetch(PDO::FETCH_ASSOC); 
      $num = $count['COUNT'];
      if($num>0){
          $data_arr["pagina_atual"] =  $page;
          $data_arr["total_paginas"] = ceil($num/$limit);
          $data_arr["total_registros"] = $num;
          $data_arr["registros_por_pagina"] = $limit;

          $data_arr["registros"] = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
 

          http_response_code(200);
          return json_encode($data_arr);
      }
      else{
          http_response_code(404);
          return json_encode(
              array("message" => "Nada encontrado.")
          );
      }
  }catch( PDOException $e ) {
      return '{"error": {"text": ' . $e->getMessage() . '}';
  } 
}


// Rotas para os produtos

require '../src/routes/produtos.php';


// Run app
$app->run();