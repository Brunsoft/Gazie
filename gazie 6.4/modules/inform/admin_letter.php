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
$tipoLettera = array ("LET"=>'',"DIC"=>'',"SOL"=>'');
// il tipo documento dev'essere settato e del tipo giusto altrimenti torna indietro
if ((isset($_GET['Update']) and  !isset($_GET['id_let'])) or
   (isset($_GET['tipo']) and (!array_key_exists($_GET['tipo'],$tipoLettera)))) {
    header("Location: ".$form['ritorno']);
    exit;
}

if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if ((isset($_POST['Insert'])) or (isset($_POST['Update']))) {   //se non e' il primo accesso
    $form['ritorno'] = $_POST['ritorno'];
    $form['hidden_req'] = $_POST['hidden_req'];
    $form['id_let'] = intval($_POST['id_let']);
    $form['gioemi'] = intval($_POST['gioemi']);
    $form['mesemi'] = intval($_POST['mesemi']);
    $form['annemi'] = intval($_POST['annemi']);
    $form['numero'] = $_POST['numero'];
    $form['tipo'] = $_POST['tipo'];
    $form['clfoco'] = substr($_POST['clfoco'],0,13);
    $form['oggetto'] = $_POST['oggetto'];
    $form['c_a'] = $_POST['c_a'];
    $form['corpo'] = $_POST['corpo'];
    if (isset($_POST['signature'])) {
        $form['signature'] = 'checked';
    } else {
        $form['signature'] = '';
    }
    //--- variabili temporanee
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    if ($_POST['hidden_req']=='clfoco') {
        $anagrafica = new Anagrafica();
        if (preg_match("/^id_([0-9]+)$/",$form['clfoco'],$match)) {
           $partner = $anagrafica->getPartnerData($match[1],1);
        } else {
           $partner = $anagrafica->getPartner($form['clfoco']);
        }
      /*      $anagrafica = new Anagrafica();
            $partner = $anagrafica->getPartner($form['clfoco']);
            $form['cerca_partner'] = substr($partner['ragso1'],0,4);
            $form['clfoco'] = 0; */
        $form['hidden_req']='';
    }
   if (isset($_POST['ins'])) {   // Se viene inviata la richiesta di conferma totale ...
       $datemi = date("Ymd",mktime(0,0,0,$form['mesemi'],$form['gioemi'],$form['annemi']));
       if (!checkdate( $form['mesemi'], $form['gioemi'], $form['annemi'])) {
          $msg .= "10+";
       }
       if ($form['clfoco'] == 0) {
          $msg .= "11+";
       }
       if ($msg == "") {// nessun errore
          $form['write_date'] = $datemi;
          if (isset($_POST['signature'])) {
                $form['signature'] = 1;
          } else {
                $form['signature'] = 0;
          }
          if ($toDo == 'update') {  // modifica
             $codice = array('id_let',$form['id_let']);
             letterUpdate($codice,$form);
             header("Location: ".$form['ritorno']);
             exit;
          } else {                  // inserimento
             letterInsert($form);
             $_SESSION['print_request'] = gaz_dbi_last_id();
             header("Location: invsta_letter.php");
             exit;
          }
       }
   }
} elseif ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['hidden_req']='';
    $lettera = gaz_dbi_get_row($gTables['letter'],'id_let',intval($_GET['id_let']));
    if ($lettera['adminid'] != $_SESSION['Login']) { //non � l'utente che ha scritto la lettera
       header("Location: report_letter.php");
       exit;
    }
    $anagrafica = new Anagrafica();
    $partner = $anagrafica->getPartner($lettera['clfoco']);
    $form['search']['clfoco']=substr($partner['ragso1'],0,10);
    $form['id_let'] = $lettera['id_let'];
    $form['gioemi'] = substr($lettera['write_date'],8,2);
    $form['mesemi'] = substr($lettera['write_date'],5,2);
    $form['annemi'] = substr($lettera['write_date'],0,4);
    $form['numero'] = $lettera['numero'];
    $form['tipo'] = $lettera['tipo'];
    $form['clfoco'] = $lettera['clfoco'];
    $form['oggetto'] = $lettera['oggetto'];
    $form['c_a'] = $lettera['c_a'];
    $form['corpo'] = $lettera['corpo'];
    if ($lettera['signature'] == 1) {
        $form['signature'] = 'checked';
    } else {
        $form['signature'] = '';
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['hidden_req']='';
    $form['search']['clfoco']='';
    $form['id_let'] = "";
    $form['gioemi'] = date("d");
    $form['mesemi'] = date("m");
    $form['annemi'] = date("Y");
    $rs_ultima_lettera = gaz_dbi_dyn_query("*", $gTables['letter'], "YEAR(write_date) = ".date("Y"),'write_date DESC, numero DESC, id_let DESC',0,1);
    $ultima_lettera = gaz_dbi_fetch_array($rs_ultima_lettera);
    if ($ultima_lettera) {
       $form['numero'] = intval($ultima_lettera['numero']) + 1;
       $form['tipo'] = $ultima_lettera['tipo'];
    } else {
       $form['numero'] = 1;
       $form['tipo'] = 'LET';
    }
    $form['clfoco'] = 0;
    $form['oggetto'] = '';
    $form['c_a'] = '';
    $form['corpo'] = '';
    $form['signature'] = 'checked';
}

