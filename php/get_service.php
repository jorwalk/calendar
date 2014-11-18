<?php
include("calendar_service.php");
include("request_client.php");

$status = array();

if ($service->get_access_token()) 
{
	$status['status']['connected'] = "true";
	
	$read = new Calendar_Read();
	$calendar = $read->get();
	$status['status']['primary'] = array();
	$status['status']['primary']['etag'] = $calendar->etag;
	$status['status']['primary']['id'] = $calendar->id;
	$status['status']['primary']['timeZone'] = $calendar->timeZone;
 	
	$service->set_token();
} 
else 
{
    $status['status']['connected'] = "false";
    $status['status']['auth_url'] = $service->get_auth_url();
}



header('Content-Type: application/json');
echo json_encode($status);
?>