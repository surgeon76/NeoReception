<html>
	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title>Электронная регистратура NeoReception</title>
<meta name="description" content="NeoComplex"> 
<meta name="keywords" content="NeoComplex">
<meta name="author" content="Vyacheslav Subbotin"> 

<link rel="stylesheet" href="neoreception.css">
<link rel="stylesheet" href="jquery/jquery-ui.min.css">
<link rel="stylesheet" href="alertify/alertify.core.css" />
<link rel="stylesheet" href="alertify/alertify.default.css" />

<script type="text/javascript" src="jquery/jquery-1.12.3.min.js"></script>
<script type="text/javascript" src="jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="tools/moment.min.js"></script>
<script type="text/javascript" src="alertify/alertify.min.js"></script>

<script type="text/javascript" src="html5.js"></script>
<script type="text/javascript" src="doc.js"></script>
<script type="text/javascript" src="block.js"></script>
<script type="text/javascript" src="list.js"></script>
<script type="text/javascript" src="controls.js"></script>
<script type="text/javascript" src="combobox.js"></script>
<script type="text/javascript" src="transport.js"></script>
<script type="text/javascript" src="register.js"></script>
<script type="text/javascript" src="currentuser.js"></script>
<script type="text/javascript" src="layout.js"></script>
<script type="text/javascript" src="account.js"></script>
<script type="text/javascript" src="passport.js"></script>
<script type="text/javascript" src="reception.js"></script>
<script type="text/javascript" src="registry.js"></script>
<script type="text/javascript" src="patients.js"></script>
<script type="text/javascript" src="logrecord.js"></script>
<script type="text/javascript" src="log.js"></script>
<script type="text/javascript" src="auth.js"></script>
</head>

<body>

<?php
require_once 'auth.php';
require_once 'log.php';
require_once 'tools.php';
require_once 'dblayer.php';
require_once 'init.php';

if (isset($_SESSION['firstTime']))
{
	echo '<input type="hidden" id="firstTime" />';
	unset($_SESSION['firstTime']);
}
?>
	
<script type="text/javascript">
	Layout();
</script>

<div id="infoDialog" title="Информация" style="display: none;">
<p style="text-align: justify;"><i>Для понимания принципа работы электронной регистратуры и эффективного ее использования, настоятельно рекомендуется ознакомиться с настоящими <b>разъяснениями по применению.</b></i></p>

<hr>

<p style="text-align: justify;">Для записи на прием Вам необходимо иметь на руках актуальное направление формы <b>057/у</b>. Без данного направления <span style="color: red">запись невозможна</span>.</p>

<p style="text-align: justify;">С помощью электронной регистратуры можно записаться на приём в <b>поликлинику</b> и в <b>диагностические отделения</b>: на маммографию, рентгенологическое исследование, компьютерную томографию (КТ), ультразвуковое исследование (УЗИ). В том случае, когда исследование требует <b>дополнительной подготовки</b> (например, введения контраста), узнайте предварительно, что для этого необходимо; без соответствующей подготовки провести исследование <span style="color: red">не представляется возможным</span>.</p>

<p style="text-align: justify;">Заполните раздел <b>паспортных данных</b> максимально полно и правильно. Это позволит регистраторам быстро отыскать Вас в картотеке, отправлять Вам обратные уведомления по электронной почте и связываться с Вами по телефону в случае необходимости. Неполное или неточное заполнение является поводом к <span style="color: red">отказу в записи</span>.</p>

<p style="text-align: justify;">Раздел записи на прием носит <b>предварительный характер</b>. Это связано со спецификой работы онкодиспансера. Вы не записываетесь на точное время и к конкретному врачу, а указываете свои предпочтения. В свою очередь медицинский регистратор ответит Вам наиболее подходящим образом, учитывая все обстоятельства.</p>

<p style="text-align: justify;"><b>Статус</b> Вашей заявки будет изменяться в зависимости от ответа регистратора. Статус можно отслеживать, выполнив вход под своей учетной записью, а также в электронной почте, адрес которой Вы указываете в разделе паспортных данных. В отдельных случаях регистратору необходимо будет связаться с Вами по телефону, номер которого Вы также указываете в блоке паспортных данных.</p>

<p style="text-align: justify;">Хорошей практикой является подача заявки на прием на дату <b>более, чем сутки</b> (в идеале - несколько суток или недель) от текущей. Это связано с тем, что электронная очередь проверяется один раз в конце рабочего дня, и Ваша заявка на сегодняшнее число будет <span style="color: red">заведомо просрочена</span>.</p>

<p style="text-align: justify;">Если Вы считаете, что Ваш случай сложнее, чем возможности <b>электронной записи</b>, - всегда доступна запись по телефону или непосредственно на месте.</p>
</div>

<script type="text/javascript">
$(function()
{
	if (document.getElementById('firstTime'))
	{
		$("#infoDialog").dialog();
		$("#infoDialog").dialog("option", "width", 800);
	}
});
</script>

</body>

</html>