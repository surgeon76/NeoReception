<?php
require_once 'phpmailer/class.smtp.php';
require_once 'phpmailer/class.phpmailer.php';
require_once 'password_compat/password.php';
require_once 'init.php';

abstract class Status
{
	const NoAction = 0;
	const ReceptionPending = 1;
	const RevokeAcceptedPending = 2;
	const Recepted = 3;
	const Declined = 4;
}

abstract class Action
{
	const Recept = 0;
	const Revoke = 1;
	const Accept = 2;
	const Decline = 3;
}

abstract class StatusColor
{
	const NoAction = '#e1f3f9';
	const ReceptionPending = '#f9f29c';
	const RevokeAcceptedPending = '#f9c758';
	const Recepted = '#aaffaa';
	const Declined = '#ffbdbd';
}

abstract class Permissions
{
	const Patient = 0;
	const Admin = 1;
	const Registrator = 2;
	const Doctor = 3;
}

abstract class Access
{
	const All = 0;
	const Polyclinic = 1;
	const Rentgen = 2;
}

abstract class ReceptionType
{
	const Polyclinic = 0;
	const Mammography = 1;
	const Rentgen = 2;
	const CT = 3;
	const USI = 4;
}

class Reception
{
	public static function CurrentUser()
	{
		$ret = null;
		
		if (!isset($_SESSION['user']))
			return "";
		
		$db = Session::GetDB();
		$sql = sprintf('select ID, Login, Permissions, NOW() as CurTime from %s where Login=\'%s\'', DBTableNames::User, $db->PrepareString($_SESSION['user']));
		$ret = $db->Query($sql);
		
		return $ret;
	}
	
	public static function AccountLoad($id)
	{
		$sql = 'select ID, Login from ' . DBTableNames::User . ' where ID=' . $id;
		return Session::GetDB()->Query($sql);
	}
	
	public static function AccountSave($data)
	{
		$ret = false;
		$id = $data['ID'];
		$db = Session::GetDB();
		$sql = sprintf('update %s set Login=\'%s\', Password=\'%s\' where ID=%d', DBTableNames::User, $db->PrepareString($data['Login']),
			$db->PrepareString(password_hash($data['Password'], PASSWORD_BCRYPT)), $id);
		if (!is_null($db->Query($sql, DataBase::queryUpdate)))
			$ret = true;

		return $ret;
	}
	
	public static function AccountDelete($id)
	{
		$sql = 'delete from ' . DBTableNames::User . ' where ID=' . $id;
		return Session::GetDB()->Query($sql);
		//return true;
	}
	
	public static function PassportLoad($id)
	{
		$sql = 'select ID, FIO, Passport, SNILS, OMS, Birthdate, Address, Email, Phone, Status from ' . DBTableNames::User . ' where ID=' . $id;
		return Session::GetDB()->Query($sql);
	}
	
	public static function PassportSave($data)
	{
		$ret = false;
		$id = $data['ID'];
		$db = Session::GetDB();
		$sql = sprintf('update %s set FIO=\'%s\', Passport=\'%s\', SNILS=\'%s\', OMS=\'%s\', Birthdate=\'%s\', Address=\'%s\', Email=\'%s\', Phone=\'%s\' where ID=%d',
			DBTableNames::User,
			$db->PrepareString($data['FIO']), $db->PrepareString($data['Passport']), $db->PrepareString($data['SNILS']),
			$db->PrepareString($data['OMS']), $db->PrepareString($data['Birthdate']), $db->PrepareString($data['Address']),
			$db->PrepareString($data['Email']), $db->PrepareString($data['Phone']),
			$id);
		if (!is_null($db->Query($sql, DataBase::queryUpdate)))
			$ret = true;

		return $ret;
	}
	
	public static function ReceptionLoad($id)
	{
		$sql = 'select ID, PreferredType, Card, F057, MKB10, PreferredDate, PreferredInfo,'
			. 'MensisDate, Menopause, MensisDate2, Target, Status, DateMade, ResultProfile, ResultFIO, ResultRoom, ResultDate, ResultInfo from '
			. DBTableNames::User . ' where ID=' . $id;
		return Session::GetDB()->Query($sql);
	}
	
