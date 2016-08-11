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

/* TIPI DI DOCUMENTO SELEZIONABILI PER LA RI/STAMPA:
   valori che si possono dare alla variabile "tipdoc" da passare tramite URL (metodo GET)
   1 => "D.d.T. di Vendita"
   2 => "Fattura Differita"
   3 => "Fattura Immediata Accompagnatoria"
   4 => "Fattura Immediata Semplice"
   5 => "Nota Credito a Cliente"
   6 => "Nota Debito a Cliente"
   7 => "Ricevuta"
*/
require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();

$msg = '';

function getLastDocument($tipo,$sezione,$anno)
{
  //recupero l'ultimo documento dello stesso tipo emesso nell'anno
  global $gTables;
  switch ($tipo) {
    case 1:  //ddt
        $where = "(tipdoc = 'DDT' OR tipdoc = 'FAD') AND YEAR(datemi) = $anno";
        break;
    case 2:  //fattura differita
        $where = "tipdoc = 'FAD' AND YEAR(datfat) = $anno";
        break;
    case 3:  //fattura immediata accompagnatoria
        $where = "tipdoc = 'FAI' AND YEAR(datfat) = $anno AND template = 'FatturaImmediata'";
        break;
    case 4: //fattura immediata semplice
        $where = "tipdoc = 'FAI' AND YEAR(datfat) = $anno AND template = 'FatturaSemplice'";
        break;
    case 5: //nota di credito
        $where = "tipdoc = 'FNC' AND YEAR(datfat) = $anno";
        break;
    case 6: //nota di debito
        $where = "tipdoc = 'FND' AND YEAR(datfat) = $anno";
        break;
    case 7: //ricevuta
        $where = "tipdoc = 'VRI' AND YEAR(datfat) = $anno";
        break;
  }
  $rs_lastdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'], $where." AND seziva = $sezione","datfat DESC, numfat DESC",0,1);
  $last = gaz_dbi_fetch_array($rs_lastdoc);
  if ($last) {
     if ($tipo == 1){
        $last['numero'] = $last['numdoc'];
        $last['protoc'] = 0;
        $last['data_fine'] = $last['datemi'];
     } else {
        $last['numero'] = $last['numfat'];
        $last['data_fine'] = $last['datfat'];
     }
  } else {
        $last['protoc'] = 1;
        $last['numero'] = 1;
        $last['template'] = '';
        $last['data_fine'] = date("Y-m-d");
  }
  return  array('protoc'=>intval($last['protoc']),'numero'=>intval($last['numero']),'template'=>$last['template'],'datfin'=>$last['data_fine']);
}

function checkDocumentExist($tipo,$sezione,$data_inizio,$data_fine,$protocollo_inizio=0,$protocollo_fine=999999999,$numero_inizio=0,$numero_fine=999999999,$cliente=0)
{
    //esiste almeno un documento nel periodo selezionato
    global $gTables;
    $date_name = 'datfat';
    $num_name = 'numfat';
    switch ($tipo) {
           case 1:  //ddt
                $date_name = 'datemi';
                $num_name = 'numdoc';
                $protocollo_inizio=0;
                $protocollo_fine=999999999;
                $where = "(tipdoc = 'DDT' OR tipdoc = 'FAD') ";
                break;
           case 2:  //fattura differita
                $where = "tipdoc = 'FAD'";
                break;
           case 3:  //fattura immediata accompagnatoria
                $where = "tipdoc = 'FAI' AND template = 'FatturaImmediata'";
                break;
           case 4: //fattura immediata semplice
                $where = "tipdoc = 'FAI' AND template <> 'FatturaImmediata'";
                break;
           case 5: //nota di credito
                $where = "tipdoc = 'FNC'";
                break;
           case 6: //nota di debito
                $where = "tipdoc = 'FND'";
                break;
           case 7: //ricevuta
                $where = "tipdoc = 'VRI'";
                break;
    }
    $where .= " AND seziva = $sezione
                AND $num_name BETWEEN $numero_inizio AND $numero_fine
                AND protoc BETWEEN $protocollo_inizio AND $protocollo_fine
                AND $date_name BETWEEN $data_inizio AND $data_fine";
    if ($cliente > 0){
       $where .= " AND codcli = $cliente";
    }
    $rs_existdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'], $where,"$date_name DESC, $num_name DESC",0,1);
    return gaz_dbi_fetch_array($rs_existdoc);
}

