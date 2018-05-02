<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Obtener todos los usuarios
$app->get('/api/get/usuarios', function(Request $request, Response $response){
    $consulta = "SELECT * FROM usuarios";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $usuarios = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($usuarios);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener todos los usuarios
$app->get('/api/post/usuario/', function(Request $request, Response $response){
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $telefono = $request->getParam('telefono');
    $usuario = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');
    $consulta = "INSERT INTO usuarios ('nombre', 'apellido','telefono','usuario','contrasena')  VALUES (:nombre,:apellido,:telefono,:usuario,:contrasena)";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido',  $apellido);
        $stmt->bindParam(':telefono',      $telefono);
        $stmt->bindParam(':usuario',      $usuario);
        $stmt->bindParam(':contrasena',    $contrasena);
        $stmt->execute();

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
