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
$msg = '';

$anagrafica = new Anagrafica();

if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if ((isset($_GET['Update']) and  !isset($_GET['id_contract']))) {
    header("Location: ".$form['ritorno']);
    exit;
}


if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if ((isset($_POST['Insert'])) or (isset($_POST['Update']))) {   //se non e' il primo accesso
    //qui si deve fare un parsing di quanto arriva dal browser...
    $form['id_contract'] = intval($_POST['id_contract']);
    $cliente = $anagrafica->getPartner(intval($_POST['id_customer']));
    $form['hidden_req'] = $_POST['hidden_req'];
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    $form['doc_type'] = strtoupper(substr($_POST['doc_type'],0,3));
    $form['id_customer'] = substr($_POST['id_customer'],0,13);
    $form['vat_section'] = intval($_POST['vat_section']);
    $form['doc_number'] = intval($_POST['doc_number']);
    $form['conclusion_date_Y'] = intval($_POST['conclusion_date_Y']);
    $form['conclusion_date_M'] = intval($_POST['conclusion_date_M']);
    $form['conclusion_date_D'] = intval($_POST['conclusion_date_D']);
    $form['start_date_Y'] = intval($_POST['start_date_Y']);
    $form['start_date_M'] = intval($_POST['start_date_M']);
    $form['start_date_D'] = intval($_POST['start_date_D']);
    $form['months_duration'] = intval($_POST['months_duration']);
    $form['initial_fee'] = floatval(preg_replace("/\,/",'.',$_POST['initial_fee']));
    $form['periodic_reassessment'] = intval($_POST['periodic_reassessment']);
    $form['bank'] = intval($_POST['bank']);
    $form['payment_method'] = intval($_POST['payment_method']);
    $form['periodicity'] = intval($_POST['periodicity']);
    $form['tacit_renewal'] = intval($_POST['tacit_renewal']);
    $form['current_fee'] = floatval(preg_replace("/\,/",'.',$_POST['current_fee']));
    $form['cod_revenue'] = intval($_POST['cod_revenue']);
    $form['vat_code'] = intval($_POST['vat_code']);
    $form['id_body_text'] = intval($_POST['id_body_text']);
    $form['body_text'] = $_POST['body_text'];
    $form['last_reassessment_Y'] = intval($_POST['last_reassessment_Y']);
    $form['last_reassessment_M'] = intval($_POST['last_reassessment_M']);
    $form['last_reassessment_D'] = intval($_POST['last_reassessment_D']);
    $form['id_agente'] = intval($_POST['id_agente']);
    $form['provvigione'] = floatval(preg_replace("/\,/",'.',$_POST['provvigione']));

    // inizio rigo di input
    $form['in_status'] = $_POST['in_status'];
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_quanti'] = gaz_format_quantity($_POST['in_quanti'],0,$admin_aziend['decimal_quantity']);
    $form['in_price'] = $_POST['in_price'];
    $form['in_discount'] = $_POST['in_discount'];
    $form['in_vat_code'] = $_POST['in_vat_code'];
    $form['in_cod_revenue'] = $_POST['in_cod_revenue'];
    // fine rigo input
    $form['rows'] = array();
    $next_row = 0;
    if (isset($_POST['rows'])) {
       foreach ($_POST['rows'] as $next_row => $value) {
            $form['rows'][$next_row]['status'] = substr($value['status'],0,30);
            $form['rows'][$next_row]['descri'] = substr($value['descri'],0,100);
            $form['rows'][$next_row]['unimis'] = substr($value['unimis'],0,3);
            $form['rows'][$next_row]['price'] = number_format(preg_replace("/\,/",'.',$value['price']),$admin_aziend['decimal_price'],'.','');
            $form['rows'][$next_row]['discount'] = floatval(preg_replace("/\,/",'.',$value['discount']));
            $form['rows'][$next_row]['quanti'] = gaz_format_quantity($value['quanti'],0,$admin_aziend['decimal_quantity']);
            $form['rows'][$next_row]['vat_code'] = intval($value['vat_code']);
            $form['rows'][$next_row]['cod_revenue'] = intval($value['cod_revenue']);
            $next_row++;
       }
    }
    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
       $form['conclusion_date'] = $form['conclusion_date_Y']."-".$form['conclusion_date_M']."-".$form['conclusion_date_D'];
       $utsconcl = mktime(0,0,0,$form['conclusion_date_M'],$form['conclusion_date_D'],$form['conclusion_date_Y']);
       $form['start_date'] = $form['start_date_Y']."-".$form['start_date_M']."-".$form['start_date_D'];
       $utsstart = mktime(0,0,0,$form['start_date_M'],$form['start_date_D'],$form['start_date_Y']);
       $form['last_reassessment'] = $form['last_reassessment_Y']."-".$form['last_reassessment_M']."-".$form['last_reassessment_D'];
       $utsreass = mktime(0,0,0,$form['last_reassessment_M'],$form['last_reassessment_D'],$form['last_reassessment_Y']);
       if (!checkdate($form['conclusion_date_M'],$form['conclusion_date_D'],$form['conclusion_date_Y'])) {
          $msg .= "0+";
       }
       if (!checkdate($form['start_date_M'],$form['start_date_D'],$form['start_date_Y'])) {
          $msg .= "1+";
       }
       if (!checkdate($form['last_reassessment_M'],$form['last_reassessment_D'],$form['last_reassessment_Y'])) {
          $msg .= "2+";
       }
       if ($utsconcl>$utsstart) {
          $msg .= "3+";
       }
       if ($utsstart>$utsreass) {
          $msg .= "4+";
       }
       if (empty($form["id_customer"])) {
          $msg .= "5+";
       }
       if (empty ($form["payment_method"])) {
          $msg .= "6+";
       }
       if (empty ($form["body_text"])) {
          $msg .= "9+";
       }
       if ($form["current_fee"] <= 0) {
          $msg .= "10+";
       }
       //controllo che i rows non abbiano descrizioni e unita' di misura vuote in presenza di quantita diverse da 0
       foreach ($form['rows'] as $i => $value) {
            if (empty($value['descri']) && $value['quanti']>0) {
                $msg .= "7+";
            }
            if (empty($value['unimis']) && $value['quanti']>0) {
                $msg .= "8+";
            }
       }
       if ($msg == "") { // nessun errore
          if (preg_match("/^id_([0-9]+)$/",$form['id_customer'],$match)) {
             $new_clfoco = $anagrafica->getPartnerData($match[1],1);
             $form['id_customer']=$anagrafica->anagra_to_clfoco($new_clfoco,$admin_aziend['mascli']);
          }
          if ($toDo == 'update') { // e' una modifica
             $old_rows = gaz_dbi_dyn_query("*", $gTables['contract_row'], "id_contract = ".$form['id_contract'],"id_contract");
             $i=0;
             $count = count($form['rows'])-1;
             while ($val_old_row = gaz_dbi_fetch_array($old_rows)) {
                   if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                      $form['rows'][$i]['id_contract'] = $form['id_contract'];
                      contractRowUpdate($form['rows'][$i],array('id_row',$val_old_row['id_row']));
                   } else { //altrimenti lo elimino
                      gaz_dbi_del_row($gTables['contract_row'], 'id_row', $val_old_row['id_row']);
                   }
                   $i++;
             }
             //qualora i nuovi rows fossero di più dei vecchi inserisco l'eccedenza
             for ($i = $i; $i <= $count; $i++) {
                $form['rows'][$i]['id_contract'] = $form['id_contract'];
                contractRowUpdate($form['rows'][$i]);
             }
             bodytextUpdate(array('id_body',$form['id_body_text']),array('table_name_ref'=>'contract','id_ref'=>$form['id_contract'],'body_text'=>$form['body_text'],'lang_id'=>$admin_aziend['id_language']));
             contractUpdate($form, array('id_contract',$form['id_contract']));
             header("Location: ".$form['ritorno']);
             exit;
          } else { // e' un'inserimento
            contractUpdate($form);
            //recupero l'id assegnato dall'inserimento
            $ultimo_id = gaz_dbi_last_id();
            bodytextInsert(array('table_name_ref'=>'contract','id_ref'=>$ultimo_id,'body_text'=>$form['body_text'],'lang_id'=>$admin_aziend['id_language']));
            gaz_dbi_put_row($gTables['contract'], 'id_contract', $ultimo_id, 'id_body_text', gaz_dbi_last_id());
            //inserisco i rows
            foreach ($form['rows'] as $i=>$value) {
                  $value['id_contract'] = $ultimo_id;
                  contractRowUpdate($value);
            }
            $_SESSION['print_request']=$ultimo_id;
            header("Location: invsta_contract.php");
            exit;
          }
    }
  }
  // Se viene inviata la richiesta di conferma cliente
  if ($_POST['hidden_req']=='id_customer') {
    if (preg_match("/^id_([0-9]+)$/",$form['id_customer'],$match)) {
        $cliente = $anagrafica->getPartnerData($match[1],1);
    } else {
        $cliente = $anagrafica->getPartner($form['id_customer']);
    }
    $form['payment_method']=$cliente['codpag'];
    $form['bank']=$cliente['banapp'];
    $form['id_agente']=$cliente['id_agente'];
    $form['in_vat_code']=$cliente['aliiva'];
    $provvigione = new Agenti;
    $form['provvigione']=$provvigione->getPercent($form['id_agente']);
    $form['hidden_req']='';
  }

  // Se viene modificato l'agente ricarico la provvigione
  if ($_POST['hidden_req'] == 'AGENTE') {
     if ($form['id_agente'] > 0) {
         $provvigione = new Agenti;
         $form['provvigione']=$provvigione->getPercent($form['id_agente']);
    } else {
         $form['provvigione']=0.00;
    }
    $form['hidden_req']='';
  }

  // Se viene inviata la richiesta di conferma rigo
  if (isset($_POST['in_submit_x'])) {
    if (substr($form['in_status'],0,6) == "UPDROW"){ //se è un rigo da modificare
         $old_key = intval(substr($form['in_status'],6));
         $form['rows'][$old_key]['status'] = "UPDATE";
         $form['rows'][$old_key]['descri'] = $form['in_descri'];
         $form['rows'][$old_key]['unimis'] = $form['in_unimis'];
         $form['rows'][$old_key]['quanti'] = $form['in_quanti'];
         $form['rows'][$old_key]['codart'] = $form['in_codart'];
         $form['rows'][$old_key]['cod_revenue'] = $form['in_cod_revenue'];
         $form['rows'][$old_key]['provvigione'] = $form['in_provvigione'];
         $form['rows'][$old_key]['price'] = number_format($form['in_price'],$admin_aziend['decimal_price'],'.','');
         $form['rows'][$old_key]['discount'] = $form['in_discount'];
         $form['rows'][$old_key]['vat_code'] = $form['in_vat_code'];
         $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_vat_code']);
         if ($form['in_type_row'] == 0 ) {  //rigo normale
         } else {   // rigo di testo
         }
         ksort($form['rows']);
    } else { //se è un rigo da inserire
         $form['rows'][$next_row]['status'] = 'INSERT';
         $form['rows'][$next_row]['descri'] = $form['in_descri'];
         $form['rows'][$next_row]['unimis'] = $form['in_unimis'];
         $form['rows'][$next_row]['price'] = number_format($form['in_price'],$admin_aziend['decimal_price'],'.','');
         $form['rows'][$next_row]['cod_revenue'] = $form['in_cod_revenue'];
         $form['rows'][$next_row]['quanti'] = $form['in_quanti'];
         $form['rows'][$next_row]['discount'] = $form['in_discount'];
         $form['rows'][$next_row]['vat_code'] =  $form['in_vat_code'];
         $form['rows'][$next_row]['cod_revenue'] = $form['in_cod_revenue'];
    }
     // reinizializzo rigo di input tranne che tipo rigo, aliquota iva e conto ricavo
     $form['in_descri'] = "";
     $form['in_unimis'] = "";
     $form['in_price'] = 0;
     $form['in_discount'] = 0;
     $form['in_quanti'] = 0;
     // fine reinizializzo rigo input
     $next_row++;
  }

  // Se viene inviata la richiesta elimina il rigo corrispondente
  if (isset($_POST['del'])) {
    $delri= key($_POST['del']);
    array_splice($form['rows'],$delri,1);
    $next_row--;
  }

} elseif ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $contract = gaz_dbi_get_row($gTables['contract'],"id_contract",intval($_GET['id_contract']));
    $cliente = $anagrafica->getPartner($contract['id_customer']);
    $form['hidden_req'] = '';
    $form['id_contract'] = $contract['id_contract'];
    $form['doc_type'] = $contract['doc_type'];
    $form['id_customer'] = $contract['id_customer'];
    $form['search']['id_customer']=substr($cliente['ragso1'],0,10);
    $form['vat_section'] = $contract['vat_section'];
    $form['doc_number'] = $contract['doc_number'];
    $form['conclusion_date_Y'] = substr($contract['conclusion_date'],0,4);
    $form['conclusion_date_M'] = substr($contract['conclusion_date'],5,2);
    $form['conclusion_date_D'] = substr($contract['conclusion_date'],8,2);
    $form['start_date_Y'] = substr($contract['start_date'],0,4);
    $form['start_date_M'] = substr($contract['start_date'],5,2);
    $form['start_date_D'] = substr($contract['start_date'],8,2);
    $form['months_duration'] = $contract['months_duration'];
    $form['initial_fee'] = $contract['initial_fee'];
    $form['periodic_reassessment'] = $contract['periodic_reassessment'];
    $form['bank'] = $contract['bank'];
    $form['payment_method'] = $contract['payment_method'];
    $form['tacit_renewal'] = $contract['tacit_renewal'];
    $form['current_fee'] = $contract['current_fee'];
    $form['vat_code'] = $contract['vat_code'];
    $form['cod_revenue'] = $contract['cod_revenue'];
    $form['id_body_text'] = $contract['id_body_text'];
    $bodytext = gaz_dbi_get_row($gTables['body_text'],"id_body",$contract['id_body_text']);
    $form['body_text'] = $bodytext['body_text'];
    $form['last_reassessment_Y'] = substr($contract['last_reassessment'],0,4);
    $form['last_reassessment_M'] = substr($contract['last_reassessment'],5,2);
    $form['last_reassessment_D'] = substr($contract['last_reassessment'],8,2);
    $form['periodicity'] = $contract['periodicity'];
    $form['provvigione'] = $contract['provvigione'];
    $form['id_agente'] = $contract['id_agente'];

    // inizio rigo di input
    $form['in_status'] = "INSERT";
    $form['in_descri'] = "";
    $form['in_unimis'] = '';
    $form['in_quanti'] = 0;
    $form['in_price'] = 0;
    $form['in_discount'] = 0;
    $form['in_vat_code'] = $admin_aziend['preeminent_vat'];
    $form['in_cod_revenue'] = $admin_aziend['impven'];
    // fine rigo input

    $form['rows'] = array();
    $next_row = 0;
    $rs_row = gaz_dbi_dyn_query("*", $gTables['contract_row'], "id_contract = ".intval($_GET['id_contract']),"id_row ASC");
    while ($row = gaz_dbi_fetch_array($rs_row)) {
           $form['rows'][$next_row]['descri'] = $row['descri'];
           $form['rows'][$next_row]['unimis'] = $row['unimis'];
           $form['rows'][$next_row]['price'] = number_format($row['price'],$admin_aziend['decimal_price'],'.','');
           $form['rows'][$next_row]['discount'] = $row['discount'];
           $form['rows'][$next_row]['quanti'] = gaz_format_quantity($row['quanti'],0,$admin_aziend['decimal_quantity']);
           $form['rows'][$next_row]['vat_code'] = $row['vat_code'];
           $form['rows'][$next_row]['cod_revenue'] = $row['cod_revenue'];
           $form['rows'][$next_row]['status'] = $row['status'];
           $next_row++;
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['id_contract'] = '';
    $form['id_customer'] = '';
    if (empty($admin_aziend['pariva'])){
        $form['doc_type'] = 'VRI';
    } else {
        $form['doc_type'] = 'FAI';
    }
    $cliente['indspe'] = '';
    $form['search']['id_customer']='';
    if (!isset($_GET['vat_section'])) {
        $rs_last = gaz_dbi_dyn_query("vat_section,doc_type", $gTables['contract'], 1,"id_contract DESC",0,1);
        $last = gaz_dbi_fetch_array($rs_last);
                if ($last){
                   $form['vat_section'] = $last['vat_section'];
                } else {
                   $form['vat_section'] = 1;
                }
    } else {
        $form['vat_section'] = intval($_GET['vat_section']);
    }
    // trovo l'ultimo numero di contratto
    $rs_last = gaz_dbi_dyn_query("*", $gTables['contract'], "YEAR(conclusion_date)=".date("Y"),"doc_number DESC",0,1);
    $last = gaz_dbi_fetch_array($rs_last);
    $form['doc_number'] = $last['doc_number']+1;
    $form['conclusion_date_Y'] = date("Y");
    $form['conclusion_date_M'] = date("m");
    $form['conclusion_date_D'] = date("d");
    $form['start_date'] = date("d-m-Y");
    $form['start_date_Y'] = date("Y");
    $form['start_date_M'] = date("m");
    $form['start_date_D'] = date("d");
    $form['months_duration'] = 12;
    $form['initial_fee'] = 0.00;
    $form['periodic_reassessment'] = 1;
    $form['payment_method'] = 0;
    $form['bank'] = 0;
    $form['periodicity'] = 0;
    $form['tacit_renewal'] = 1;
    $form['current_fee'] = 0.00;
    $form['cod_revenue'] = $admin_aziend['impven'];
    $form['id_body_text'] = 0;
    $form['vat_code'] = $admin_aziend['preeminent_vat'];
    $form['body_text'] = '';
    $form['last_reassessment'] = '';
    $form['last_reassessment_Y'] = date("Y");
    $form['last_reassessment_M'] = date("m");
    $form['last_reassessment_D'] = date("d");
    $form['id_agente'] = 0;
    $form['provvigione'] = 0.00;
    $form['rows'] = array();
    $next_row = 0;
    $form['hidden_req'] = '';
    // inizio rigo di input
    $form['in_status'] = "INSERT";
    $form['in_descri'] = "";
    $form['in_type_row'] = 0;
    $form['in_unimis'] = "";
    $form['in_price'] = 0;
    $form['in_discount'] = 0;
    $form['in_quanti'] = 0;
    $form['in_vat_code'] = $admin_aziend['preeminent_vat'];
    $form['in_cod_revenue'] = $admin_aziend['impven'];
    // fine rigo input
}

