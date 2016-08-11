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
$msg = "";

if (!isset($_GET['valdar'])) {  //al primo accesso
    $form['pagini'] = $admin_aziend["upggio"]+1;
    $form['stadef'] = false;
    $form['copert'] = false;
    $form['valdar'] = 0;
    $form['valave'] = 0;
    $form['date_ini_D'] = 1;
    $form['date_ini_M'] = 1;
    $form['date_ini_Y'] =  date("Y");
    $form['date_fin_D'] =  date("d");
    $form['date_fin_M'] =  date("m");
    $form['date_fin_Y'] =  date("Y");
} else {
    $form['pagini'] = intval($_GET['pagini']);
    if (isset($_GET['stadef'])){
       $form['stadef']=intval($_GET['stadef']);
    } else {
       $form['stadef']='';
    }
    if (isset($_GET['copert'])){
       $form['copert']=intval($_GET['copert']);
    } else {
       $form['copert']='';
    }
    $form['valdar'] = number_format($_GET['valdar'],2,'.','');
    $form['valave'] = number_format($_GET['valave'],2,'.','');
    $form['date_ini_D'] = intval($_GET['date_ini_D']);
    $form['date_ini_M'] = intval($_GET['date_ini_M']);
    $form['date_ini_Y'] = intval($_GET['date_ini_Y']);
    $form['date_fin_D'] = intval($_GET['date_fin_D']);
    $form['date_fin_M'] = intval($_GET['date_fin_M']);
    $form['date_fin_Y'] = intval($_GET['date_fin_Y']);
}
//controllo i campi
if (!checkdate( $form['date_ini_M'], $form['date_ini_D'], $form['date_ini_Y']))
    $msg .= "0+";
if (!checkdate( $form['date_fin_M'], $form['date_fin_D'], $form['date_fin_Y']))
    $msg .= "1+";
$utsini= mktime(0,0,0,$form['date_ini_M'],$form['date_ini_D'],$form['date_ini_Y']);
$utsfin= mktime(0,0,0,$form['date_fin_M'],$form['date_fin_D'],$form['date_fin_Y']);
$datainizio = date("Ymd",$utsini);
$datafine = date("Ymd",$utsfin);
if ($utsini > $utsfin) $msg .="2+";
if (isset($_GET['stampa']) && $msg == "") {
        if ($form['copert']==1) {
           $form['copert'] = "&copert";
        } else {
           $form['copert']="";
        }
        if ($form['stadef']==1) {
           $form['stadef'] = "&stadef";
        } else {
           $form['stadef']="";
        }
        //Mando in stampa i movimenti contabili selezionati
        $locazione = "Location: stampa_libgio.php?pagini=".$form['pagini']."&regini=".date("d-m-Y",$utsini)."&regfin=".date("d-m-Y",$utsfin)."&valdar=".$form['valdar']."&valave=".$form['valave'].$form['copert'];
        //print $locazione;
        header($locazione);
        exit;
}

if (isset($_GET['Return'])) {
        header("Location:docume_contab.php");
        exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain(0,array('calendarpopup/CalendarPopup'));
echo "<script type=\"text/javascript\">
var cal = new CalendarPopup();
var calName = '';
function setMultipleValues(y,m,d) {
     document.getElementById(calName+'_Y').value=y;
     document.getElementById(calName+'_M').selectedIndex=m*1-1;
     document.getElementById(calName+'_D').selectedIndex=d*1-1;
}
function setDate(name) {
  calName = name.toString();
  var year = document.getElementById(calName+'_Y').value.toString();
  var month = document.getElementById(calName+'_M').value.toString();
  var day = document.getElementById(calName+'_D').value.toString();
  var mdy = month+'/'+day+'/'+year;
  cal.setReturnFunction('setMultipleValues');
  cal.showCalendar('anchor', mdy);
}
</script>
";
echo "<form method=\"GET\" name=\"select\">\n";
$gForm = new contabForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="4" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['stadef']."</td><td class=\"FacetDataTD\">\n";
$gForm->selCheckbox('stadef',$form['stadef'],$script_transl['stadef_title']);
echo "</td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['pagini']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"pagini\" value=\"".$form['pagini']."\" maxlength=\"5\" size=\"5\" /></td>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['valdar']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"valdar\" value=\"".$form['valdar']."\" maxlength=\"15\" size=\"15\" /></td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['valave']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"valave\" value=\"".$form['valave']."\" maxlength=\"15\" size=\"15\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_ini']."</td><td colspan=\"3\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_ini',$form['date_ini_D'],$form['date_ini_M'],$form['date_ini_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_fin']."</td><td colspan=\"3\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_fin',$form['date_fin_D'],$form['date_fin_M'],$form['date_fin_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
$result = gaz_dbi_dyn_query ("darave,datreg, SUM(import) AS import, COUNT(*) AS nrow",$gTables['rigmoc']." LEFT JOIN ".$gTables['tesmov']." ON (".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes) ","datreg BETWEEN ".$datainizio." AND ".$datafine." GROUP BY darave");
$nr=0;
$dare=0;$avere=0;
while ($rs = gaz_dbi_fetch_array($result)){
      $nr+=$rs['nrow'];
      if ($rs['darave']== 'D'){
         $dare= $rs['import'];
      } else {
         $avere= $rs['import'];
      }
}
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['nrow']."</td><td class=\"FacetDataTD\" colspan=\"3\">".$nr." &nbsp;</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['tot_d']."</TD><TD class=\"FacetDataTD\">".gaz_format_number($dare)."</td><td class=\"FacetFieldCaptionTD\">".$script_transl['tot_a']."</TD><td class=\"FacetDataTD\">".gaz_format_number($avere)." &nbsp;</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\"> &nbsp;</td><td align=\"right\" class=\"FacetFooterTD\" colspan=4><input type=\"submit\" name=\"Return\" value=\"Indietro\"><input type=\"submit\" name=\"stampa\" value=\"".$script_transl['print']." !\" > ".$script_transl['cover']." <input type=\"checkbox\"  name=\"copert\" value=1></td></tr>";
?>
</table>
</form>
</body>
</html>