	public static function ReceptionSave($data)
	{
		$ret = false;
		$action = $data['receptaction'];
		$db = Session::GetDB();
		$id = $data['ID'];
		$sql = "";
		if ($action == Action::Recept)
		{
			//if ($data['PreferredType'] == ReceptionType::Polyclinic) //disable polyclinic due to requirements
			//	return false;
			
			$sql = sprintf('update %s set PreferredType=%d, Card=\'%s\', F057=\'%s\', MKB10=\'%s\', PreferredDate=\'%s\', PreferredInfo=\'%s\', '
				. 'MensisDate=%s, Menopause=%d, MensisDate2=%s, Target=\'%s\', ResultProfile=null, ResultFIO=null, ResultRoom=null, ResultDate=null, ResultInfo=null where ID=%d',
				DBTableNames::User,
				$data['PreferredType'], $db->PrepareString($data['Card']), $db->PrepareString($data['F057']),
				$db->PrepareString($data['MKB10']), $db->PrepareString($data['PreferredDate']), $db->PrepareString($data['PreferredInfo']),
				strlen($data['MensisDate']) > 0 ? "'" . $data['MensisDate'] . "'" : "null", $data['Menopause'],
				strlen($data['MensisDate2']) > 0 ? "'" . $data['MensisDate2'] . "'" : "null", $db->PrepareString($data['Target']), 
				$id);
		}
		else if ($action == Action::Revoke)
		{
			$sql = sprintf('update %s set PreferredInfo=\'%s\' where ID=%d', DBTableNames::User, $db->PrepareString($data['PreferredInfo']), $id);
		}
		else if ($action == Action::Accept)
		{
			$sql = sprintf('update %s set ResultProfile=\'%s\', ResultFIO=\'%s\', ResultRoom=\'%s\', ResultDate=\'%s\', ResultInfo=\'%s\' where ID=%d',
				DBTableNames::User,
				$db->PrepareString($data['ResultProfile']), $db->PrepareString($data['ResultFIO']), $db->PrepareString($data['ResultRoom']),
				$db->PrepareString($data['ResultDate']), $db->PrepareString($data['ResultInfo']),
				$id);
		}
		else if ($action == Action::Decline)
		{
			$sql = sprintf('update %s set ResultProfile=null, ResultFIO=null, ResultRoom=null, ResultDate=null, ResultInfo=\'%s\' where ID=%d',
				DBTableNames::User, $db->PrepareString($data['ResultInfo']), $id);

			//$sql = sprintf('update %s set ResultInfo=\'%s\' where ID=%d', DBTableNames::User, $db->PrepareString($data['ResultInfo']), $id);
		}
		if (!is_null($db->Query($sql, DataBase::queryUpdate)))
			$ret = true;

		if ($ret)
			return Reception::AlterStatus($data);
		
		return false;
	}
	
	public static function AlterStatus($data)
	{
		$id = $data['ID'];
		$action = $data['receptaction'];
		$db = Session::GetDB();
		$sql = 'select Status from ' . DBTableNames::User . ' where ID=' . $id;
		$curStatus = $db->GetVal($sql);
		$newStatus = $curStatus;
		$ret = false;
		if (is_null($curStatus) || $curStatus == Status::NoAction)
		{
			if ($action == Action::Recept) //patient
			{
				$newStatus = Status::ReceptionPending;
				$ret = true;
			}
		}
		else if ($curStatus == Status::ReceptionPending)
		{
			if ($action == Action::Revoke) //patient
			{
				$newStatus = Status::NoAction;
				$ret = true;
			}
			else if ($action == Action::Accept) //registrator
			{
				$newStatus = Status::Recepted;
				$ret = true;
			}
			else if ($action == Action::Decline) //registrator
			{
				$newStatus = Status::Declined;
				$ret = true;
			}
		}
		else if ($curStatus == Status::Recepted)
		{
			if ($action == Action::Revoke) //patient
			{
				$newStatus = Status::RevokeAcceptedPending;
				$ret = true;
			}
			else if ($action == Action::Decline) //registrator
			{
				$newStatus = Status::Declined;
				$ret = true;
			}
		}
		else if ($curStatus == Status::Declined)
		{
			if ($action == Action::Recept) //patient
			{
				$newStatus = Status::ReceptionPending;
				$ret = true;
			}
			else if ($action == Action::Accept) //registrator
			{
				$newStatus = Status::Recepted;
				$ret = true;
			}
		}
		else if ($curStatus == Status::RevokeAcceptedPending)
		{
			if ($action == Action::Accept) //patient
			{
				$newStatus = Status::Recepted;
				$ret = true;
			}
			else if ($action == Action::Decline) //registrator
			{
				$newStatus = Status::NoAction;
				$ret = true;
			}
		}
		
		if ($ret && $newStatus != $curStatus)
		{
			$sql = sprintf('update %s set Status=%d, DateMade=NOW() where ID=%d', DBTableNames::User, $newStatus, $id);
			if (!is_null($db->Query($sql, DataBase::queryUpdate)))
			{
				Reception::LogRecord($id);
				return true;
			}
		}
		
		return false;
	}
	