require("../../library/include/header.php");
$script_transl = HeadMain(0,array('tiny_mce/tiny_mce',
                                  'boxover/boxover',
                                  'calendarpopup/CalendarPopup',
                                  'jquery/jquery-1.7.1.min',
                                  'jquery/ui/jquery.ui.core',
                                  'jquery/ui/jquery.ui.widget',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.autocomplete',
                                  'jquery/autocomplete_anagra'));
$title = ucfirst($script_transl['ins_this']);
if ($toDo=='update'){
  $title = ucfirst($script_transl['upd_this']);
}
echo "<script type=\"text/javascript\">
          // Initialize TinyMCE with the new plugin and menu button
          tinyMCE.init({
          mode : \"specific_textareas\",
          theme : \"advanced\",
          forced_root_block : false,
          force_br_newlines : true,
          force_p_newlines : false,
          elements : \"body_text\",
          plugins : \"table,advlink\",
          theme_advanced_buttons1 : \"mymenubutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,|,link,unlink,code,|,formatselect,forecolor,backcolor,|,tablecontrols\",
          theme_advanced_buttons2 : \"\",
          theme_advanced_buttons3 : \"\",
          theme_advanced_toolbar_location : \"external\",
          theme_advanced_toolbar_align : \"left\",
          editor_selector  : \"mceClass\",
          });\n";
