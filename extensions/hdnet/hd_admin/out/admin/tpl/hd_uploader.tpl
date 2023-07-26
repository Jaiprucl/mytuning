[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="hd_TPL_SETTINGS" }]";
        top.setTitle();
    }
//-->
</script>
<h2 style="margin: 1px;">[{ oxmultilang ident="hd_TPL_UPLOADER" }]</h2>
<br>
<h3>Datei hochladen</h3>
<form action="" method="post" enctype="multipart/form-data">
  <p>
    <input name="jFile" type="file" size="50" maxlength="100000" accept="text/*"><br><br>
	Pfad:<br>
	<input style="padding: 3px; width: 500px;" type="text" name="uploadPath"><br><br>
	<input style="padding: 1px 15px;" type="submit" name="uploadFile" value="hochladen">
  </p>
</form>