<?php
include("calendar_service.php");
include("request_client.php");

// PHP Pattern - Structural - Facade
class Event_Update {
  public $service;
  public $event;
  public $params;
  public $event_id;

  public function __construct(){
    $this->set_service();
    $this->set_event();
    $this->set_params();
  }

  public function set_service(){
    $calendar = new Calendar_Service();
    $this->service = $calendar->get_service();
  }

  public function set_event(){
    $this->event = new Google_Service_Calendar_Event();
  }
  
  public function set_params(){
    $this->params = new Event_Params();
    $this->params->set_summary("370 - Weekly Development Team Status");
  }

  public function set_event_id($id){
    $this->event_id = $id;
  }

  public function update_try(){
    // First retrieve the event from the API.
    $event = $this->service->events->get('primary', $this->event_id);
    $event->setSummary($this->params->get_summary());

    try {
      return $this->service->events->update('primary', $event->getId(), $event);
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

}

$update = new Event_Update();
$update->set_event_id("2bd2t3dmp60v7kkmgd42sp119k_20140923T183000Z");
echo "<pre>";
print_r($update->update_try());

?>