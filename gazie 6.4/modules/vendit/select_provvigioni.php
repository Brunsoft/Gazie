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

$mastrofornitori = $admin_aziend['masfor']."000000";
$inifornitori=$admin_aziend['masfor'].'000001';
$finfornitori=$admin_aziend['masfor'].'999999';
$msg = '';

if (!isset($_POST['ritorno'])) { //al primo accesso allo script
   $msg = '';
   $form['ritorno'] = $_SERVER['HTTP_REFERER'];
   if (isset($_GET['id_agente'])) { //se mi viene richiesto un agente specifico...
      $form['id_agente'] = intval($_GET['id_agente']);
   } else {
      $form['id_agente'] = 0;
   }
   $form['cerca_agente'] = '';
   if (isset($_POST['datini'])) {
      $form['gi'] = substr($_POST['datini'],6,2);
      $form['mi'] = substr($_POST['datini'],4,2);
      $form['ai'] = substr($_POST['datini'],0,4);
   } else {
      $form['gi'] = 1;
      $form['mi'] = 1;
      $form['ai'] = date("Y");
   }
   if (isset($_POST['datfin'])) {
      $form['gf'] = substr($_POST['datfin'],6,2);
      $form['mf'] = substr($_POST['datfin'],4,2);
      $form['af'] = substr($_POST['datfin'],0,4);
   } else {
      $form['gf'] = date("d");
      $form['mf'] = date("m");
      $form['af'] = date("Y");
   }

} else { // le richieste successive
   $form['id_agente'] = intval($_POST['id_agente']);
   if (isset($_POST['cerca_agente'])){
      $form['cerca_agente'] = substr($_POST['cerca_agente'],0,15);
   }
   if (isset($_POST['newagente'])) {
            $agente = gaz_dbi_get_row($gTables['agenti']." LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['agenti'].".id_fornitore = ".$gTables['clfoco'].".codice", $gTables['agenti'].'.id_agente', intval($_POST['id_agente']));
            $form['cerca_agente'] = substr($agente['ragso1'],0,4);
            $form['id_agente'] = 0;
   }
   $form['ritorno'] = $_POST['ritorno'];
   $form['gi'] = intval($_POST['gi']);
   $form['mi'] = intval($_POST['mi']);
   $form['ai'] = intval($_POST['ai']);
   $form['gf'] = intval($_POST['gf']);
   $form['mf'] = intval($_POST['mf']);
   $form['af'] = intval($_POST['af']);
}


if (isset($_POST['Print'])) {
    if (!checkdate( $form['mi'], $form['gi'], $form['ai'])) {
       $msg .= "16+";
    }
    if (!checkdate( $form['mf'], $form['gf'], $form['af'])) {
       $msg .= "17+";
    }
    $utsini= mktime(0,0,0,$form['mi'],$form['gi'],$form['ai']);
    $utsfin= mktime(0,0,0,$form['mf'],$form['gf'],$form['af']);
    if ($utsini > $utsfin) {
       $msg .="18+";
    }
    if (empty($msg)) { //non ci sono errori
       $datini = sprintf("%04d%02d%02d", $form['ai'], $form['mi'], $form['gi']);
       $datfin = sprintf("%04d%02d%02d", $form['af'], $form['mf'], $form['gf']);
       $_SESSION['print_request'] = array('id_agente'=>$form['id_agente'],'di'=>$datini,'df'=>$datfin);
       header("Location: invsta_provvigioni.php");
       exit;
    }
}

if (isset($_POST['Return']))
    {
    header("Location:report_agenti.php");
    exit;
}
require("../../library/include/header.php");
$script_transl = HeadMain();
echo "<form method=\"POST\">";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"".$form['ritorno']."\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl[0]."</div>";
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">";
if (!empty($msg)) {
    $message = "";
    $rsmsg = array_slice( explode('+',chop($msg)),0,-1);
    foreach ($rsmsg as $value){
            $message .= $script_transl['error']."! -> ";
            $rsval = explode('-',chop($value));
            foreach ($rsval as $valmsg){
                    $message .= $script_transl[$valmsg]." ";
            }
            $message .= "<br>";
    }
    echo '<tr><td colspan="5" class="FacetDataTDred">'.$message.'</td></tr>';
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[1] : </td><td class=\"FacetDataTD\">\n";
$messaggio = "";
$ric_mastro = substr($form['id_agente'],0,3);
$table = $gTables['clfoco']." LEFT JOIN ".$gTables['agenti']." ON ".$gTables['agenti'].".id_fornitore = ".$gTables['clfoco'].".codice LEFT JOIN ".$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id';
if ($form['id_agente'] == 0) {
   $tabula =" tabindex=\"1\" ";
   if (strlen($form['cerca_agente']) >= 2) {
      $rs_agente = gaz_dbi_dyn_query("*", $table, $gTables['agenti'].".id_agente > 0 AND codice BETWEEN '$inifornitori' AND '$finfornitori' AND ragso1 LIKE '".addslashes($form['cerca_agente'])."%'","ragso1");
      $n_agenti = gaz_dbi_num_rows($rs_agente);
      if ($n_agenti > 0) {
         $tabula="";
         echo "\t<select name=\"id_agente\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
         echo "<option value=\"000000000\"> $script_transl[2] </option>\n";
         while ($row = gaz_dbi_fetch_array($rs_agente)) {
           $selected = "";
           if ($row["id_agente"] == $form['id_agente']) {
               $selected = "selected";
           }
           echo "\t\t <option value=\"".$row["id_agente"]."\" $selected >".$row["ragso1"]."&nbsp;".$row["citspe"]."</option>\n";
         }
         echo "\t </select>\n";
      } else {
      $messaggio = $script_transl[4];
      echo "\t<input type=\"hidden\" name=\"id_agente\" value=\"".$form['id_agente']."\">\n";
      }
   } else {
      $messaggio = $script_transl[3];
      echo "\t<input type=\"hidden\" name=\"id_agente\" value=\"".$form['id_agente']."\">\n";
   }
   echo "\t<input type=\"text\" name=\"cerca_agente\" accesskey=\"e\" value=\"".$form['cerca_agente']."\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
   echo $messaggio;
   echo "\t <input type=\"image\" align=\"middle\" accesskey=\"c\" name=\"search\" src=\"../../library/images/cerbut.gif\"></td>\n";
} else {
   $agente = gaz_dbi_get_row($gTables['agenti']." LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['agenti'].".id_fornitore = ".$gTables['clfoco'].".codice LEFT JOIN ".$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id', $gTables['agenti'].'.id_agente', intval($form['id_agente']));
   echo "<input type=\"submit\" value=\"".$agente['ragso1'].' '.$agente['ragso2']."\" name=\"newagente\" title=\" MODIFICA ! \">\n";
   echo "\t<input type=\"hidden\" name=\"id_agente\" value=\"".$form['id_agente']."\">\n";
}
echo "</td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[5]</td>";
echo "<td class=\"FacetDataTD\">";
// select del giorno
echo "\t <select name=\"gi\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 31; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['gi'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mi\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 12; $counter++ )
    {
    $selected = "";
    if($counter == $form['mi'])
            $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"ai\" class=\"FacetSelect\">\n";
for( $counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++ )
    {
    $selected = "";
    if($counter == $form['ai'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }

echo "\t </select>\n";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[6]</td>";
echo "<td class=\"FacetDataTD\">";
// select del giorno
echo "\t <select name=\"gf\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 31; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['gf'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mf\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 12; $counter++ )
    {
    $selected = "";
    if($counter == $form['mf'])
            $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"af\" class=\"FacetSelect\">\n";
for( $counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++ )
    {
    $selected = "";
    if($counter == $form['af'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }
echo "\t </select>\n";
echo "</td></tr>";
echo "<tr>\n
     <td class=\"FacetFieldCaptionTD\"><input type=\"submit\" name=\"Return\" value=\"".ucfirst($script_transl['return'])."\"></td>\n
     <td align=\"right\" class=\"FacetFooterTD\"><input type=\"submit\" name=\"Print\" value=\"".ucfirst($script_transl['print'])."\"></td>\n
     </tr>\n";
?>
</table>
</form>
</body>
</html>