if (!isset($_POST['ritorno'])) { //al primo accesso allo script
   $msg = '';
   $form['ritorno'] = $_SERVER['HTTP_REFERER'];
   if (isset($_GET['seziva'])){
      $form['seziva'] = intval($_GET['seziva']);
   } else {
      $form['seziva'] = 1;
   }
   $form['codcli'] = '';
   $form['ragso1'] = '';
   if (isset($_GET['tipdoc'])){
      $form['tipdoc'] = intval($_GET['tipdoc']);
   } else {
      $form['tipdoc'] = 2; //fattura differita
   }
   $last = getLastDocument($form['tipdoc'],$form['seziva'],date("Y"));
   if (isset($_GET['datini'])) {
      $form['gioini'] = substr($_GET['datini'],6,2);
      $form['mesini'] = substr($_GET['datini'],4,2);
      $form['annini'] = substr($_GET['datini'],0,4);
   } else {
      $form['gioini'] = 1;
      $form['mesini'] = substr($last['datfin'],5,2);
      $form['annini'] = date("Y");
   }
   // controllo se un'altro script passa dei valori tramite URL per facilitare la scelta
   // ad esempio in fase di generazione e stampa fatture differite
   if (isset($_GET['proini'])) {
      $form['proini'] = intval($_GET['proini']);
   } else {
      $form['proini'] = 1;
   }
   if (isset($_GET['numini'])) {
      $form['numini'] = intval($_GET['numini']);
   } else {
      $form['numini'] = 1;
   }
   if (isset($_GET['datfin'])) {
      $form['giofin'] = substr($_GET['datfin'],6,2);
      $form['mesfin'] = substr($_GET['datfin'],4,2);
      $form['annfin'] = substr($_GET['datfin'],0,4);
   } else {
      $form['giofin'] = substr($last['datfin'],8,2);
      $form['mesfin'] = substr($last['datfin'],5,2);
      $form['annfin'] = substr($last['datfin'],0,4);
   }
   if (isset($_GET['profin'])) {
      $form['profin'] = intval($_GET['profin']);
   } else {
      $form['profin'] = $last['protoc'];
   }
   if (isset($_GET['numfin'])) {
      $form['numfin'] = intval($_GET['numfin']);
   } else {
      $form['numfin'] = $last['numero'];
   }
} else { // le richieste successive
   $form['ritorno'] = $_POST['ritorno'];
   $form['seziva'] = intval($_POST['seziva']);
   $form['codcli'] = intval($_POST['codcli']);
   $form['ragso1'] = substr($_POST['ragso1'],0,15);
   $form['tipdoc'] = intval($_POST['tipdoc']);
   $form['gioini'] = intval($_POST['gioini']);
   $form['mesini'] = intval($_POST['mesini']);
   $form['annini'] = intval($_POST['annini']);
   $form['giofin'] = intval($_POST['giofin']);
   $form['mesfin'] = intval($_POST['mesfin']);
   $form['annfin'] = intval($_POST['annfin']);
   $form['proini'] = intval($_POST['proini']);
   $form['numini'] = intval($_POST['numini']);
   $form['profin'] = intval($_POST['profin']);
   $form['numfin'] = intval($_POST['numfin']);
}


if (isset($_POST['Print'])) {
    //Mando in stampa le fatture generate solo se non ci sono errori
    if ($form['numini'] <= 0 ) {
        $msg .= "12+";
    }
    if ($form['numfin'] < $form['numini']) {
        $msg .= "13+";
    }
    if ($form['proini'] <= 0 ) {
        $msg .= "14+";
    }
    if ($form['profin'] < $form['proini']) {
        $msg .= "15+";
    }
    if (!checkdate( $form['mesini'], $form['gioini'], $form['annini'])) {
       $msg .= "16+";
    }
    if (!checkdate( $form['mesfin'], $form['giofin'], $form['annfin'])) {
       $msg .= "17+";
    }
    $utsini= mktime(0,0,0,$form['mesini'],$form['gioini'],$form['annini']);
    $utsfin= mktime(0,0,0,$form['mesfin'],$form['giofin'],$form['annfin']);
    if ($utsini > $utsfin) {
       $msg .="18+";
    }
    if (empty($msg)) {
       $datini = sprintf("%04d%02d%02d", $form['annini'], $form['mesini'], $form['gioini']);
       $datfin = sprintf("%04d%02d%02d", $form['annfin'], $form['mesfin'], $form['giofin']);
       if (!checkDocumentExist($form['tipdoc'],$form['seziva'],$datini,$datfin,$form['proini'],$form['profin'],$form['numini'],$form['numfin'])){
          $msg .="19+";
       }
    }
    if (empty($msg)) { //non ci sono errori
       unset ($form['gioini'],$form['giofin'],$form['mesini'],$form['mesfin'],$form['annini'],$form['annfin']);
       $form['datini'] = $datini;
       $form['datfin'] = $datfin;
       $_SESSION['print_request'] = $form;
       header("Location: invsta_docven.php");
       exit;
    }
}

