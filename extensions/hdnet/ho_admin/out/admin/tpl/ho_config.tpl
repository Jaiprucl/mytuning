[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="ho_TPL_SETTINGS" }]";
        top.setTitle();
    }
//-->
</script>
<h2 style="margin: 1px;">[{ oxmultilang ident="ho_TPL_SETTINGS" }]</h2>
<br>
[{if $oView->getTableConfig()}]
<form action="" method="post">
	<fieldset>
		<legend>Exporteinstellungen</legend>
		<table>
			<tr>
				<td>Pfad:</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
			<tr>
				<td>Dateiname:</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
			<tr>
				<td>Sonstige Einstellung</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Importeinstellungen</legend>
		<table>
			<tr>
				<td>Pfad:</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
			<tr>
				<td>Dateiname:</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
			<tr>
				<td>Sonstige Einstellung</td>
				<td style="width: 300px"><input style="width: 300px;" type="text" name=""></td>
			</tr>
		</table>
	</fieldset><br>
	<input style="padding: 3px 15px;" type="submit" name="saveSettings" value="Import">
</form>
[{else}]
<form action="" method="post">
	<input style="padding: 3px 15px;" type="submit" name="install" value="Import">
</form>
[{/if}]