require("../../library/include/header.php");
$script_transl = HeadMain(0,array('tiny_mce/tiny_mce'));
echo "<script type=\"text/javascript\">
// Initialize TinyMCE with the new plugin and menu button
tinyMCE.init({
  mode : \"specific_textareas\",
  theme : \"advanced\",
  forced_root_block : false,
  force_br_newlines : true,
  force_p_newlines : false,
  elements : \"corpo\",
  plugins : \"table,advlink\",
  theme_advanced_buttons1 : \"mymenubutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,|,link,unlink,code,|,formatselect,forecolor,backcolor,|,tablecontrols\",
  theme_advanced_buttons2 : \"\",
  theme_advanced_buttons3 : \"\",
  theme_advanced_toolbar_location : \"external\",
  theme_advanced_toolbar_align : \"left\",
  theme_advanced_resizing : true,
  editor_selector  : \"mceClass1\",
});
</script>\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".ucfirst($script_transl[$toDo].$script_transl['title'].$script_transl[0][$form['tipo']])."</div>\n";
echo "<form method=\"POST\">";
echo "<input type=\"hidden\" name=\"".ucfirst($toDo)."\" value=\"\">\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" name=\"id_let\" value=\"".$form['id_let']."\">\n";
echo "<table class=\"Tlarge\">\n";
if (!empty($msg)) {
    echo "<tr><td colspan=\"6\" class=\"FacetDataTDred\">";
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
    echo $message."</td></tr>\n";
}
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[7]</td><td class=\"FacetDataTD\"><select name=\"tipo\" class=\"FacetSelect\">\n";
foreach ($tipoLettera as $key => $value) {
    $selected="";
    if($form["tipo"] == $key) {
        $selected = " selected ";
    }
    echo "<option value=\"".$key."\"".$selected.">".$script_transl[0][$key]."</option>";
}
echo "</select></td>";
echo " <td align=\"right\" class=\"FacetFieldCaptionTD\">$script_transl[3]</td><td class=\"FacetDataTD\"> <input type=\"text\" value=\"".$form['numero']."\" maxlength=\"20\" size=\"20\" name=\"numero\"></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[1]</td><td class=\"FacetDataTD\"> \n";
// select del giorno
echo "\t <select name=\"gioemi\" class=\"FacetSelect\" >\n";
for( $counter = 1; $counter <= 31; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['gioemi'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mesemi\" class=\"FacetSelect\" >\n";
for( $counter = 1; $counter <= 12; $counter++ )
    {
    $selected = "";
    if($counter == $form['mesemi'])
            $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"annemi\" class=\"FacetSelect\">\n";
for( $counter = $form['annemi']-10; $counter <= $form['annemi']+10; $counter++ )
    {
    $selected = "";
    if($counter == $form['annemi'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }
echo "\t </select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4] : </td><td colspan=\"3\" class=\"FacetDataTD\"><input type=\"text\" value=\"".$form['oggetto']."\" maxlength=\"60\" size=\"60\" name=\"oggetto\"></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[2] : </td><td class=\"FacetDataTD\">\n";
$select_cliente = new selectPartner('clfoco');
$select_cliente->selectDocPartner('clfoco',$form['clfoco'],$form['search']['clfoco'],'clfoco',$script_transl['mesg'],-1);
echo "</td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[5] : </td><td colspan=\"3\" class=\"FacetDataTD\"><input type=\"text\" value=\"".$form['c_a']."\" maxlength=\"60\" size=\"60\" name=\"c_a\"></td>\n";
echo "<td class=\"FacetFieldCaptionTD\"></td><td class=\"FacetDataTD\" ";
if (isset($partner['indspe'])){
   echo "title=\"fax: ".$partner['fax']."\">".$partner['indspe']."<br />".$partner['capspe']." ".$partner['citspe']." (".$partner['prospe'].")";
} else {
   echo ">";
}
echo "</td></tr>\n";
echo "<tr><td colspan=\"6\" class=\"FacetFieldCaptionTD\" align=\"center\">$script_transl[8]</td></tr>\n";
echo "<tr><td colspan=\"6\"><textarea id=\"corpo\" name=\"corpo\" class=\"mceClass1\" style=\"width:100%;height:200px;\">".$form["corpo"]."</textarea></td></tr>\n";
echo "<tr><td colspan=\"3\" class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[9]<input type=\"checkbox\" name=\"signature\" ".$form['signature']."></td>
          <td colspan=\"3\" class=\"FacetFieldCaptionTD\" align=\"right\"><input type=\"submit\" accesskey=\"i\" name=\"ins\" value=\"".$script_transl['submit']." !\" /></td>
          </tr>";
echo "</table>";
?>
</form>
</body>
</html>