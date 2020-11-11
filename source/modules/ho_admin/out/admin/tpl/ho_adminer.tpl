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
<h2 style="margin: 1px;">[{ oxmultilang ident="hoAdminer" }]</h2>
[{ include file="../modules/ho_admin/out/src/tools/adminer/adminer.php" }]
