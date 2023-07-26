<?php
// Mit den folgenden Zeilen lassen sich
// alle Dateien in einem Verzeichnis auslesen
$handle = opendir ("config");
while ($datei = readdir ($handle)) {
 echo "$datei<br>";
}
closedir($handle);
?>