	public static function PatientsList($data)
	{
		$accessQuery = "";
		if ($_SESSION['access'] == Access::Polyclinic)
			$accessQuery = "and PreferredType=" . ReceptionType::Polyclinic;
		else if ($_SESSION['access'] == Access::Rentgen)
			$accessQuery = "and PreferredType<>" . ReceptionType::Polyclinic;
		
		$sql = sprintf('select ID, FIO, Status, DateMade, PreferredDate, ResultDate from %s where Status<>%d and ' .
			'(Status=%d or DateMade>=date_add(NOW(), INTERVAL -36 HOUR) or (Status=%d and ResultDate>=NOW()) or ' .
			'(Status=%d and (DateMade>=date_add(NOW(), INTERVAL -24 HOUR) or ResultDate>=date_add(NOW(), INTERVAL -24 HOUR)))) %s order by Status asc, DateMade desc',
			DBTableNames::User, Status::NoAction, Status::ReceptionPending, Status::Recepted, Status::RevokeAcceptedPending, $accessQuery);
		
		return Session::GetDB()->Query($sql);
	}
	
	public static function ExpiredResetStatus($id)
	{
		$sql = sprintf('update %s set Status=%d where (Status=%d or Status=%d) and (DateMade<date_add(NOW(), INTERVAL -24 HOUR) or DateMade is NULL) and
			(ResultDate<date_add(NOW(), INTERVAL -24 HOUR) or ResultDate is NULL) and ID=%d',
			DBTableNames::User, Status::NoAction, Status::Recepted, Status::RevokeAcceptedPending, $id);
		return Session::GetDB()->Query($sql, DataBase::queryUpdate);
	}
	
	private static function PrepareInfoMail($info)
	{
		$order = array("\r\n", "\n", "\r");
		$replace = '<br>';
		return str_replace($order, $replace, $info);
	}
	
