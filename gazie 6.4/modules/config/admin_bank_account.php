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

require("../../library/include/check.inc.php");
$admin_aziend=checkAdmin();
$msg = '';

$anagrafica = new Anagrafica();

if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if (!isset($_POST['ritorno'])) {
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}

if ((isset($_GET['Update']) and  !isset($_GET['codice'])) or isset($_POST['Return'])) {
    header("Location: ".$_POST['ritorno']);
    exit;
}

if ((isset($_POST['Insert'])) or (isset($_POST['Update']))) {   //se non e' il primo accesso
    $parse_clfoco=gaz_dbi_parse_post('clfoco');
    $form = $parse_clfoco+gaz_dbi_parse_post('anagra');
    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
       //validazione IBAN
       $iban= new IBAN;
       $msg .= ((empty($form['iban']) || !$iban->checkIBAN($form['iban'])) ? "16+" : '' );
       $msg .= ((substr($form['iban'],0,2) != $form['country']) ? "20+" : '' );
       //fine validazione IBAN
       if ($toDo == 'insert') { // e' un inserimento, controllo se il codice esiste
          $rs_ctrl = $anagrafica->getPartner($admin_aziend['masban']*1000000+$form['codice']);
          if ($rs_ctrl){
             $msg .= "17+";
          }
       }
       if ($form['codice'] <= 0){  //codice sbagliato <1
             $msg .= "18+";
       }
       if (empty($form['ragso1']) && $form['banapp'] <= 0){  //descrizione vuota e senza banca appoggio
             $msg .= "19+";
       }
       if ($msg == "") {// nessun errore
          //formatto i campi per l'iserimento
          $form['codice']=$admin_aziend['masban']*1000000+$form['codice'];
          if (empty($form['ragso1'])){  //prendo la descrizione della banca appoggio
             $banapp = gaz_dbi_get_row($gTables['banapp'],'codice',$form['banapp']);
             $form['ragso1'] = $banapp['descri'];
             $form['citspe'] = $banapp['locali'];
             $form['prospe'] = $banapp['codpro'];
          }
          if ($toDo == 'update') { // e' una modifica
             $anagrafica->updatePartners($form['codice'],$form);
          } else { // e' un'inserimento
             $anagrafica->insertPartner($form);
          }
          header("Location: ".$_POST['ritorno']);
          exit;
       }
  }
} elseif ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $form = $anagrafica->getPartner($admin_aziend['masban']*1000000+intval($_GET['codice']));
    $form['codice'] = str_pad(intval($_GET['codice']),6,'0',STR_PAD_LEFT);
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    //ricerca del numero libero sul piano dei conti
    $result = gaz_dbi_dyn_query('codice', $gTables['clfoco'], "codice BETWEEN ".$admin_aziend['masban']."000001 AND ".$admin_aziend['masban']."999999",'codice ASC');
    $ctrl_val = $admin_aziend['masban']*1000000;
    $last_account_number = 1;
    while ($row = gaz_dbi_fetch_array($result)) {
          $last_account_number = $row['codice'] - $admin_aziend['masban']*1000000 + 1;
          if ($ctrl_val < $row['codice']-1) {
             $last_account_number = $row['codice'] - $admin_aziend['masban']*1000000 - 1;
          }
          $ctrl_val = $row['codice'];
    }
    //fine ricerca numeri liberi
    //controllo esistenza condizioni minime per l'inserimento di un CCB
    $mastro = gaz_dbi_get_row($gTables['clfoco'],'codice',$admin_aziend['masban']*1000000);
    if (!$mastro){
             $msg .= "14+";
    }
    if ($admin_aziend['masban'] < 100 ){
             $msg .= "15+";
    }
    //fine controllo condizioni minime per CCB
    $form['codice'] = str_pad($last_account_number,6,'0',STR_PAD_LEFT);
    $form['id_anagra']=0;
    $form['ragso1'] = '';
    $form['banapp'] = 0;
    $form['indspe'] = '';
    $form['capspe'] = '';
    $form['citspe'] = '';
    $form['prospe'] = '';
    $form['country'] = $admin_aziend['country'];
    $form['iban'] = '';
    $form['sia_code'] = '';
    $form['addbol'] = 'N';
    $form['sedleg'] = '';
    $form['telefo'] = '';
    $form['fax'] = '';
    $form['e_mail'] = '';
    $form['annota'] = '';
}
require("../../library/include/header.php");
$script_transl = HeadMain();
if ($toDo == 'update') {
   $title = ucwords($script_transl[$toDo].$script_transl[0])." n.".$form['codice'];
} else {
   $title = ucwords($script_transl[$toDo].$script_transl[0]);
}
print "<form method=\"POST\">\n";
$gForm = new configForm();
print "<input type=\"hidden\" name=\"".ucfirst($toDo)."\" value=\"\">\n";
print "<input type=\"hidden\" value=\"".$_POST['ritorno']."\" name=\"ritorno\">\n";
print "<input type=\"hidden\" value=\"".$form['id_anagra']."\" name=\"id_anagra\">\n";
print "<div align=\"center\" class=\"FacetFormHeaderFont\">$title</div>";
print "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
if (!empty($msg)) {
    $message = "";
    $rsmsg = array_slice( explode('+',chop($msg)),0,-1);
    foreach ($rsmsg as $value){
            $message .= $script_transl['error']."! -> ";
            $rsval = explode('-',chop($value));
            foreach ($rsval as $valmsg){
                    $message .= $script_transl[$valmsg]." ";
            }
            $message .= "<br />";
    }
    echo '<tr><td colspan="5" class="FacetDataTDred">'.$message."</td></tr>\n";
}
if ($toDo == 'update') {
   print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[1]</td><td class=\"FacetDataTD\">".$admin_aziend['masban']." <input type=\"hidden\" name=\"codice\" value=\"".$form['codice']."\" />".$form['codice']."</td></tr>\n";
} else {
   print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[1]*</td><td class=\"FacetDataTD\">".$admin_aziend['masban']." <input type=\"text\" name=\"codice\" value=\"".$form['codice']."\" maxlength=\"6\" size=\"6\" /></td></tr>\n";
}
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[2]*</td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"ragso1\" value=\"".$form['ragso1']."\" maxlength=\"50\" size=\"50\" /></td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[3]</td><td class=\"FacetDataTD\">";
$select_banapp = new selectbanapp("banapp");
$select_banapp -> addSelected($form["banapp"]);
$select_banapp -> output();
print "</td></tr>";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4]*</td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"indspe\" value=\"".$form['indspe']."\" maxlength=\"50\" size=\"50\" /></td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[5]*</td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"capspe\" value=\"".$form['capspe']."\" maxlength=\"5\" size=\"5\" /></td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[6]*</td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"citspe\" value=\"".$form['citspe']."\" maxlength=\"50\" size=\"43\" />
       <input type=\"text\" name=\"prospe\" value=\"".$form['prospe']."\" maxlength=\"2\" size=\"2\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[7]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"country\" class=\"FacetSelect\">";
