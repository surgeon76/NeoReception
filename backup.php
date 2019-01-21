<?php
require_once 'log.php';
require_once 'tools.php';
require_once 'dblayer.php';
require_once 'init.php';
require_once 'phpmailer/class.smtp.php';
require_once 'phpmailer/class.phpmailer.php';

//exec("mysqldump -hdb6.ipipe.ru -udimadns_db0 -pGSWEMsTLu9l3 dimadns_db0 " . DBTableNames::User . " " . DBTableNames::Log . " > backup/backup.sql");
$suffix = date('z');
exec("mysqldump -hdb6.ipipe.ru -udimadns_db2 -p" . Constants::DBPass . " dimadns_db2 > backup/backup$suffix.sql");

$mail = new PHPMailer;

$mail->isSMTP();
$mail->Host = 'mail.ipipe.ru';
$mail->SMTPAuth = true;
$mail->Username = 'noreply.registry@onkosimfer.ru';
$mail->Password = Constants::MailPass;
$mail->Port = 25;

$mail->CharSet = 'utf-8';
$mail->setFrom('noreply.registry@onkosimfer.ru', 'Электронная регистратура ГБУЗ РК "КРОКД имени В.М. Ефетова');
$mail->addAddress('surgeon76@gmail.com', 'Admin');
$mail->isHTML(true);
$mail->Subject = 'DB Backup';
$mail->Body = 'DB Backup';
$mail->AddAttachment("backup/backup$suffix.sql");

$mail->send();

?>