	public static function ReceptionMail($id)
	{
		$db = Session::GetDB();
		$sql = 'select Email, FIO, Status, ResultProfile, ResultFIO, ResultRoom, ResultDate, ResultInfo, PreferredType from ' . DBTableNames::User . ' where ID=' . $id;
		$res = $db->Query($sql);
		if (is_null($res) || !$res || count($res) == 0)
			return null;
		
		$body = "Добрый день.<br><br>";
		$status = $res[0]['Status'];
		if ($status == Status::NoAction)
		{
			$body .= '<div style="background-color: ' . StatusColor::NoAction . '; display: inline-block; padding: 4px;">';
			$body .= "<b>Вы не записаны на прием.</b>";
			$body .= "</div>";
		}
		else if ($status == Status::ReceptionPending)
		{
			$body .= '<div style="background-color: ' . StatusColor::ReceptionPending . '; display: inline-block; padding: 4px;">';
			$body .= "<b>Ваша заявка принята к рассмотрению, ожидается ответ регистратора.</b>";
			$body .= "</div>";
		}
		else if ($status == Status::RevokeAcceptedPending)
		{
			$body .= '<div style="background-color: ' . StatusColor::RevokeAcceptedPending . '; display: inline-block; padding: 4px;">';
			$body .= "<b>Вы записаны на прием и отзываете заявку, что требует действий со стороны регистратора. Вы будете уведомлены об отмене записи.</b>";
			$body .= "</div>";
		}
		else if ($status == Status::Recepted)
		{
			$body .= '<div style="background-color: ' . StatusColor::Recepted . '; display: inline-block; padding: 4px;">';
			$body .= "<b>Вы записаны на прием.</b><br>";
			$body .= "</div><br><br>";
			$body .= 
"<table style=\"border-collapse: collapse;\">
<tr><td style=\"border: 1px solid gainsboro;\"><b>Дата: </b></td><td style=\"border: 1px solid gainsboro;\">" . $res[0]['ResultDate'] . "</td></tr>" .
($res[0]['PreferredType'] == ReceptionType::Polyclinic ?
("<tr><td style=\"border: 1px solid gainsboro;\"><b>Профиль: </b></td><td style=\"border: 1px solid gainsboro;\">" . htmlspecialchars($res[0]['ResultProfile']) . "</td></tr>
<tr><td style=\"border: 1px solid gainsboro;\"><b>Врач: </b></td><td style=\"border: 1px solid gainsboro;\">" . htmlspecialchars($res[0]['ResultFIO']) . "</td></tr>")
:
"") .
"<tr><td style=\"border: 1px solid gainsboro;\"><b>Кабинет: </b></td><td style=\"border: 1px solid gainsboro;\">" . htmlspecialchars($res[0]['ResultRoom']) . "</td></tr>
<tr><td style=\"border: 1px solid gainsboro;\"><b>Примечания: </b></td><td style=\"border: 1px solid gainsboro;\">" . Reception::PrepareInfoMail(htmlspecialchars($res[0]['ResultInfo'])) . "</td></tr>
</table>";
		}
		else if ($status == Status::Declined)
		{
			$body .= '<div style="background-color: ' . StatusColor::Declined . '; display: inline-block; padding: 4px;">';
			$body .= "<b>В записи отказано.</b><br>";
			$body .= "</div><br><br>";
			$body .= 
"<table style=\"border-collapse: collapse;\">
<tr><td style=\"border: 1px solid gainsboro;\"><b>Дополнительная информация: </b></td><td style=\"border: 1px solid gainsboro;\">" . Reception::PrepareInfoMail(htmlspecialchars($res[0]['ResultInfo'])) . "</td></tr>
</table>";
		}
		$body .= '<br><br>Просмотреть заявку можно выполнив вход в <a href="http://onkosimfer.ru/neoreception/default.php" target="_blank">электронную регистратуру</a>.';
		$body .= '<br><br>С уважением,<br> регистратура ГБУЗ РК "КРОКД имени В.М. Ефетова".';
		
		$mail = new PHPMailer;

		$mail->isSMTP();
		$mail->Host = 'mail.ipipe.ru';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply.registry@onkosimfer.ru';
		$mail->Password = Constants::MailPass;
		$mail->Port = 25;
		
		$mail->CharSet = 'utf-8';
		$mail->setFrom('noreply.registry@onkosimfer.ru', 'Электронная регистратура ГБУЗ РК "КРОКД имени В.М. Ефетова');
		$mail->addAddress($res[0]['Email'], $res[0]['FIO']);
		$mail->isHTML(true);
		$mail->Subject = 'Уведомление об изменении статуса Вашей электронной заявки на запись';
		$mail->Body = $body;

		if(!$mail->send())
			return null;
		
		return true;
	}
	
