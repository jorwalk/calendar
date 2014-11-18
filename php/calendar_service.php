<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set("US/Central");
set_include_path('php/');
require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';
session_start();

// PHP Pattern - Fluent Interface
class Client_Fluent {
  
  public $client_id;
  public $client_secret;
  public $client_key;
  public $redirect_uri;

  public function __construct(){
    $client_id = '858792117555-6kgdm0tsv0iev99ljtvml6tqcfp9ljk1.apps.googleusercontent.com';
    $client_secret = 'Zlj38Cc_Q2Kcb55v_KTLE7kG';
    $client_key = 'AIzaSyDqMb58kw_R0Ci19ArEcaNj30yTojE7c7g';
    $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].'/calendar/';

    $this->_set_client_id($client_id)
    ->_set_client_secret($client_secret)
    ->_set_client_key($client_key)
    ->_set_redirect_uri($redirect_uri);
  }
  
  public function get_client_id(){
    return $this->client_id;
  }
  
  public function get_client_secret(){
    return $this->client_secret;
  }

  public function get_client_key(){
    return $this->client_key;
  }
  
  public function get_redirect_uri(){
    return $this->redirect_uri;
  }

  private function _set_client_id($client_id){
    $this->client_id = $client_id;
    return $this;
  }

  private function _set_client_secret($client_secret){
    $this->client_secret = $client_secret;
    return $this;
  }

  private function _set_client_key($client_key){
    $this->client_key = $client_key;
    return $this;
  }
  
  private function _set_redirect_uri($redirect_uri){
    $this->redirect_uri = $redirect_uri;
    return $this;
  }
}

class Client_Bridge {
  
  public $client;
  public $interface;
  
  public function __construct(){
    $this->interface = new Client_Fluent();
  }

  public function get_client(){
    $this->set_client();
    return $this->client;
  }

  public function get_redirect_uri(){
    return $this->interface->get_redirect_uri();
  }

  private function set_client(){
    $this->client = new Google_Client();
    $this->client->setClientId($this->interface->get_client_id());
    $this->client->setClientSecret($this->interface->get_client_secret());
    $this->client->setDeveloperKey($this->interface->get_client_key());
    $this->client->setRedirectUri($this->get_redirect_uri());
    $this->client->setAccessType('offline');
    $this->client->addScope("https://www.googleapis.com/auth/calendar");
    $this->client->addScope("https://www.googleapis.com/auth/calendar.readonly");
  }
}

class Client_Command {
  public $bridge;
  public $client;
  public $auth_url;
  
  function __construct(){
    $this->set_bridge();
    $this->set_client();

    //setcookie (session_id(), "", time() - 3600);
    //session_destroy();
    //session_write_close();
  }
  
  function set_bridge(){
    $this->bridge = new Client_Bridge();
  }

  function set_client(){
    $this->client = $this->bridge->get_client();
  }

  public function get_client(){
    return $this->client;
  }

  public function try_access_token(){
    if (isset($_GET['code'])) :
      $this->client->authenticate($_GET['code']);
      $_SESSION['access_token'] = $this->client->getAccessToken();
      $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
      header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    endif;

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) :
      $this->client->setAccessToken($_SESSION['access_token']);
      $google_token= json_decode($_SESSION['access_token']);
      try{
        $this->client->refreshToken($google_token->refresh_token);  
      } catch(exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
      }
      
      
      $_SESSION['access_token']= $this->client->getAccessToken();

    else:
      $this->auth_url = $this->client->createAuthUrl();
    endif;
  }
  
  public function get_auth_url(){
    return $this->auth_url;
  }
}

class Calendar_Service {

  public $client;
  public $client_command;
  public $service;
  
  function __construct(){
    $this->_set_client();
    $this->_set_service();
  }

  private function _set_client(){
    $this->client_command = new Client_Command();
    $this->client = $this->client_command->get_client();
    $this->client_command->try_access_token();
  }

  private function _set_service(){
    $this->service = new Google_Service_Calendar($this->client);
  }

  public function get_access_token(){
    return $this->client->getAccessToken();
  }

  public function get_service(){
    return $this->service;
  }

  public function get_auth_url(){
    return $this->client_command->get_auth_url();
  }

  public function set_token(){
    $_SESSION['access_token'] = $this->client->getAccessToken();
  }
}

$service = new Calendar_Service();
?>