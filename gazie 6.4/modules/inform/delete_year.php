<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
         (http://www.devincentiis.it)
           <http://gazie.sourceforge.net>
 --------------------------------------------------------------------------
    Questo programma e` free software;   e` lecito redistribuirlo  e/o
    modificarlo secondo i  termini della Licenza Pubblica Generica GNU
    come e` pubblicata dalla Free Software Foundation; o la versione 2
    della licenza o (a propria scelta) una versione successiva.

    Questo programma  e` distribuito nella speranza  che sia utile, ma
    SENZA   ALCUNA GARANZIA; senza  neppure  la  garanzia implicita di
    NEGOZIABILITA` o di  APPLICABILITA` PER UN  PARTICOLARE SCOPO.  Si
    veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.

    Ognuno dovrebbe avere   ricevuto una copia  della Licenza Pubblica
    Generica GNU insieme a   questo programma; in caso  contrario,  si
    scriva   alla   Free  Software Foundation,  Inc.,   59
    Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
 --------------------------------------------------------------------------
*/
require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();

require("../../library/include/header.php");
$script_transl=HeadMain();
echo '<div align="center" class="FacetFormHeaderFont">ELIMINAZIONI DATI</div>';
?>
<table class="Tlarge">
<form method="POST">
<?php

$message = '<tr><th class="FacetFieldCaptionTD">Numero ID</th><th class="FacetFieldCaptionTD">Descrizione </th><th class="FacetFieldCaptionTD">DARE </th><th class="FacetFieldCaptionTD">AVERE </th><th class="FacetFieldCaptionTD">SBILANCIO</th></tr>';
echo $message;
$result = gaz_dbi_dyn_query ($gTables['tesdoc'].".id_tes",$gTables['tesdoc'], "YEAR(datemi)<= 2010", 1);
while ($a_row = gaz_dbi_fetch_array($result)) {
      if ($message){
            $message=false;
      }
      gaz_dbi_del_row($gTables['rigdoc'], "id_tes", $a_row['id_tes']);
      gaz_dbi_del_row($gTables['tesdoc'], "id_tes", $a_row['id_tes']);
      echo "<tr><td class=\"FacetDataTD\" colspan=\"5\" align=\"center\">DOCUMENTO ID ".$a_row['id_tes']." CANCELLATO ! </td></tr>\n";
}

$result = gaz_dbi_dyn_query ($gTables['tesmov'].".id_tes",$gTables['tesmov'], "YEAR(datreg)<= 2010", 1);
while ($a_row = gaz_dbi_fetch_array($result)) {
      if ($message){
            $message=false;
      }
      gaz_dbi_del_row($gTables['rigmoc'], "id_tes", $a_row['id_tes']);
      gaz_dbi_del_row($gTables['tesmov'], "id_tes", $a_row['id_tes']);
      echo "<tr><td class=\"FacetDataTD\" colspan=\"5\" align=\"center\">MOVIMENTO ID ".$a_row['id_tes']." CANCELLATO ! </td></tr>\n";
}

$result = gaz_dbi_dyn_query ($gTables['effett'].".id_tes",$gTables['effett'], "YEAR(datemi)<= 2010", 1);
while ($a_row = gaz_dbi_fetch_array($result)) {
      if ($message){
            $message=false;
      }
      gaz_dbi_del_row($gTables['effett'], "id_tes", $a_row['id_tes']);
      echo "<tr><td class=\"FacetDataTD\" colspan=\"5\" align=\"center\">EFFETTO ID ".$a_row['id_tes']." CANCELLATO ! </td></tr>\n";
}

if ($message) {
   echo "<tr><td class=\"FacetFormHeaderFont\" align=\"center\" colspan=\"5\">NON CI SONO DATI DA CANCELLARE !</td></tr>\n";
}
?>
</table>
</form>
</body>
</html>