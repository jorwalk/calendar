<?php 
// PHP Pattern - Facade
class Calendar_Read {

  public $service;
  public $params;
  

  public function __construct(){
    $calendar = new Calendar_Service();
    $this->service = $calendar->get_service();
    $this->_get_params();
  }

  private function _get_params(){
      $this->params = new Event_Params();
      $this->params->set_single_event("true")
      ->set_order_by("startTime")
      ->set_time_max("2014-10-24T18:00:00.000-07:00")
      ->set_time_min("2014-10-20T07:00:00.000-07:00");
  }

  public function get(){
    try {
      return $this->service->calendars->get('primary'); 
    } catch (Exception $e) {

      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function events(){
    try {
      return $this->service->events->listEvents('primary',$this->params->get());
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } 
  }

  public function __toString(){
    return $this->get();
  }
}

// PHP Pattern - Structural - Facade
class Event_Create {
  public $service;
  public $event;
  public $params;

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
    $this->params->set_summary("370 - Appointment")
    ->set_location('HLK')
    ->set_description('Appointment to rock!')
    ->set_time_min('2014-09-17T10:00:00.000-07:00')
    ->set_time_max('2014-09-17T10:25:00.000-07:00');
  }

  public function insert(){

    $this->event->setSummary($this->params->get_summary());
    $this->event->setLocation($this->params->get_location());
    $this->event->setDescription($this->params->get_description());
    
    $this->set_start();
    $this->set_end();
    
    return $this->insert_try();
  }

  public function insert_try(){
    try {
      $createdEvent = $this->service->events->insert('primary', $this->event);
      return $createdEvent->getId();
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function set_start(){
    $start = new Google_Service_Calendar_EventDateTime();
    $start->setDateTime($this->params->get_time_min());
    $this->event->setStart($start);
  }

  public function set_end(){
    $end = new Google_Service_Calendar_EventDateTime();
    $end->setDateTime($this->params->get_time_max());
    $this->event->setEnd($end);
  }

  /*
  $this->attendee1 = new EventAttendee();
  $this->attendee1->setEmail('attendeeEmail');
  $attendees = array($attendee1,  );
  $this->event->attendees = $attendees;
  */
}



// PHP Pattern - Fluent Interface
class Event_Params {

  public $single_event;
  public $order_by;
  public $time_max;
  public $time_min;
  public $summary;
  public $location;
  public $description;

  public function set_single_event($bool = true){
    $this->single_event = $bool;
    return $this;
  }

  public function get_single_event(){
    return $this->single_event;
  }
  
  public function set_order_by($str){
    $this->order_by = $str;
    return $this;
  }

  public function get_order_by(){
    return $this->order_by;
  }

  public function set_time_max($time){
    $this->time_max = $time;
    return $this;
  }

  public function get_time_max(){
    return $this->time_max;
  }
  
  public function set_time_min($time){
    $this->time_min = $time;
    return $this;
  }

  public function get_time_min(){
    return $this->time_min;
  }

  public function set_summary($summary){
    $this->summary = $summary;
    return $this;
  }

  public function get_summary(){
    return $this->summary;
  }

  public function set_location($location){
    $this->location = $location;
    return $this;
  }

  public function get_location(){
    return $this->location;
  }

  public function set_description($description){
    $this->description = $description;
    return $this;
  }

  public function get_description(){
    return $this->description;
  }

  public function get(){
    $opt = array(
      'singleEvents'  => $this->get_single_event(),
      'orderBy'       => $this->get_order_by(),
      'timeMax'       => $this->get_time_max(),
      'timeMin'       => $this->get_time_min()
    );

    return $opt;
  }
}

// PHP Pattern - Creational / Factory Method
class Events_Date_Format {
  public $events;

  public function __construct($events){
    $this->events = $events;
  }

  public function get_events(){
    return $this->events;
  }

  public function events_by_day(){
      $event_week = array();
      $events = $this->get_events();

      foreach ($events->getItems() as $key=> $event):
        $id = $event->getId();
        
        $start = $event->getStart()->dateTime;
        $date_start = date_create_from_format('Y-m-d\TH:i:sP',$start);
        
        $end = $event->getEnd()->dateTime;
        $date_end = date_create_from_format('Y-m-d\TH:i:sP',$end);
        
        $interval = $date_start->diff($date_end);
        $hours    = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
        
        $mdy = date_format($date_start, 'm-d-Y');
        $event_week[$mdy]['date'] = date_format($date_start, 'D d');
        $event_week[$mdy]['jobs'][] = array(
          'summary'=> $event->getSummary(),
          'start'=> date_format($date_start, 'H:i'),
          'end'=> date_format($date_end, 'H:i'),
          'hours'=> $hours
        );
      endforeach;


      // fix the associative array
      $jsonEvents = array();
      $c = 0;
      foreach($event_week as $w):
        $jsonEvents[$c++]=$w;
      endforeach;

      return $jsonEvents;
  }
}


// Colors
class Calendar_Colors {
  public $service;

  public function __construct(){
    $this->set_service();
  }

  public function set_service(){
    $calendar = new Calendar_Service();
    $this->service = $calendar->get_service();
  }

  public function get(){
    $colors = $this->service->colors->get();

    // Print available calendarListEntry colors.
    foreach ($colors->getCalendar() as $key => $color) {
      print "colorId : {$key}\n";
      print "  Background: {$color->getBackground()}\n";
      print "  Foreground: {$color->getForeground()}\n";
    }
    // Print available event colors.
    foreach ($colors->getEvent() as $key => $color) {
      print "colorId : {$key}\n";
      print "  Background: {$color->getBackground()}\n";
      print "  Foreground: {$color->getForeground()}\n";
    }
  }
}
?>