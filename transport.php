<?php

require_once 'log.php';
require_once 'tools.php';
require_once 'dblayer.php';
require_once 'init.php';
require_once 'reception.php';

if (session_status() == PHP_SESSION_NONE)
	session_start();

$response = Reception::GetAuth();
if (!$response || !Reception::CheckPermissions())
{
	Reception::ClearSession();
	echo json_encode(["error" => "auth"]);
	die();
}

if (!Reception::CheckActivity())
{
	Reception::ClearSession();
	echo json_encode(["error" => "activity"]);
	die();
}

$request = $_GET["request"];
$response = [];
if ($request == 'list')
{
	if ($_GET["name"] == 'Registry')
		$response = Reception::PatientsList($_GET);
	else if ($_GET["name"] == 'LogRecord')
		$response = Reception::Log($_GET);
}
else if ($request == 'object')
{
	if ($_GET["name"] == 'Account')
	{
		if ($_GET["action"] == 'load')
			$response = Reception::AccountLoad($_GET["id"]);
		else if ($_GET["action"] == 'save')
			$response = Reception::AccountSave($_GET);
		else if ($_GET["action"] == 'delete')
			$response = Reception::AccountDelete($_GET["id"]);
		else if ($_GET["action"] == 'expire')
			$response = Reception::ExpiredResetStatus($_GET["id"]);
	}
	else if ($_GET["name"] == 'Passport')
	{
		if ($_GET["action"] == 'load')
			$response = Reception::PassportLoad($_GET["id"]);
		else if ($_GET["action"] == 'save')
			$response = Reception::PassportSave($_GET);
	}
	else if ($_GET["name"] == 'Reception')
	{
		if ($_GET["action"] == 'load')
			$response = Reception::ReceptionLoad($_GET["id"]);
		else if ($_GET["action"] == 'save')
			$response = Reception::ReceptionSave($_GET);
		else if ($_GET["action"] == 'mail')
			$response = Reception::ReceptionMail($_GET["id"]);
		else if ($_GET["action"] == 'time')
			$response = Reception::GetCurTime();
	}
	else if ($_GET["name"] == 'Status')
	{
		$response = Reception::AlterStatus($_GET);
	}
	else if ($_GET["name"] == 'CurrentUser')
	{
		$response = Reception::CurrentUser($_GET["id"]);
	}
	else if ($_GET["name"] == 'LogRecord')
	{
		$response = Reception::LogLoad($_GET["id"]);
	}
	else if ($_GET["name"] == 'Auth')
	{
		$response = true;
	}
}

if (!$response || is_null($response))
	echo json_encode(["error" => "dbQueryErr"]);
else
	echo json_encode($response);

?>

