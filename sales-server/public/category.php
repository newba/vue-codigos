<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/categories', function (Request $request, Response $response) {

        $sql = "";
        $parameters = $request->getQueryParams();
        $start =(int)$parameters['start'];
        $limit =(int)$parameters['limit'];


        if (!empty($start)&&!empty($limit)){
            $start--;
            $sql = "SELECT id,name FROM categories LIMIT :start,:limit";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':start', $start,PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit,PDO::PARAM_INT);
            $stmt->execute();

            $sqlCount =  "SELECT count(id) FROM categories";
            $stmtCount = DB::prepare($sqlCount);
            $stmtCount->execute();
            $total = $stmtCount->fetchColumn();

             return  $response->withJson($stmt->fetchAll())->withHeader('Access-Control-Expose-Headers','x-total-count')->withHeader('x-total-count', $total);

        }else{
            $sql = "SELECT id,name FROM categories";
            $stmt = DB::prepare($sql);
            $stmt->execute();

            return  $response->withJson($stmt->fetchAll());
        }



});

$app->post('/category', function (Request $request, Response $response) {

    try{

        $category = (object)$request->getParsedBody();

        if (!empty($category->id)){
            //update
            $sql = "UPDATE  categories SET name=:name WHERE id=:id";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':name', $category->name);
            $stmt->bindParam(':id', $category->id,PDO::PARAM_INT);
            $stmt->execute();
            return $response->withJson($category);
        }else{
            //insert
            $sql = "INSERT INTO categories (name) VALUES (:name)";
            $stmt = DB::prepare($sql);
            $stmt->bindParam(':name', $category->name);
            $stmt->execute();
            $category->id = DB::lastInsertId();
            return $response->withJson($category);
        }

    }
	catch(\Exception $e){
		return $response->withStatus(500)->write($e->getMessage());
	}

})->add($auth);