<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
//********************************************************************************
//********************************METODOS POST************************************
//********************************************************************************
//Postea un ride
$app->post('/api/post/new_ride/', function(Request $request, Response $response){
    
    $salida = $request->getParam('salida');
    $llegada = $request->getParam('llegada');
    $hora_salida = $request->getParam('hora_salida');
    $hora_llegada = $request->getParam('hora_llegada');
    $campos = $request->getParam('campos');
    $dias = $request->getParam('dias');
    $descripcion = $request->getParam('descripcion');
    $conductor =  $request->getParam('conductor');

    $consulta = "INSERT INTO rides (conductor,salida,llegada,hora_salida,hora_llegada,campos,dias,descripcion,id_usuario)  VALUES (:conductor,:salida,:llegada,:hora_salida,:hora_llegada,:campos,:dias,:descripcion,:id_usuario)";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':conductor', $conductor);
        $stmt->bindParam(':salida', $salida);
        $stmt->bindParam(':llegada', $llegada);
        $stmt->bindParam(':hora_salida', $hora_salida);
        $stmt->bindParam(':hora_llegada', $hora_llegada);
        $stmt->bindParam(':campos', $campos);
        $stmt->bindParam(':dias', $dias);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_usuario', $_SESSION["ride"]);
        $stmt->execute();

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Postea un usuarios
$app->post('/api/post/usuario/', function(Request $request, Response $response){
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $telefono = $request->getParam('telefono');
    $usuario = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');
    $consulta = "INSERT INTO usuarios (nombre,apellido,telefono,usuario,contrasena)  VALUES (:nombre,:apellido,:telefono,:usuario,:contrasena)";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Postea un usuario y ride
$app->post('/api/post/apartar/{id}', function(Request $request, Response $response){
    $id_ride = $request->getAttribute('id');
    
    $consulta = "INSERT INTO ride_usuario (id_ride,id_usuario)  VALUES (:ride,:usuario)";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':ride', $id_ride);
        $stmt->bindParam(':usuario', $_SESSION["ride"]);
        $stmt->execute();

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Cierra Session
$app->post('/api/post/salir/', function(Request $request, Response $response){
    
    try{
        // borra session
        unset($_SESSION["ride"]);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



//********************************************************************************
//********************************METODOS GET*************************************
//********************************************************************************
//Obtener todos los rides
$app->get('/api/get/rides/', function(Request $request, Response $response){
    $consulta = "SELECT * FROM rides where campos > 0";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener un ride en especifico
$app->get('/api/get/ride/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute("id");
    $consulta = "SELECT * FROM rides where id = ".$id;
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener un ride en especifico
$app->get('/api/get/id_ride/', function(Request $request, Response $response){
    $consulta = "select id_ride from ride_usuario where id_usuario = ".$_SESSION["ride"];
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener todos los rides fuera del usuario
$app->get('/api/get/rides_out/', function(Request $request, Response $response){
    $sql = $request->getParam("sql");
    $consulta = $sql." id_usuario <> ".$_SESSION["ride"];
    
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener todos los rides del usuario
$app->get('/api/get/rides_user/', function(Request $request, Response $response){
    $consulta = "SELECT * FROM rides where campos > 0 and id_usuario = ".$_SESSION["ride"];
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener todos los rides partados del usuario
$app->get('/api/get/rides_apartados/', function(Request $request, Response $response){
    $consulta = "select rides.*,ride_usuario.id_r from rides join ride_usuario on rides.id = ride_usuario.id_ride where ride_usuario.id_usuario =  ".$_SESSION["ride"];
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $rides = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($rides);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtener todos los usuarios
$app->get('/api/get/usuarios/', function(Request $request, Response $response){
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
//Obtiene un  usuarios
$app->get('/api/get/usuario/', function(Request $request, Response $response){
    $usuario = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');
    $consulta = "select * from usuarios where usuarios.usuario = '".$usuario."' and usuarios.contrasena = '".$contrasena."'";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $usuarios = $ejecutar->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        //session_start(); 
        $usuario = json_encode($usuarios,JSON_FORCE_OBJECT);
        if ($usuarios != null) {
            $_SESSION['ride'] = $usuarios[0]["id"];
        }
        //return $this->response->withJson(array("ok"=>"acceso autorizado"));
        //Exportar y mostrar en formato JSON
        echo $usuario;

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Obtiene los datos de un usuario por id
$app->get('/api/get/usuario_data/', function(Request $request, Response $response){
    $id = $request->getParam('id');
    $consulta = "select * from usuarios where usuarios.id = '".$id."'";
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $usuarios = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //session_start(); 
        $usuario = json_encode($usuarios,JSON_FORCE_OBJECT);
        //$_SESSION['ride'] = $usuario.id;
        //return $this->response->withJson(array("ok"=>"acceso autorizado"));
        //Exportar y mostrar en formato JSON
        echo $usuario;

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



//********************************************************************************
//********************************METODOS PUT*************************************
//********************************************************************************
//Actualiza dato del usuario
$app->put('/api/put/usuario/', function(Request $request, Response $response){
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $telefono = $request->getParam('telefono');
    $usuario = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');
    $consulta = "UPDATE usuarios SET
                nombre          = :nombre,
                apellido       = :apellido,
                telefono        = :telefono,
                usuario           = :usuario,
                contrasena       = :contrasena WHERE id = ".$_SESSION["ride"];
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();
        echo 'Usuario Actualizado';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Actualiza dato de un ride
$app->put('/api/put/ride/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $salida = $request->getParam('salida');
    $llegada = $request->getParam('llegada');
    $hora_salida = $request->getParam('hora_salida');
    $hora_llegada = $request->getParam('hora_llegada');
    $campos = $request->getParam('campos');
    $dias = $request->getParam('dias');
    $descripcion = $request->getParam('descripcion');

    $consulta = "UPDATE rides SET
                salida       = :salida,
                llegada      = :llegada,
                hora_salida  = :hora_salida,
                hora_llegada = :hora_llegada,
                campos       = :campos,
                dias         = :dias,
                descripcion  = :descripcion  
                WHERE id = ".$id;
               
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':salida', $salida);
        $stmt->bindParam(':llegada', $llegada);
        $stmt->bindParam(':hora_salida', $hora_salida);
        $stmt->bindParam(':hora_llegada', $hora_llegada);
        $stmt->bindParam(':campos', $campos);
        $stmt->bindParam(':dias',$dias);
        $stmt->bindParam(':descripcion',$descripcion);
        $stmt->execute();
        echo 'Ride Actualizado';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Actualiza dato de un ride
$app->patch('/api/patch/ride/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $campos = $request->getParam('campos');
    

    $consulta = "UPDATE rides SET
                campos       = :campos
                 
                WHERE id = ".$id;
               
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':campos', $campos);
        $stmt->execute();
        echo 'Ride Cancelado';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//Actualiza dato de un ride
$app->put('/api/put/ride_apartar/{id}/{cantidad}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $campos = $request->getAttribute('cantidad');

    $consulta = "UPDATE rides SET
                campos       = :campos
                WHERE id = ".$id;
               
    try{
        // Instanciar la base de datos
        $db = new database();

        // Conexión
        $campos= $campos-1;
        $db = $db->conectar();
        $stmt = $db->prepare($consulta);
        $stmt->bindParam(':campos',$campos );
        $stmt->execute();
        echo 'Ride Apartado';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



//********************************************************************************
//*******************************METODOS DELETE***********************************
//********************************************************************************
// Borrar ride
$app->delete('/api/delete/ride/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM rides WHERE id = $id";

    try{
        // Instanciar la base de datos
        $db = new database();
        // Conexion
        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Ride Borrado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
// Borrar ride
$app->delete('/api/delete/ride_user/{id_ride}/{id_ride_user}', function(Request $request, Response $response){
    $id_ride = $request->getAttribute('id_ride');
    $id_ride_user = $request->getAttribute('id_ride_user');
    $sql = "DELETE FROM ride_usuario WHERE id_r = $id_ride_user";

    try{
        // Instanciar la base de datos
        $db = new database();
        // Conexion
        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});