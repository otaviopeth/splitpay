<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Lista todos os produtos

$app->get('/api/produtos', function (Request $request, Response $response) {
  $sql = "SELECT * FROM produtos";
  try {

    //Faz a conexão com o banco e realiza a query.
    $db = new Db();
    $db = $db->connect();
    $stmt = $db->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    $data = json_encode($produtos);

    $response->getBody()->write($data);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } catch (PDOException $e) {
    echo '{"error": "text":' . $e->getMessage() . '}';
  }
});


//Busca os produtos por ID

$app->get('/api/produtos/{id}', function (Request $request, Response $response, array $args) {
  $id = $args['id'];
  $sql = "SELECT * FROM produtos WHERE id=$id";
  try {
    //Faz a conexão com o banco e realiza a query.
    $db = new Db();
    $db = $db->connect();
    $stmt = $db->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    $data = json_encode($produtos);

    $response->getBody()->write($data);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } catch (PDOException $e) {
    echo '{"error": "text":' . $e->getMessage() . '}';
  }
});

//Cadastra um novo produto

$app->post('/api/produtos', function (Request $request, Response $response) {

  //Captura todos os parametros passados na requisicao
  $params = (array)$request->getParsedBody();

  $name = $params['nome'];
  $description = $params['descricao'];
  $price = $params['preco'];
  $amount = $params['quantidade'];

  $sql = "INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (:nome, :descricao, :preco, :quantidade)";
  try {
    //Faz a conexão com o banco e realiza a query.
    $db = new Db();
    $db = $db->connect();

    $stmt = $db->prepare($sql);
    $stmt->execute([
      ":nome" => $name,
      ":descricao" => $description,
      ":preco" => $price,
      ":quantidade" => $amount
    ]);


    //Retorna 200 OK 

    $msg = array('message' => "Produto adicionado com sucesso!");
    $msgJson = json_encode($msg);


    $response->getBody()->write($msgJson);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } catch (PDOException $e) {

    $msgErr = array('error' => $e->getMessage());
    $msgErrJson = json_encode($msgErr);
    $response->getBody()->write($msgErrJson);
    return $response
      ->withHeader('Content-Type', 'application/json')->withStatus(400);
  }
});

//Atualiza um produto

$app->put('/api/produtos/{id}', function (Request $request, Response $response) {

  //Captura todos os parametros passados na requisicao
  $params = (array)$request->getParsedBody();
  $id = $request->getAttribute('id');
  $name = $params['nome'];
  $description = $params['descricao'];
  $price = $params['preco'];
  $amount = $params['quantidade'];

  $sql = "UPDATE produtos SET
          nome = :nome,
          descricao = :descricao,
          preco = :preco,
          quantidade = :quantidade
         WHERE id=$id";
  try {
    //Faz a conexão com o banco e realiza a query.
    $db = new Db();
    $db = $db->connect();

    $stmt = $db->prepare($sql);
    $stmt->execute([
      ":nome" => $name,
      ":descricao" => $description,
      ":preco" => $price,
      ":quantidade" => $amount
    ]);


    //Retorna 200 OK 

    $msg = array('message' => "Produto atualizado com sucesso!");
    $msgJson = json_encode($msg);


    $response->getBody()->write($msgJson);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } catch (PDOException $e) {

    $msgErr = array('error' => $e->getMessage());
    $msgErrJson = json_encode($msgErr);
    $response->getBody()->write($msgErrJson);
    return $response
      ->withHeader('Content-Type', 'application/json')->withStatus(400);
  }
});


//Exclui um produto

$app->delete('/api/produtos/{id}', function (Request $request, Response $response, array $args) {
  $id = $args['id'];
  $sql = "DELETE FROM produtos WHERE id=$id";
  try {
    //Faz a conexão com o banco e realiza a query.
    $db = new Db();
    $db = $db->connect();

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;

    $msg = array('message' => "Produto deletado com sucesso!");
    $msgJson = json_encode($msg);

    $response->getBody()->write($msgJson);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } catch (PDOException $e) {
    echo '{"error": "text":' . $e->getMessage() . '}';
  }
});