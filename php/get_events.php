<?php
	include("calendar_service.php");
	include("request_client.php");
	include("events_job_format.php");
	
	$read = new Calendar_Read();
  	$job_format = new Events_Job_Format($read->events());
  	$job_format->get_jobs();

	header('Content-Type: application/json');
	echo $job_format;
?>