	public static function LogRecord($id)
	{
		$db = Session::GetDB();
		$sql = sprintf(
"INSERT INTO %s
	(`LogDate`, `UserID`, `Login`, `FIO`, `Passport`, `SNILS`, `OMS`, `Email`, `Phone`, `Gender`, `Birthdate`, `Address`,
	`PreferredType`, `Card`, `F057`, `MKB10`, `PreferredDate`, `PreferredInfo`, `MensisDate`, `Menopause`, `MensisDate2`, `Target`, 
	`Status`, `DateMade`, `ResultProfile`, `ResultFIO`, `ResultRoom`, `ResultDate`, `ResultInfo`,
	`ActionLogin`)
SELECT
    NOW(), `ID`, `Login`, `FIO`, `Passport`, `SNILS`, `OMS`, `Email`, `Phone`, `Gender`, `Birthdate`, `Address`,
	`PreferredType`, `Card`, `F057`, `MKB10`, `PreferredDate`, `PreferredInfo`, `MensisDate`, `Menopause`, `MensisDate2`, `Target`,
	`Status`, `DateMade`, `ResultProfile`, `ResultFIO`, `ResultRoom`, `ResultDate`, `ResultInfo`,
	'%s'
FROM %s
WHERE ID=%d", DBTableNames::Log, $db->PrepareString($_SESSION['user']), DBTableNames::User, $id);
		return $db->Query($sql);
	}
	
	private static function LogSortQuery($data)
	{
		$s = '';
		
		$sortCommand = isset($data['sortCommand']) ? $data['sortCommand'] : null;
		if (is_null($sortCommand))
			return '';

		if ($sortCommand == 'DateAsc')
			$s = " order by LogDate asc, FIO asc, Status asc ";
		else if ($sortCommand == 'FIOAsc')
			$s = " order by FIO asc, LogDate desc, Status asc ";
		else if ($sortCommand == 'StatusAsc')
			$s = " order by Status asc, FIO asc, LogDate desc ";
		else if ($sortCommand == 'DateDesc')
			$s = " order by LogDate desc, FIO asc, Status asc ";
		else if ($sortCommand == 'FIODesc')
			$s = " order by FIO desc, LogDate desc, Status asc ";
		else if ($sortCommand == 'StatusDesc')
			$s = " order by Status desc, FIO asc, LogDate desc ";
		
		return $s;
	}
	
	public static function Log($data)
	{
		$accessQuery = "";
		if ($_SESSION['access'] == Access::Polyclinic)
			$accessQuery = "where PreferredType=" . ReceptionType::Polyclinic;
		else if ($_SESSION['access'] == Access::Rentgen)
			$accessQuery = "where PreferredType<>" . ReceptionType::Polyclinic;
		
		$sql = sprintf('select ID, LogDate, FIO, Status from %s %s' . Reception::LogSortQuery($data), DBTableNames::Log, $accessQuery);
		return Session::GetDB()->Query($sql);
	}
	
	public static function LogLoad($id)
	{
		$sql = 'select * from ' . DBTableNames::Log . ' where ID=' . $id;
		return Session::GetDB()->Query($sql);
	}
	
	public static function GetAuth()
	{
		return isset($_SESSION['user']) ? true : false;
	}
	
	public static function CheckActivity()
	{
		$curTime = new DateTime();
		if (!isset($_SESSION['activity']))
			$_SESSION['activity'] = ['startTime' => $curTime->getTimestamp(), 'count' => 0];
		
		$activity = $_SESSION['activity'];
		$startTime = $activity['startTime'];
		$count = $activity['count'];
		$interval = $curTime->getTimestamp() - $startTime;
		
		if ($interval > 60)
		{
			$_SESSION['activity'] = ['startTime' => $curTime->getTimestamp(), 'count' => 0];
			return true;
		}

		$count++;
		$_SESSION['activity']['count'] = $count;
		if ($count <= 200)
			return true;
		
		return false;
	}
	
	public static function GetCurTime()
	{
		$curTime = new DateTime();
		return ['CurTime' => $curTime->format('Y-m-d\TH:i')];
	}
	
	public static function GetPermissions()
	{
		if (!isset($_SESSION['permissions']))
			$_SESSION['permissions'] = Permissions::Patient;
		return $_SESSION['permissions'];
	}
	
	public static function CheckPermissions()
	{
		if (!isset($_SESSION['userID']))
		{
			$db = Session::GetDB();
			$sql = sprintf("select ID, Permissions, Access from %s where Login='%s'", DBTableNames::User, $db->PrepareString($_SESSION['user']));
			$val = $db->Query($sql);
			if (!is_null($val))
			{
				$_SESSION['userID'] = $val[0]["ID"];
				$_SESSION['permissions'] = $val[0]["Permissions"];
				$_SESSION['access'] = $val[0]["Access"];
			}
		}
		if (!isset($_SESSION['userID']))
			return false;
		
		$id = $_SESSION['userID'];
		$permissions = Reception::GetPermissions();
		if ($permissions == Permissions::Patient)
		{
			if (isset($_GET["request"]))
			{
				if ($_GET["request"] != "object")
					return false;
				
				if (isset($_GET["id"]) && $_GET["id"] > 0)
				{
					if ($_GET["id"] !== $id)
						return false;
				}
			}
		}
		
		return true;
	}
	
	public static function ClearSession()
	{
		$_SESSION = [];
	}
}

?>