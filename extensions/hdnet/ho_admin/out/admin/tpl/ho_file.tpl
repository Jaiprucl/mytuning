[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" href="../modules/ho_admin/out/src/tools/treeview/jquery.treeview.css" />
	
<script src="../modules/ho_admin/out/src/tools/treeview/lib/jquery.js" type="text/javascript"></script>
<script src="../modules/ho_admin/out/src/tools/treeview/lib/jquery.cookie.js" type="text/javascript"></script>
<script src="../modules/ho_admin/out/src/tools/treeview/jquery.treeview.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="ho_TPL_SETTINGS" }]";
        top.setTitle();
    }
//-->
</script>
<script type="text/javascript">
$(document).ready(function(){
	$("#main").treeview();
});
</script>
<h2 style="margin: 1px;">[{ oxmultilang ident="ho_TPL_UPLOADER" }]</h2>
<br>
<h3>Datei hochladen</h3>
<form action="" method="post" enctype="multipart/form-data">
  <p>
    <input name="jFile" type="file" size="50" maxlength="100000"><br><br>
	Pfad:<br>
	<input style="padding: 3px; width: 500px;" type="text" name="uploadPath"><br><br>
	<input style="padding: 1px 15px;" type="submit" name="uploadFile" value="hochladen">
  </p>
</form><br>
<h3>Datei downloaden</h3>
[{ $oView->listFolderFiles('../modules/ho_admin/') }]