if (isset($_POST['Return']))
    {
    header("Location:report_docven.php");
    exit;
}
require("../../library/include/header.php");
$script_transl=HeadMain();
echo "<form method=\"POST\">";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"".$form['ritorno']."\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">Ristampa documenti gi&agrave; emessi ".$script_transl[1];
echo "<select name=\"seziva\" class=\"FacetFormHeaderFont\">\n";
for ($counter = 1; $counter <= 3; $counter++) {
    $selected="";
    if ($counter == $form['seziva']) {
       $selected = " selected ";
    }
    echo "<option value=\"".$counter."\"".$selected.">".$counter."</option>\n";
}
echo "</select>\n";
echo "</div>";
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
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl[7]."</td>
     <td class=\"FacetDataTD\">\n";
echo "<select name=\"tipdoc\" class=\"FacetSelect\">\n";
for( $counter =  1; $counter <=  7; $counter++ ){
      $selected = '';
      if($form['tipdoc'] == $counter){
         $selected = "selected";
      }
      echo "\t\t <option value=\"".$counter."\" $selected >".$script_transl[0][$counter]."</option>\n";
}
echo "</select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[8]</td>";
echo "<td class=\"FacetDataTD\">";
$messaggio = '';
$tabula =" tabindex=\"1\" ";
$cerca = $form['ragso1'];
echo "<select name=\"codcli\" class=\"FacetSelect\">";
echo "\t\t <option value=\"\">$script_transl[10]</option>\n";
if (strlen($form['ragso1']) >= 2)
   {
   $mascon=$admin_aziend['mascli'].'000000';
   $result = gaz_dbi_dyn_query("codice,ragso1,citspe", $gTables['clfoco'].' LEFT JOIN '.$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id',"codice like '".$admin_aziend['mascli']."%' and codice > '$mascon'  and ragso1 like '".addslashes($cerca)."%'","ragso1 desc");
   $numclfoco = gaz_dbi_num_rows($result);
   if ($numclfoco > 0)
      {
      $tabula="";
      while ($a_row = gaz_dbi_fetch_array($result))
        {
        $selected = "";
        if($a_row["codice"] == $form['codcli'])
                            $selected = "selected";
        echo "\t\t <option value=\"".$a_row["codice"]."\" $selected >".$a_row["ragso1"]."&nbsp;".$a_row["citspe"]."</option>\n";
        }
    }
    else $messaggio = $script_transl[11];
} else {
  $messaggio = $script_transl[9];
}
echo "\t </select>\n";
echo "\t<input type=\"text\" name=\"ragso1\" ".$tabula." accesskey=\"e\" value=\"".$form['ragso1']."\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
echo $messaggio;
echo "\t <input type=\"image\" align=\"middle\" accesskey=\"c\" ".$tabula." name=\"clfoco\" src=\"../../library/images/cerbut.gif\">\n";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[2] $script_transl[5]</td>";
echo "<td class=\"FacetDataTD\">";
// select del giorno
echo "\t <select name=\"gioini\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 31; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['gioini'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mesini\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 12; $counter++ )
    {
    $selected = "";
    if($counter == $form['mesini'])
            $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"annini\" class=\"FacetSelect\">\n";
for( $counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++ )
    {
    $selected = "";
    if($counter == $form['annini'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }

echo "\t </select>\n";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[2] $script_transl[6]</td>";
echo "<td class=\"FacetDataTD\">";
// select del giorno
echo "\t <select name=\"giofin\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 31; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['giofin'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mesfin\" class=\"FacetSelect\">\n";
for( $counter = 1; $counter <= 12; $counter++ )
    {
    $selected = "";
    if($counter == $form['mesfin'])
            $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"annfin\" class=\"FacetSelect\">\n";
for( $counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++ )
    {
    $selected = "";
    if($counter == $form['annfin'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }
echo "\t </select>\n";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4] $script_transl[5]</td>";
echo "<td class=\"FacetDataTD\">";
echo "<input title=\"Numero del primo documento che si intende stampare\" type=\"text\" name=\"numini\" value=\"".
      $form["numini"]."\" maxlength=\"5\" size=\"5\" class=\"FacetInput\">";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4] $script_transl[6]</td>";
echo "<td class=\"FacetDataTD\">";
echo "<input title=\"Numero dell'ultimo documento che si intende stampare\" type=\"text\" name=\"numfin\" value=\"".
      $form["numfin"]."\" maxlength=\"5\" size=\"5\" class=\"FacetInput\">";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[3] $script_transl[5]</td>";
echo "<td class=\"FacetDataTD\">";
echo "<input title=\"Numero di protocollo della prima fattura che si intende stampare\" type=\"text\" name=\"proini\" value=\"".
      $form["proini"]."\" maxlength=\"5\" size=\"5\" class=\"FacetInput\">";
echo "</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[3] $script_transl[6]</td>";
echo "<td class=\"FacetDataTD\">";
echo "<input title=\"Numero di protocollo dell'ultima fattura che si intende stampare\" type=\"text\" name=\"profin\" value=\"".
      $form["profin"]."\" maxlength=\"5\" size=\"5\" class=\"FacetInput\">";
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