echo "
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
echo "<form method=\"POST\" name=\"contract\">\n";
$gForm = new GAzieForm();
echo "<input type=\"hidden\" name=\"".ucfirst($toDo)."\" value=\"\">\n";
echo "<input type=\"hidden\" value=\"".$form['id_contract']."\" name=\"id_contract\">\n";
echo "<input type=\"hidden\" value=\"".$form['id_body_text']."\" name=\"id_body_text\">\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">$title ";
$select_cliente = new selectPartner("id_customer");
$select_cliente->selectDocPartner('id_customer',$form['id_customer'],$form['search']['id_customer'],'id_customer',$script_transl['mesg'],$admin_aziend['mascli']);
echo ' n.'.$form['doc_number']."</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['vat_section']."</td><td class=\"FacetDataTD\">\n";
$gForm->selectNumber('vat_section',$form['vat_section'],0,1,3);
echo "\t </td>\n";
if (!empty($msg)) {
    echo '<td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td>\n";
} else {
    echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['address']."</td><td>".$cliente['indspe']."<br />";
    echo "</td>\n";
}
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['conclusion_date']."</td><td class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('conclusion_date',$form['conclusion_date_D'],$form['conclusion_date_M'],$form['conclusion_date_Y']);
echo "\t</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['doc_number']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"doc_number\" value=\"".$form['doc_number']."\" align=\"right\" maxlength=\"9\" size=\"3\" /></td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['current_fee']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"current_fee\" value=\"".$form['current_fee']."\" align=\"right\" maxlength=\"9\" size=\"9\" tabindex=\"2\" /></td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['payment_method']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectFromDB('pagame','payment_method','codice',$form['payment_method'],'codice',1,' ','descri');
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['initial_fee']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"initial_fee\" value=\"".$form['initial_fee']."\" align=\"right\" maxlength=\"9\" size=\"3\" /></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['bank']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectFromDB('banapp','bank','codice',$form['bank'],'codice',1,' ','descri');
echo "</td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['start_date']."</td>\n";
echo "\t<td class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('start_date',$form['start_date_D'],$form['start_date_M'],$form['start_date_Y']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['months_duration']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"months_duration\" value=\"".$form['months_duration']."\" align=\"right\" maxlength=\"3\" size=\"3\" />\n";
echo "\t </td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['id_agente']."</td><td  class=\"FacetDataTD\">\n";
$select_agente = new selectAgente("id_agente");
$select_agente->addSelected($form["id_agente"]);
$select_agente->output();
echo " ".$script_transl['provvigione']."\n";
echo "\t<input type=\"text\" name=\"provvigione\" value=\"".$form['provvigione']."\" align=\"right\" maxlength=\"5\" size=\"3\" />\n";
echo "</td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['doc_type']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('doc_type',$script_transl['doc_type_value'],$form['doc_type']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['tacit_renewal']."</td><td class=\"FacetDataTD\">\n";
$gForm->selectNumber('tacit_renewal',$form['tacit_renewal'],1);
echo "\t </td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['cod_revenue']."</td><td class=\"FacetDataTD\">\n";
$select_cod_revenue = new selectconven('cod_revenue');
$select_cod_revenue->addSelected($form['cod_revenue']);
$select_cod_revenue->output(substr($form['cod_revenue'],0,1));
echo "\t </td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['last_reassessment']."</td><td class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('last_reassessment',$form['last_reassessment_D'],$form['last_reassessment_M'],$form['last_reassessment_Y']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">\n".$script_transl['periodic_reassessment']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectNumber('periodic_reassessment',$form['periodic_reassessment'],1);
echo "\t </td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['vat_code']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectFromDB('aliiva','vat_code','codice',$form['vat_code'],'codice',0,' - ','descri');
echo "</td>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['periodicity']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('periodicity',$script_transl['periodicity_value'],$form['periodicity']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td colspan=\"6\" align=\"center\">\n";
echo $script_transl['body_text'];
echo "</td></tr>\n";
echo "\t<td colspan=\"6\">\n";
echo "<textarea id=\"body_text\" name=\"body_text\" class=\"mceClass\" style=\"width:100%;height:400px;\" >".$form['body_text']."</textarea>\n";
echo "</td></tr>\n";
echo "</table>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr>\n";
echo "\t<td colspan=\"8\" align=\"center\">".$script_transl['rows_title']."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['in_status']."\" name=\"in_status\" />\n";
echo "\t<tr class=\"FacetColumnTD\" align=\"center\">\n";
echo "\t<td colspan=\"3\">".$script_transl['descri']."</td>\n";
echo "\t<td>".$script_transl['unimis']."</td>\n";
echo "\t<td>".$script_transl['quanti']."</td>\n";
echo "\t<td>".$script_transl['price']."</td>\n";
echo "\t<td>".$script_transl['discount']."</td>\n";
echo "</tr>\n";
echo "<tr class=\"FacetColumnTD\" align=\"center\">\n";
echo "<td colspan=\"3\">\n";
echo "<input type=\"text\" value=\"".$form['in_descri']."\" maxlength=\"100\" size=\"100\" name=\"in_descri\">\n";
echo "\t </td>\n";
echo "<td>\n";
echo "<input type=\"text\" value=\"".$form['in_unimis']."\" maxlength=\"3\" size=\"3\" name=\"in_unimis\">\n";
echo "\t </td>\n";
echo "<td>\n";
echo "<input type=\"text\" style=\"text-align:right\" value=\"".$form['in_quanti']."\" maxlength=\"11\" size=\"7\" name=\"in_quanti\">\n";
echo "\t </td>\n";
echo "<td >\n";
echo "<input type=\"text\" style=\"text-align:right\" value=\"".$form['in_price']."\" maxlength=\"15\" size=\"7\" name=\"in_price\">\n";
echo "\t </td>\n";
echo "<td>\n";
echo "<input type=\"text\" style=\"text-align:right\" value=\"".$form['in_discount']."\" maxlength=\"4\" size=\"1\" name=\"in_discount\">";
echo "\t </td>\n";
echo "<td align=\"right\">\n";
echo "<input type=\"image\" name=\"in_submit\" src=\"../../library/images/vbut.gif\" title=\"".$script_transl['submit'].$script_transl['thisrow']."!\">\n";
echo "\t </td>\n";
echo "\t </tr>\n";
echo "\t<tr class=\"FacetColumnTD\">\n";
echo "<td colspan=\"7\">\n";
echo $script_transl['vat_code'].' :';
$gForm->selectFromDB('aliiva','in_vat_code','codice',$form['in_vat_code'],'codice',0,' - ','descri');
echo $script_transl['cod_revenue'].' :';
$select_cod_revenue = new selectconven("in_cod_revenue");
$select_cod_revenue -> addSelected($form['in_cod_revenue']);
$select_cod_revenue -> output(substr($form['in_cod_revenue'],0,1));
echo "\t </td>\n";
echo "\t </tr>\n";
if ($next_row>0) {
    echo "<tr class=\"FacetFieldCaptionTD\"><td colspan=\"8\">".$script_transl['insrow']." :</td></tr>\n";
    foreach ($form['rows'] as $k=>$val) {
            $nr=$k+1;
            $aliiva = gaz_dbi_get_row($gTables['aliiva'],'codice',$val['vat_code']);
            echo "<input type=\"hidden\" value=\"".$val['status']."\" name=\"rows[$k][status]\">\n";
            echo "<input type=\"hidden\" value=\"".$val['vat_code']."\" name=\"rows[$k][vat_code]\">\n";
            echo "<input type=\"hidden\" value=\"".$val['cod_revenue']."\" name=\"rows[$k][cod_revenue]\">\n";
            echo "<tr class=\"FacetFieldCaptionTD\">\n";
            echo "<td colspan=\"3\">$nr<input type=\"text\" name=\"rows[$k][descri]\" value=\"".$val['descri']."\" maxlength=\"100\" size=\"50\" />
                  ".$script_transl['cod_revenue'].": ".$val['cod_revenue']." - ".$aliiva['descri']."</td>\n";
            echo "<td><input type=\"text\" name=\"rows[$k][unimis]\" value=\"".$val['unimis']."\" maxlength=\"3\" size=\"3\" /></td>\n";
            echo "<td><input type=\"text\" style=\"text-align:right\" name=\"rows[$k][quanti]\" value=\"".$val['quanti']."\" maxlength=\"11\" size=\"7\" /></td>\n";
            echo "<td><input type=\"text\" style=\"text-align:right\" name=\"rows[$k][price]\" value=\"".$val['price']."\" maxlength=\"15\" size=\"7\" /></td>\n";
            echo "<td><input type=\"text\" style=\"text-align:right\" name=\"rows[$k][discount]\" value=\"".$val['discount']."\" maxlength=\"4\" size=\"3\" /></td>\n";
            echo "<td align=\"right\"><input type=\"image\" name=\"del[$k]\" src=\"../../library/images/xbut.gif\" title=\"".$script_transl['delete'].$script_transl['thisrow']."!\" /></td></tr>\n";
            echo "\t </tr>\n";
    }
}
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo '<td colspan="6" align="right"> <input type="submit" accesskey="i" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="';
echo $script_transl['submit'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";
?>
</form>
</body>
</html>