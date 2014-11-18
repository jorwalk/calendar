<?php
	include("calendar_service.php");
	include("request_client.php");
	
	$read = new Calendar_Read();
  	$day_format = new Events_Date_Format($read->events());
  	$day = $day_format->events_by_day();

  	echo "<pre>";
  	print_r($day);

	//header('Content-Type: application/json');
	//echo $job_format;
?>