$result = gaz_dbi_dyn_query("iso,name", $gTables['country'],1,"name ASC");
while ($a_row = gaz_dbi_fetch_array($result)) {
       $selected="";
       if($form['country'] == $a_row['iso']) {
            $selected = " selected ";
       }
       echo "<option value=\"".$a_row['iso']."\"".$selected.">".$a_row['iso']." - ".$a_row['name']."</option>";
}
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[8]* </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"iban\" value=\"".$form['iban']."\" maxlength=\"27\" size=\"27\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['sia_code']."* </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"sia_code\" value=\"".$form['sia_code']."\" maxlength=\"5\" size=\"5\" />
       </td></tr>\n";

echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['eof']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('addbol',$script_transl['eof_value'],$form['addbol'],'FacetSelect',0,'eof');
echo "\t </td>\n";
echo "</tr>\n";

print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[9] </td><td class=\"FacetDataTD\">
       <textarea type =\"text\" name=\"sedleg\" row=\"2\" cols=\"30\">".$form['sedleg']."</textarea>
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[10] </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"telefo\" value=\"".$form['telefo']."\" maxlength=\"50\" size=\"50\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[11] </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"fax\" value=\"".$form['fax']."\" maxlength=\"50\" size=\"50\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[12] </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"e_mail\" value=\"".$form['e_mail']."\" maxlength=\"50\" size=\"50\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[13] </td><td class=\"FacetDataTD\">
       <input type=\"text\" name=\"annota\" value=\"".$form['annota']."\" maxlength=\"50\" size=\"50\" />
       </td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\"><input type=\"reset\" name=\"Cancel\" value=\"".$script_transl['cancel']."\">\n";
print "</td><td class=\"FacetDataTD\" align=\"right\">\n";
print "<input type=\"submit\" name=\"Return\" value=\"".$script_transl['return']."\">\n";
if ($toDo == 'update') {
   print '<input type="submit" accesskey="m" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="'.strtoupper($script_transl['update']).'!"></td></tr><tr></tr>';
} else {
   print '<input type="submit" accesskey="i" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="'.strtoupper($script_transl['insert']).'!"></td></tr><tr></tr>';
}
print "</td></tr></table>\n";
?>
</form>
</body>
</html>