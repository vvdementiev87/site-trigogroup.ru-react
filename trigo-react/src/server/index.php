<?php
header("Access-Control-Allow-Origin: https://trigogroup.ru, http://localhost:3000");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();
require_once 'model/UserProvider.php';

$pdo = require 'model/db.php';

if (isset($_POST['username'])){
    echo $_REQUEST['REQUEST_METHOD'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userProvider = new UserProvider($pdo);
    $user = $userProvider->getByUsernameAndPassword($username, $password);  
    if ($user === null) {
        $error = [
            'error' => 'Пользователь с указанными учетными данными не найден'
        ];
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($user->get_object_as_array(), JSON_UNESCAPED_UNICODE);            
    } 
}

/* 
    switch ($method) {
      case 'GET':
        echo 'You are using '.$method.' Method';
        break;
      case 'POST':
        try {
             
        } catch (\Throwable $th) {
            json_encode($th, JSON_UNESCAPED_UNICODE);
        }              
        break;
      case 'PUT':
        //Here Handle PUT Request
        echo json_encode('You are using '.$method.' Method', JSON_UNESCAPED_UNICODE);
        break;
      case 'PATCH':
        //Here Handle PATCH Request
        echo 'You are using '.$method.' Method';
        break;
      case 'DELETE':
        //Here Handle DELETE Request
        echo 'You are using '.$method.' Method';
        break;
      case 'COPY':
          //Here Handle COPY Request
          echo 'You are using '.$method.' Method';
          break;
    
      case 'OPTIONS':
          //Here Handle OPTIONS Request
          echo 'You are using '.$method.' Method';
          break;
      case 'LINK':
          //Here Handle LINK Request
          echo 'You are using '.$method.' Method';
          break;
      case 'UNLINK':
          //Here Handle UNLINK Request
          echo 'You are using '.$method.' Method';
          break;
      case 'PURGE':
          //Here Handle PURGE Request
          echo 'You are using '.$method.' Method';
          break;
      case 'LOCK':
          //Here Handle LOCK Request
          echo 'You are using '.$method.' Method';
          break;
      case 'UNLOCK':
          //Here Handle UNLOCK Request
          echo 'You are using '.$method.' Method';
          break;
      case 'PROPFIND':
          //Here Handle PROPFIND Request
          echo 'You are using '.$method.' Method';
          break;
      case 'VIEW':
          //Here Handle VIEW Request
          echo 'You are using '.$method.' Method';
          break;
      Default:
      handle_error($method);
      break;
    }




function handle_error($method)
{
    var_dump($method);
}; */

?>