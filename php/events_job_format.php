<?php
// PHP Pattern - Creational / Factory Method
class Events_Job_Format {
  
  public $events;
  public $jobs_array = array();
  public $id;
  public $start;
  public $date_start;
  public $end;
  public $date_end;
  public $hours;
  
  // look to remove mdy
  public $mdy;
  public $day_number;
  public $day_name;
  public $month;
  public $year;

  public $summary;
  
  

  public function __construct($events){
    $this->events = $events;
  }

  public function get_events(){
    return $this->events;
  }

  public function get_jobs(){
    $this->_filter_events();
    return $this->jobs_array;
  }

  private function _filter_events(){
    $events = $this->get_events();
    $this->_sort_events($events);
    $this->_filter_job_array();
  }

  private function _sort_events($events){
    foreach ($events->getItems() as $key=> $event):
      $this->id = $event->getId();
      $this->start = $event->getStart()->dateTime;
      $this->date_start = date_create_from_format('Y-m-d\TH:i:sP',$this->start);
      $this->end = $event->getEnd()->dateTime;
      $this->date_end = date_create_from_format('Y-m-d\TH:i:sP',$this->end);
      $interval = $this->date_start->diff($this->date_end);
      $this->hours = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
      
      $this->mdy = date_format($this->date_start, 'm-d-Y');

      $this->day_number = date_format($this->date_start, 'd');
      $this->day_name = date_format($this->date_start, 'l');
      $this->month = date_format($this->date_start, 'F');
      $this->year = date_format($this->date_start, 'Y');
      
      // check the summary for the job number
      $this->summary = $event->getSummary();
      $this->_filter_job_number();
    endforeach;
  }

  private function _filter_job_number() {
    if(ctype_digit(substr($this->summary,0,3))):
      $job_number = substr($this->summary,0,3);
      if(ctype_digit(substr($this->summary,0,5))):
        $job_number = substr($this->summary,0,5);
      endif;
      $this->_create_jobs($job_number);
    else:
      $this->_create_jobs("370");
    endif;
  }

  private function _create_jobs($number){
    //$this->jobs_array['events'][$this->mdy]['mdy'] = $this->mdy;

    $this->jobs_array['events'][$this->mdy]['day_number'] = $this->day_number;
    $this->jobs_array['events'][$this->mdy]['day_name'] = $this->day_name;
    $this->jobs_array['events'][$this->mdy]['month'] = $this->month;
    $this->jobs_array['events'][$this->mdy]['year'] = $this->year;

    $this->jobs_array['events'][$this->mdy]['jobs'][$number]['number'] = $number;
    $this->jobs_array['events'][$this->mdy]['jobs'][$number]['entries'][] = array(
      'id'=>$this->id,
      'summary'=> $this->summary,
      'start'=> date_format($this->date_start, 'H:i'),
      'end'=> date_format($this->date_end, 'H:i'),
      'hours'=> $this->hours
    );
  }

  private function _filter_job_array(){
    $a = 0;
    
    foreach($this->jobs_array['events'] as $key => $val):
      
      $array['events'][$a]['day_number'] = $val['day_number'];
      $array['events'][$a]['day_name'] = $val['day_name'];
      $array['events'][$a]['month'] = $val['month'];
      $array['events'][$a]['year'] = $val['year'];

      $b = 0;
      $jobs_count = count($val['jobs'])+1;
      $array['events'][$a]['jobs_count'] = $jobs_count;

      foreach($val['jobs'] as $k => $v):
 
          $sum = 0;
          foreach($v['entries'] as $w):
            $sum += $w['hours'];
          endforeach;
          
          $v['total'] = $sum;
          $array['events'][$a]['jobs'][$b++] = $v;
        
      endforeach;
      $a++;
    endforeach;

    $this->jobs_array = $array;
  }

  public function __toString(){
    return json_encode($this->jobs_array);
  }
}
?>