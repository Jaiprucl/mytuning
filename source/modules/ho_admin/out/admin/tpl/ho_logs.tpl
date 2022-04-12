[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" href="../modules/ho_admin/out/src/css/tabs.css">
<link rel="stylesheet" href="../modules/ho_admin/out/src/css/main.css">
<script src="../modules/ho_admin/out/src/js/jquery-1.9.1.js"></script>
<script src="../modules/ho_admin/out/src/js/jquery-ui.js"></script>
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="HO_ORDER_EXPORT" }]";
        top.setTitle();
    }
//-->
</script>
<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
</script>

<h2 style="margin: 1px;">[{ oxmultilang ident="HO_LOGS_TITLE" }]</h2>
<br>
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[{ oxmultilang ident="HO_LOG_TABS_ORDER" }]</a></li>
    <li><a href="#tabs-2">[{ oxmultilang ident="HO_LOG_TABS_ARTICLE" }]</a></li>
    <li><a href="#tabs-3">[{ oxmultilang ident="HO_LOG_TABS_DELETE" }]</a></li>
    <li><a href="#tabs-4">[{ oxmultilang ident="HO_LOG_TABS_IMAGE" }]</a></li>
    <li><a href="#tabs-5">[{ oxmultilang ident="HO_LOG_TABS_STOCK" }]</a></li>
    <li><a href="#tabs-6">[{ oxmultilang ident="HO_LOG_TABS_ERRORLOG" }]</a></li>
    <li><a href="#tabs-7">[{ oxmultilang ident="HO_LOG_TABS_CHANGELOG" }]</a></li>
  </ul>
  <div id="tabs-1">
    <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("order")}]</textarea>
  </div>
  <div id="tabs-2">
	  <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("article")}]</textarea>
  </div>
  <div id="tabs-3">
    <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("delete")}]</textarea>
  </div>
  <div id="tabs-4">
	  <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("picture")}]</textarea>
  </div>
  <div id="tabs-5">
	  <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("stock")}]</textarea>
  </div>
  <div id="tabs-6">
	  <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("error")}]</textarea>
  </div>
  <div id="tabs-7">
    <textarea cols="150" rows="30" style="padding: 5px;" disabled="disabled">[{$oView->getLogData("version")}]</textarea>
  </div>
</div>