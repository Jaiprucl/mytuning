[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" href=" ../modules/hdnet/hd_admin/out/src/css/main.css">
<script src=" ../modules/hdnet/hd_admin/out/src/js/jquery-1.9.1.js"></script>
<script src=" ../modules/hdnet/hd_admin/out/src/js/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href=" ../modules/hdnet/hd_admin/out/src/css/normalize.css" />
<link rel="stylesheet" type="text/css" href=" ../modules/hdnet/hd_admin/out/src/css/demo.css" />
<link rel="stylesheet" type="text/css" href=" ../modules/hdnet/hd_admin/out/src/css/component.css" />
<script type="text/javascript">
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="HD_ORDER_EXPORT" }]";
        top.setTitle();
    }

	function setImportData(type){
		$.ajax({
			type: "POST",
			cache:false,
			url: "index.php?cl=hd_vimport&action=" + type,
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			dataType: "html",
			beforeSend: function(){
				$( "#" + type + 'result').hide();
				$("#" + type + "-loader").show();
				console.log(type + this.url);
			},
			complete: function(){
				$( "#" + type + 'result').show();
				$("#" + type + "-loader").hide();
				console.log("Complete");
			},
			success: function(data) {
				$( "#" + type + 'result').html(data);
				console.log("Success");
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
				$( "#" + type + 'result').html("Status: " + textStatus); 
				$( "#" + type + 'result').html("Error: " + errorThrown); 
				console.log("Error");
			}
		});    
		return false;
	}
</script>
<script type="text/javascript">
  $(function() {
    $('.show1').click(function() {
      $('.hidden1').slideToggle();
      $(this).text($(this).text() == 'zuklappen' ? 'Inhalt der CSV Datei anzeigen' : 'zuklappen');
    });
	$('.show2').click(function() {
      $('.hidden2').slideToggle();
      $(this).text($(this).text() == 'zuklappen' ? 'Inhalt der CSV Datei anzeigen' : 'zuklappen');
    });
	$('.show3').click(function() {
      $('.hidden3').slideToggle();
      $(this).text($(this).text() == 'zuklappen' ? 'Inhalt der CSV Datei anzeigen' : 'zuklappen');
    });
  });
</script>
<style type="text/css">
	.importbox {
		padding: 25px 0;
	}
	.button, .button:hover, .button:visited, .button:focus{
		text-decoration: none;
	}
	.button input[type=button] {
		padding: 3px 15px;
	}
	.resultbox{
		padding: 15px 5px;
		float: right;
		font-size: 16px;
		display: none;
	}
	.ajaxloader {
		float: right;
		display: none;
	}
	.message {
		color: green;
	}
	.alert{
		color: red;
	}
	.uploader-form {
	}
	.topbox{
		padding: .45rem 1.25rem;
		font-size: 16px;
	}
</style>
<h2>Import</h2>
<h3 style="margin: 1px;">[{ oxmultilang ident="HD_CSR_IMPORT_TITLE" }] [{$oView->getImporter("HD_IMPORT_CSR_ARTICLE_PATH")}]</h3>

<div class="importbox">
	<a id="articleimportbutton" class="button" href="#" onclick="setImportData('article'); return false"><input type="button" value="CSR Artikel aus CSV importieren" /></a>
	<img id="article-loader" class="ajaxloader" src=" ../modules/hdnet/hd_admin/out/src/icon/loader.svg"><div id="articleresult" class="resultbox"></div>
</div>

<div class="importbox">
	<a id="pictureimportbutton" class="button" href="#" onclick="setImportData('picture'); return false"><input type="button" value="CSR Bilder aus CSV importieren" /></a>
	<img id="picture-loader" class="ajaxloader" src=" ../modules/hdnet/hd_admin/out/src/icon/loader.svg"><div id="pictureresult" class="resultbox"></div>
</div>

<div class="importbox">
	<a id="stockimportbutton" class="button" href="#" onclick="setImportData('stock'); return false"><input type="button" value="CSR Bestand aus CSV importieren" /></a>
	<img id="stock-loader" class="ajaxloader" src=" ../modules/hdnet/hd_admin/out/src/icon/loader.svg"><div id="stockresult" class="resultbox"></div>
</div>

<br>

<h3 style="margin: 1px;">[{ oxmultilang ident="HD_ALL_IMPORT_TITLE" }]</h3>

<div class="importbox">
	<a id="shipimportbutton" class="button" href="#" onclick="setImportData('ship'); return false"><input type="button" value="Versand ID zuweisen" /></a>
	<img id="ship-loader" class="ajaxloader" src=" ../modules/hdnet/hd_admin/out/src/icon/loader.svg"><div id="shipresult" class="resultbox"></div>
</div>

<script src=" ../modules/hdnet/hd_admin/out/src/js/custom-file-input.js"></script>