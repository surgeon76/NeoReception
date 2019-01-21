<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Ёлектронна€ запись на прием");
$APPLICATION->SetPageProperty("keywords", "Ёлектронна€ запись на прием");
$APPLICATION->SetPageProperty("description", "Ёлектронна€ запись на прием в онкологический диспансер, г. —имферополь");
$APPLICATION->SetTitle("Ёлектронна€ запись на прием");
?>
<div class="col-lg-3 col-md-3 col-sm-4">
	 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"right",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "right",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "N"
	)
);?>
</div>
<div class="col-lg-9 col-md-9 col-sm-8">
<h1 class="hs_heading"><?$APPLICATION->ShowTitle(false);?></h1>

<script type="text/javascript">
function receptReady()
{
	var h = window.screen.availHeight - 150;
	if (h < 700)
		h = 700;
	document.getElementById("recept").height = h;
}
window.addEventListener("load", function()
{
	setTimeout(function()
	{
		var top = 350;
		window.scrollTo(0, top);
	},
	1);
});
</script>

<iframe id="recept" width="850px" height="700px" style="border: 1px solid gainsboro;" src="default.php" onload="receptReady();"></iframe>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>