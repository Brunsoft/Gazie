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
require("../../modules/magazz/lib.function.php");
$admin_aziend=checkAdmin();
$msg = "";

$upd_mm = new magazzForm;
$docOperat = $upd_mm->getOperators();

if (isset($_POST['newdestin'])) {
    $_POST['id_des']=0;
    $_POST['destin']="";
}

if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if ((isset($_GET['Update']) and  !isset($_GET['id_tes'])) and
    !isset($_GET['tipdoc'] )) {
    header("Location: ".$form['ritorno']);
    exit;
}

if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if ((isset($_POST['Insert'])) or (isset($_POST['Update']))) {   //se non e' il primo accesso
    //qui si dovrebbe fare un parsing di quanto arriva dal browser...
    $form['id_tes'] = $_POST['id_tes'];
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner($_POST['clfoco']);
    $form['hidden_req'] = $_POST['hidden_req'];
    // ...e della testata
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    $form['cosear'] = $_POST['cosear'];
    $form['seziva'] = $_POST['seziva'];
    $form['tipdoc'] = $_POST['tipdoc'];
    $form['gioemi'] = $_POST['gioemi'];
    $form['mesemi'] = $_POST['mesemi'];
    $form['annemi'] = $_POST['annemi'];
    $form['giotra'] = $_POST['giotra'];
    $form['mestra'] = $_POST['mestra'];
    $form['anntra'] = $_POST['anntra'];
    $form['oratra'] = $_POST['oratra'];
    $form['mintra'] = $_POST['mintra'];
    $form['protoc'] = $_POST['protoc'];
    $form['numdoc'] = $_POST['numdoc'];
    $form['numfat'] = $_POST['numfat'];
    $form['datfat'] = $_POST['datfat'];
    $form['clfoco'] = $_POST['clfoco'];
    //tutti i controlli su  tipo di pagamento e rate
    $form['speban'] = $_POST['speban'];
    $form['numrat'] = $_POST['numrat'];
    $form['pagame'] = $_POST['pagame'];
    $form['change_pag'] = $_POST['change_pag'];
    if ($form['change_pag'] != $form['pagame']){  //se è stato cambiato il pagamento
       $new_pag = gaz_dbi_get_row($gTables['pagame'],"codice",$form['pagame']);
       $old_pag = gaz_dbi_get_row($gTables['pagame'],"codice",$form['change_pag']);
       if (($new_pag['tippag'] == 'B' or $new_pag['tippag'] == 'T' or $new_pag['tippag'] == 'V')
           and ($old_pag['tippag'] == 'C' or $old_pag['tippag'] == 'D')) { // se adesso devo mettere le spese e prima no
           $form['numrat'] = $new_pag['numrat'];
           if ($toDo == 'update') {  //se è una modifica mi baso sulle vecchie spese
              $old_header = gaz_dbi_get_row($gTables['tesdoc'],"id_tes",$form['id_tes']);
              if ($old_header['speban'] > 0 and $fornitore['speban'] == "S"){
                 $form['speban'] = 0;
              } elseif ($old_header['speban'] == 0 and $fornitore['speban'] == "S"){
                 $form['speban'] = 0;
              } else {
                 $form['speban'] = 0.00;
              }
           } else { //altrimenti mi avvalgo delle nuove dell'azienda
              $form['speban'] = 0;
           }
       } elseif (($new_pag['tippag'] == 'C' or $new_pag['tippag'] == 'D')
           and ($old_pag['tippag'] == 'B' or $old_pag['tippag'] == 'T' or $old_pag['tippag'] == 'V')) { // se devo togliere le spese
           $form['speban'] = 0.00;
           $form['numrat'] = 1;
       }
       $form['pagame'] = $_POST['pagame'];
       $form['change_pag'] = $_POST['pagame'];
    }
    $form['banapp'] = $_POST['banapp'];
    $form['vettor'] = $_POST['vettor'];
    $form['listin'] = $_POST['listin'];
    $form['spediz'] = $_POST['spediz'];
    $form['portos'] = $_POST['portos'];
    $form['imball'] = $_POST['imball'];
    $form['destin'] = $_POST['destin'];
    $form['id_des'] = substr($_POST['id_des'],3);;
    $form['traspo'] = $_POST['traspo'];
    $form['spevar'] = $_POST['spevar'];
    $form['ivaspe'] = $_POST['ivaspe'];
    $form['pervat'] = $_POST['pervat'];
    $form['cauven'] = $_POST['cauven'];
    $form['caucon'] = $_POST['caucon'];
    $form['caumag'] = $_POST['caumag'];
    $form['caucon'] = $_POST['caucon'];
    $form['id_agente'] = $_POST['id_agente'];
    $form['id_pro'] = $_POST['id_pro'];
    $form['sconto'] = $_POST['sconto'];
    // inizio rigo di input
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_tiprig'] = $_POST['in_tiprig'];
    $form['in_artsea'] = $_POST['in_artsea'];
    $form['in_codart'] = $_POST['in_codart'];
    $form['in_pervat'] = $_POST['in_pervat'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_prelis'] = $_POST['in_prelis'];
    $form['in_sconto'] = $_POST['in_sconto'];
    $form['in_quanti'] = gaz_format_quantity($_POST['in_quanti'],0,$admin_aziend['decimal_quantity']);
    $form['in_codvat'] = $_POST['in_codvat'];
    $form['in_codric'] = $_POST['in_codric'];
    $form['in_id_mag'] = $_POST['in_id_mag'];
    $form['in_annota'] = $_POST['in_annota'];
    $form['in_pesosp'] = $_POST['in_pesosp'];
    $form['in_status'] = $_POST['in_status'];
    // fine rigo input
    
    
    $form['rows'] = array();
    $i = 0;
    if (isset($_POST['rows'])) {
       foreach ($_POST['rows'] as $i => $value) {
            if (isset($_POST["row_$i"])) { //se ho un rigo testo
               $form["row_$i"] = $_POST["row_$i"];
            }
            $form['rows'][$i]['descri'] = substr($value['descri'],0,50);
            $form['rows'][$i]['tiprig'] = intval($value['tiprig']);
            $form['rows'][$i]['codart'] = substr($value['codart'],0,15);
            $form['rows'][$i]['pervat'] = preg_replace("/\,/",'.',$value['pervat']);
            $form['rows'][$i]['unimis'] = substr($value['unimis'],0,3);
            $form['rows'][$i]['prelis'] = number_format(floatval(preg_replace("/\,/",'.',$value['prelis'])),$admin_aziend['decimal_price'],'.','');
            $form['rows'][$i]['sconto'] = floatval(preg_replace("/\,/",'.',$value['sconto']));
            $form['rows'][$i]['quanti'] = gaz_format_quantity($value['quanti'],0,$admin_aziend['decimal_quantity']);
            $form['rows'][$i]['codvat'] = intval($value['codvat']);
            $form['rows'][$i]['codric'] = intval($value['codric']);
            $form['rows'][$i]['id_mag'] = intval($value['id_mag']);
            $form['rows'][$i]['lot_or_serial'] = intval($value['lot_or_serial']);
            $form['rows'][$i]['annota'] = substr($value['annota'],0,50);
            $form['rows'][$i]['pesosp'] = floatval($value['pesosp']);
            $form['rows'][$i]['status'] = substr($value['status'],0,10);

            if (isset($_POST['upd_row'])) {
               $key_row = key($_POST['upd_row']);
               if ($key_row == $i) {
                  $form['in_descri'] = $form['rows'][$key_row]['descri'];
                  $form['in_tiprig'] = $form['rows'][$key_row]['tiprig'];
                  $form['in_codart'] = $form['rows'][$key_row]['codart'];
                  $form['in_pervat'] = $form['rows'][$key_row]['pervat'];
                  $form['in_unimis'] = $form['rows'][$key_row]['unimis'];
                  $form['in_prelis'] = $form['rows'][$key_row]['prelis'];
                  $form['in_sconto'] = $form['rows'][$key_row]['sconto'];
                  $form['in_quanti'] = $form['rows'][$key_row]['quanti'];
                  $form['in_codvat'] = $form['rows'][$key_row]['codvat'];
                  $form['in_codric'] = $form['rows'][$key_row]['codric'];
                  $form['in_id_mag'] = $form['rows'][$key_row]['id_mag'];
                  $form['in_annota'] = $form['rows'][$key_row]['annota'];
                  $form['in_pesosp'] = $form['rows'][$key_row]['pesosp'];
                  $form['in_status'] = "UPDROW".$key_row;
                  if ($form['in_artsea'] == 'D'){
                    $artico_u = gaz_dbi_get_row($gTables['artico'],'codice',$form['rows'][$key_row]['codart']);
                    $form['cosear'] = $artico_u['descri'];
                  } elseif ($form['in_artsea'] == 'B') {
                    $artico_u = gaz_dbi_get_row($gTables['artico'],'codice',$form['rows'][$key_row]['codart']);
                    $form['cosear'] = $artico_u['barcode'];
                  } else {
                    $form['cosear'] = $form['rows'][$key_row]['codart'];
                  }
                  array_splice($form['rows'],$key_row,1);
                  $i--;
               }
            }
            $i++;
       }
    }
    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
       $sezione=$form['seziva'];
       $datemi = $form['annemi']."-".$form['mesemi']."-".$form['gioemi'];
       $utsemi = mktime(0,0,0,$form['mesemi'],$form['gioemi'],$form['annemi']);
       $initra = $form['anntra']."-".$form['mestra']."-".$form['giotra'];
       $utstra = mktime(0,0,0,$form['mestra'],$form['giotra'],$form['anntra']);
       if ($form['tipdoc'] == 'DDR' or $form['tipdoc'] == 'DDL') {  //se è un DDT vs Fattura differita
          if ($utstra < $utsemi) {
             $msg .= "38+";
          }
          if (!checkdate( $form['mestra'], $form['giotra'], $form['anntra'])) {
             $msg .= "37+";
          }
       } else {
          if ($utstra > $utsemi) {
             $msg .= "53+";
          }
          if (!checkdate( $form['mestra'], $form['giotra'], $form['anntra'])) {
             $msg .= "54+";
          }
          if (empty($form['numfat'])) {
             $msg .= "55+";
          }
       }
       if (!isset($_POST['rows'])) {
          $msg .= "39+";
       }
       // --- inizio controllo coerenza date-numerazione
       if ($toDo == 'update') {  // controlli in caso di modifica
          if ($form['tipdoc'] == 'DDR' or $form['tipdoc'] == 'DDL') {  //se è un DDT vs Fattura differita
             $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi < '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione","numdoc desc",0,1);
             $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
             if ($result and ($form['numdoc'] < $result['numdoc'])) {
                $msg .= "40+";
             }
             $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi > '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione","numdoc asc",0,1);
             $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
             if ($result and ($form['numdoc'] > $result['numdoc'])) {
                $msg .= "41+";
             }
          } elseif ($form['tipdoc'] == 'ADT') { //se è un DDT acquisto
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi < '$datemi' and tipdoc = 'ADT' and seziva = $sezione","protoc desc",0,1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
            if ($result && ($form['protoc'] < $result['protoc'])) {
               $msg .= "42+";
            }
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi > '$datemi' and tipdoc = 'ADT' and seziva = $sezione","protoc asc",0,1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
            if ($result && ($form['protoc'] > $result['protoc'])) {
                $msg .= "43+";
            }
          } else { //se sono altri documenti
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi < '$datemi' and tipdoc like '".substr($form['tipdoc'],0,1)."__' and seziva = $sezione","protoc desc",0,1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
            if ($result && ($form['protoc'] < $result['protoc'])) {
               $msg .= "42+";
            }
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and datemi > '$datemi' and tipdoc like '".substr($form['tipdoc'],0,1)."__' and seziva = $sezione","protoc asc",0,1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
            if ($result && ($form['protoc'] > $result['protoc'])) {
               $msg .= "43+";
            }
          }
       } else {    //controlli in caso di inserimento
         if ($form['tipdoc'] == 'DDR' or $form['tipdoc'] == 'DDL') {  //se è un DDT
            $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and tipdoc like 'DD_' and seziva = $sezione","numdoc desc, datemi desc",0,1);
            $ultimo_ddt = gaz_dbi_fetch_array($rs_ultimo_ddt);
            $utsUltimoDdT = mktime(0,0,0,substr($ultimo_ddt['datfat'],5,2),substr($ultimo_ddt['datfat'],8,2),substr($ultimo_ddt['datfat'],0,4));
            if ($ultimo_ddt and ($utsUltimoDdT > $utsemi)) {
               $msg .= "44+";
            }
         } else { //se sono altri documenti
           $rs_ultimo_tipo = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = ".$form['annemi']." and tipdoc like '".substr($form['tipdoc'],0,1)."%' and seziva = $sezione","protoc desc, datfat desc, datemi desc",0,1);
           $ultimo_tipo = gaz_dbi_fetch_array($rs_ultimo_tipo);
           $utsUltimoProtocollo = mktime(0,0,0,substr($ultimo_tipo['datfat'],5,2),substr($ultimo_tipo['datfat'],8,2),substr($ultimo_tipo['datfat'],0,4));
           if ($ultimo_tipo and ($utsUltimoProtocollo > $utsemi)) {
              $msg .= "45+";
           }
         }
       }
       // --- fine controllo coerenza date-numeri
       if (!checkdate( $form['mesemi'], $form['gioemi'], $form['annemi']))
          $msg .= "46+";
       if (empty ($form["clfoco"]))
          $msg .= "47+";
       if (empty ($form["pagame"]))
          $msg .= "48+";
       //controllo che i righi non abbiano descrizioni  e unita' di misura vuote in presenza di quantita diverse da 0
       foreach ($form['rows'] as $i => $value) {
            if ($value['descri']=='' &&
                $value['quanti']) {
                $msgrigo= $i+1;
                $msg .= "49+";
            }
            if ($value['unimis']=='' &&
                $value['quanti'] &&
                $value['tiprig'] == 0) {
                $msgrigo= $i+1;
                $msg .= "50+";
            }
       }
       if ($msg == "") {// nessun errore
          if (preg_match("/^id_([0-9]+)$/",$form['clfoco'],$match)) {
             $new_clfoco = $anagrafica->getPartnerData($match[1],1);
             $form['clfoco']=$anagrafica->anagra_to_clfoco($new_clfoco,$admin_aziend['masfor']);
          }
          function getProtocol($type,$year,$sezione)  // questa funzione trova l'ultimo numero di protocollo
          {                                           // controllando sia l'archivio documenti che il
                global $gTables;                      // registro IVA acquisti
                $rs_ultimo_tesdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'],"YEAR(datemi) = $year AND tipdoc LIKE '".substr($type,0,2)."_' AND seziva = $sezione","protoc DESC",0,1);
                $ultimo_tesdoc = gaz_dbi_fetch_array($rs_ultimo_tesdoc);
                $rs_ultimo_tesmov = gaz_dbi_dyn_query("*", $gTables['tesmov'],"YEAR(datreg) = $year AND regiva = 6 AND seziva = $sezione","protoc DESC",0,1);
                $ultimo_tesmov = gaz_dbi_fetch_array($rs_ultimo_tesmov);
                $lastProtocol=0;
                if ($ultimo_tesdoc) {
                   $lastProtocol = $ultimo_tesdoc['protoc'];
                }
                if ($ultimo_tesmov) {
                   if ($ultimo_tesmov['protoc'] > $lastProtocol){
                      $lastProtocol = $ultimo_tesmov['protoc'];
                   }
                }
                return $lastProtocol+1;
          }
          $initra .= " ".$form['oratra'].":".$form['mintra'].":00";
          $form['spediz'] = addslashes($form['spediz']);
          $form['portos'] = addslashes($form['portos']);
          $form['imball'] = addslashes($form['imball']);
          $form['destin'] = addslashes($form['destin']);
          if ($toDo == 'update') { // e' una modifica
             $old_rows = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = ".$form['id_tes'],"id_rig asc");
             $i=0;
             $count = count($form['rows'])-1;
             while ($val_old_row = gaz_dbi_fetch_array($old_rows)) {
              if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                 $form['rows'][$i]['id_tes'] = $form['id_tes'];
                 $codice = array('id_rig',$val_old_row['id_rig']);
                 rigdocUpdate($codice,$form['rows'][$i]);
                 if (isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0) { //se è un rigo testo già presente lo modifico
                      bodytextUpdate(array('id_body',$val_old_row['id_body_text']),array('table_name_ref'=>'rigdoc','id_ref'=>$val_old_row['id_rig'],'body_text'=>$form["row_$i"],'lang_id'=>$admin_aziend['id_language']));
                      gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', $val_old_row['id_body_text']);
                 } elseif (isset($form["row_$i"]) && $val_old_row['id_body_text'] == 0 ) { //prima era un rigo diverso da testo
                      bodytextInsert(array('table_name_ref'=>'rigdoc','id_ref'=>$val_old_row['id_rig'],'body_text'=>$form["row_$i"],'lang_id'=>$admin_aziend['id_language']));
                      gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', gaz_dbi_last_id());
                 } elseif (!isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0){ //un rigo che prima era testo adesso non lo è più
                      gaz_dbi_del_row($gTables['body_text'], "table_name_ref = 'rigdoc' AND id_ref", $val_old_row['id_rig']);
                 }
                 if ($form['rows'][$i]['id_mag'] > 0 ){ //se il rigo ha un movimento di magazzino associato
                     $upd_mm->uploadMag($val_old_row['id_rig'],
                                    $form['tipdoc'],
                                    $form['numdoc'],
                                    $form['seziva'],
                                    $datemi,
                                    $form['clfoco'],
                                    $form['sconto'],
                                    $form['caumag'],
                                    $form['rows'][$i]['codart'],
                                    $form['rows'][$i]['quanti'],
                                    $form['rows'][$i]['prelis'],
                                    $form['rows'][$i]['sconto'],
                                    $val_old_row['id_mag'],
                                    $admin_aziend['stock_eval_method'],
                                    false,
                                    $form['protoc']
                                    );
                 }
              } else { //altrimenti lo elimino
                  if ($val_old_row['id_mag'] > 0){  //se c'è stato un movimento di magazzino lo azzero
                     $upd_mm->uploadMag('DEL',$form['tipdoc'],'','','','','','','','','','',$val_old_row['id_mag'],$admin_aziend['stock_eval_method']);
                  }
                  gaz_dbi_del_row($gTables['rigdoc'], "id_rig", $val_old_row['id_rig']);
              }
              $i++;
             }
             //qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
             for ($i = $i; $i <= $count; $i++) {
                $form['rows'][$i]['id_tes'] = $form['id_tes'];
                rigdocInsert($form['rows'][$i]);
                if ($admin_aziend['conmag'] == 2 and
                   $form['rows'][$i]['tiprig'] == 0 and
                   !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                   $upd_mm->uploadMag(gaz_dbi_last_id(),
                                    $form['tipdoc'],
                                    $form['numdoc'],
                                    $form['seziva'],
                                    $datemi,
                                    $form['clfoco'],
                                    $form['sconto'],
                                    $form['caumag'],
                                    $form['rows'][$i]['codart'],
                                    $form['rows'][$i]['quanti'],
                                    $form['rows'][$i]['prelis'],
                                    $form['rows'][$i]['sconto'],
                                    0,
                                    $admin_aziend['stock_eval_method'],
                                    false,
                                    $form['protoc']
                                    );
                }
             }
             //modifico la testata con i nuovi dati...
             $old_head = gaz_dbi_get_row($gTables['tesdoc'],'id_tes',$form['id_tes']);
             if (substr($form['tipdoc'],0,2) == 'DD') { //se è un DDT non fatturato
               $form['datfat'] = '';
               $form['numfat'] = 0;
             } else {
               $form['datfat'] = $initra;
             }
             $form['geneff'] = $old_head['geneff'];
             $form['id_contract'] = $old_head['id_contract'];
             $form['id_con'] = $old_head['id_con'];
             $form['status'] = $old_head['status'];
             $form['initra'] = $initra;
             $form['datemi'] = $datemi;
             $codice = array('id_tes',$form['id_tes']);
             tesdocUpdate($codice,$form);
             header("Location: ".$form['ritorno']);
             exit;
          } else { // e' un'inserimento
            // ricavo i progressivi in base al tipo di documento
            $where = "numdoc desc";
            switch ($form['tipdoc']) {
                  case "DDR":
                  $sql_documento = "YEAR(datemi) = ".$form['annemi']." and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione";
                  break;
                  case "DDL":
                  $sql_documento = "YEAR(datemi) = ".$form['annemi']." and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione";
                  break;
                  case "AFA":
                  $sql_documento = "YEAR(datemi) = ".$form['annemi']." and tipdoc like 'AFA' and seziva = $sezione";
                  $where = "numfat desc";
                  break;
                  case "ADT":
                  $sql_documento = "YEAR(datemi) = ".$form['annemi']." and tipdoc like 'ADT' and seziva = $sezione";
                  break;
                  case "AFC":
                  $sql_documento = "YEAR(datemi) = ".$form['annemi']." and tipdoc = 'AFC' and seziva = $sezione";
                  $where = "numfat desc";
                  break;
            }
            $rs_ultimo_documento = gaz_dbi_dyn_query("*", $gTables['tesdoc'], $sql_documento,$where,0,1);
            $ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
            // se e' il primo documento dell'anno, resetto il contatore
            if ($ultimo_documento) {
               $form['numdoc'] = $ultimo_documento['numdoc'] + 1;
            } else {
               $form['numdoc'] = 1;
            }
            if (substr($form['tipdoc'],0,2) == 'DD'){  //ma se e' un ddt il protocollo è 0 così come il numero e data fattura
               $form['protoc'] = 0;
               $form['numfat'] = 0;
               $form['datfat'] = 0;
            } else { //in tutti gli altri casi si deve prendere quanto inserito nel form
               $form['datfat'] = $initra;
               $form['protoc'] = getProtocol($form['tipdoc'],$form['annemi'],$sezione);
               $form['numdoc'] = $form['numfat'];
            }
            //inserisco la testata
            $form['status'] = '';
            $form['initra'] = $initra;
            $form['datemi'] = $datemi;
            tesdocInsert($form);
            //recupero l'id assegnato dall'inserimento
            $ultimo_id = gaz_dbi_last_id();
            //inserisco i righi
            foreach ($form['rows'] as $i => $value) {
                  $form['rows'][$i]['id_tes'] = $ultimo_id;
                  rigdocInsert($form['rows'][$i]);
                  $last_rigdoc_id = gaz_dbi_last_id();
                  if (isset($form["row_$i"])) { //se è un rigo testo lo inserisco il contenuto in body_text
                      bodytextInsert(array('table_name_ref'=>'rigdoc','id_ref'=>$last_rigdoc_id,'body_text'=>$form["row_$i"],'lang_id'=>$admin_aziend['id_language']));
                      gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_body_text', gaz_dbi_last_id());
                  }
                  if ($admin_aziend['conmag'] == 2 and
                     $form['rows'][$i]['tiprig'] == 0 and
                     !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                     $upd_mm->uploadMag(gaz_dbi_last_id(),
                                    $form['tipdoc'],
                                    $form['numdoc'],
                                    $form['seziva'],
                                    $datemi,
                                    $form['clfoco'],
                                    $form['sconto'],
                                    $form['caumag'],
                                    $form['rows'][$i]['codart'],
                                    $form['rows'][$i]['quanti'],
                                    $form['rows'][$i]['prelis'],
                                    $form['rows'][$i]['sconto'],
                                    0,
                                    $admin_aziend['stock_eval_method'],
                                    false,
                                    $form['protoc']
                                    );
                  }
            }
          $_SESSION['print_request'] = $ultimo_id;
          header("Location: invsta_docacq.php");
          exit;
       }
    }
  }
  // Se viene inviata la richiesta di conferma fornitore
  if ($_POST['hidden_req']=='clfoco') {
    $anagrafica = new Anagrafica();
    if (preg_match("/^id_([0-9]+)$/",$form['clfoco'],$match)) {
        $fornitore = $anagrafica->getPartnerData($match[1],1);
    } else {
        $fornitore = $anagrafica->getPartner($form['clfoco']);
    }
    if (substr($form['tipdoc'],0,1) != 'A') {
       $result = gaz_dbi_get_row($gTables['imball'],"codice",$fornitore['imball']);
       $form['imball']=$result['descri'];
    }
    $result = gaz_dbi_get_row($gTables['portos'],"codice",$fornitore['portos']);
    $form['portos']=$result['descri'];
    $result = gaz_dbi_get_row($gTables['spediz'],"codice",$fornitore['spediz']);
    $form['spediz']=$result['descri'];
    $form['destin']=$fornitore['destin'];
    $form['id_des']=$fornitore['id_des'];
    $id_des = $anagrafica->getPartner($form['id_des']);
    $form['search']['id_des']=substr($id_des['ragso1'],0,10);
    if ($fornitore['aliiva'] > 0) {
        $form['ivaspe']=$fornitore['aliiva'];
        $result = gaz_dbi_get_row($gTables['aliiva'],'codice',$fornitore['aliiva']);
        $form['pervat']=$result['aliquo'];
    }
    $form['in_codvat']=$fornitore['aliiva'];
    $form['sconto']=$fornitore['sconto'];
    $form['pagame']=$fornitore['codpag'];
    $form['change_pag']=$fornitore['codpag'];
    $form['banapp']=$fornitore['banapp'];
    $form['listin']=$fornitore['listin'];
    $pagame = gaz_dbi_get_row($gTables['pagame'],"codice",$form['pagame']);
    if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V')
        and $fornitore['speban'] == 'S') {
           $form['speban'] = 0;
           $form['numrat'] = $pagame['numrat'];
    } else {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
    }
    if ($fornitore['cosric'] > 0) {
       $form['in_codric'] = $fornitore['cosric'];
    }
    $form['hidden_req']='';
  }

  // Se viene inviata la richiesta di conferma rigo
  if (isset($_POST['in_submit_x'])) {
    $artico = gaz_dbi_get_row($gTables['artico'],"codice",$form['in_codart']);
    if (substr($form['in_status'],0,6) == "UPDROW"){ //se è un rigo da modificare
         $old_key = intval(substr($form['in_status'],6));
         $form['rows'][$old_key]['tiprig'] = $form['in_tiprig'];
         $form['rows'][$old_key]['descri'] = $form['in_descri'];
         $form['rows'][$old_key]['id_mag'] = $form['in_id_mag'];
         $form['rows'][$old_key]['status'] = "UPDATE";
         $form['rows'][$old_key]['unimis'] = $form['in_unimis'];
         $form['rows'][$old_key]['quanti'] = $form['in_quanti'];
         $form['rows'][$old_key]['codart'] = $form['in_codart'];
         $form['rows'][$old_key]['codric'] = $form['in_codric'];
         $form['rows'][$old_key]['prelis'] = number_format($form['in_prelis'],$admin_aziend['decimal_price'],'.','');
         $form['rows'][$old_key]['sconto'] = $form['in_sconto'];
         $form['rows'][$old_key]['codvat'] = $form['in_codvat'];
         $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
         $form['rows'][$old_key]['pervat'] = $iva_row['aliquo'];
         $form['rows'][$old_key]['annota'] = '';
         $form['rows'][$old_key]['pesosp'] = '';
         $form['rows'][$old_key]['lot_or_serial'] = '';
         if ($form['in_tiprig'] == 0 and !empty($form['in_codart'])) {  //rigo normale
            $form['rows'][$old_key]['annota'] = $artico['annota'];
            $form['rows'][$old_key]['pesosp'] = $artico['peso_specifico'];
            $form['rows'][$old_key]['unimis'] = $artico['unimis'];
            $form['rows'][$old_key]['descri'] = $artico['descri'];
            $form['rows'][$i]['lot_or_serial'] = $artico['lot_or_serial'];
            $form['rows'][$old_key]['prelis'] = number_format($artico['preacq'],$admin_aziend['decimal_price'],'.','');
         } elseif ($form['in_tiprig'] == 2) { //rigo descrittivo
            $form['rows'][$old_key]['codart'] = "";
            $form['rows'][$old_key]['annota'] = "";
            $form['rows'][$old_key]['pesosp'] = "";
            $form['rows'][$old_key]['unimis'] = "";
            $form['rows'][$old_key]['quanti'] = 0;
            $form['rows'][$old_key]['prelis'] = 0;
            $form['rows'][$old_key]['codric'] = 0;
            $form['rows'][$old_key]['sconto'] = 0;
            $form['rows'][$old_key]['pervat'] = 0;
            $form['rows'][$old_key]['codvat'] = 0;
         } elseif ($form['in_tiprig'] == 1) { //rigo forfait
            $form['rows'][$old_key]['codart'] = "";
            $form['rows'][$old_key]['unimis'] = "";
            $form['rows'][$old_key]['quanti'] = 0;
            $form['rows'][$old_key]['sconto'] = 0;
         } elseif ($form['in_tiprig'] == 3) {   //var.tot.fatt.
            $form['rows'][$old_key]['codart'] = "";
            $form['rows'][$old_key]['quanti'] = "";
            $form['rows'][$old_key]['unimis'] = "";
            $form['rows'][$old_key]['sconto'] = 0;
         }
         ksort($form['rows']);
    } else { //se è un rigo da inserire
         $form['rows'][$i]['tiprig'] = $form['in_tiprig'];
         $form['rows'][$i]['descri'] = $form['in_descri'];
         $form['rows'][$i]['id_mag'] = $form['in_id_mag'];
         $form['rows'][$i]['status'] = "INSERT";
         if ($form['in_tiprig'] == 0) {  //rigo normale
            $form['rows'][$i]['codart'] = $form['in_codart'];
            $form['rows'][$i]['annota'] = $artico['annota'];
            $form['rows'][$i]['pesosp'] = $artico['peso_specifico'];
            $form['rows'][$i]['descri'] = $artico['descri'];
            $form['rows'][$i]['unimis'] = $artico['uniacq'];
            $form['rows'][$i]['lot_or_serial'] = $artico['lot_or_serial'];
            $form['rows'][$i]['codric'] = $form['in_codric'];
            $form['rows'][$i]['quanti'] = $form['in_quanti'];
            $form['rows'][$i]['sconto'] = $form['in_sconto'];
            $form['rows'][$i]['prelis'] = number_format($artico['preacq'],$admin_aziend['decimal_price'],'.','');
            $form['rows'][$i]['codvat'] = $admin_aziend['preeminent_vat'];
            $iva_azi = gaz_dbi_get_row($gTables['aliiva'],"codice",$admin_aziend['preeminent_vat']);
            $form['rows'][$i]['pervat'] = $iva_azi['aliquo'];
            if ($artico['aliiva'] > 0) {
               $form['rows'][$i]['codvat'] = $artico['aliiva'];
               $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$artico['aliiva']);
               $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
            }
            if ($form['in_codvat'] > 0) {
               $form['rows'][$i]['codvat'] = $form['in_codvat'];
               $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
               $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
            }
            if ($artico['id_cost'] > 0) {
               $form['rows'][$i]['codric'] = $artico['id_cost'];
               $form['in_codric'] = $artico['id_cost'];
            }
            if ($form['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
               $form['in_codric'] = $admin_aziend['purchases_return'];
            }
         } elseif ($form['in_tiprig'] == 1) { //forfait
            $form['rows'][$i]['codart'] = "";
            $form['rows'][$i]['annota'] = "";
            $form['rows'][$i]['pesosp'] = "";
            $form['rows'][$i]['unimis'] = "";
            $form['rows'][$i]['lot_or_serial'] = '';
            $form['rows'][$i]['quanti'] = 0;
            $form['rows'][$i]['prelis'] = 0;
            $form['rows'][$i]['codric'] = $form['in_codric'];
            $form['rows'][$i]['sconto'] = 0;
            $form['rows'][$i]['codvat'] = $admin_aziend['preeminent_vat'];
            $iva_azi = gaz_dbi_get_row($gTables['aliiva'],"codice",$admin_aziend['preeminent_vat']);
            $form['rows'][$i]['pervat'] = $iva_azi['aliquo'];
            $form['rows'][$i]['tipiva'] = $iva_azi['tipiva'];
            if ($form['in_codvat'] > 0) {
               $form['rows'][$i]['codvat'] = $form['in_codvat'];
               $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
               $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
               $form['rows'][$i]['tipiva'] = $iva_row['tipiva'];
            }
         } elseif ($form['in_tiprig'] == 2) { //descrittivo
            $form['rows'][$i]['codart'] = "";
            $form['rows'][$i]['annota'] = "";
            $form['rows'][$i]['pesosp'] = "";
            $form['rows'][$i]['lot_or_serial'] = '';
            $form['rows'][$i]['unimis'] = "";
            $form['rows'][$i]['quanti'] = 0;
            $form['rows'][$i]['prelis'] = 0;
            $form['rows'][$i]['codric'] = 0;
            $form['rows'][$i]['sconto'] = 0;
            $form['rows'][$i]['pervat'] = 0;
            $form['rows'][$i]['codvat'] = 0;
         } elseif ($form['in_tiprig'] == 3) {
            $form['rows'][$i]['codart'] = "";
            $form['rows'][$i]['annota'] = "";
            $form['rows'][$i]['pesosp'] = "";
            $form['rows'][$i]['lot_or_serial'] = '';
            $form['rows'][$i]['unimis'] = "";
            $form['rows'][$i]['quanti'] = 0;
            $form['rows'][$i]['prelis'] = $form['in_prelis'];
            $form['rows'][$i]['codric'] = $form['in_codric'];
            $form['rows'][$i]['sconto'] = 0;
            $form['rows'][$i]['codvat'] = $form['in_codvat'];
            $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
            $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
         } elseif ($form['in_tiprig']>5 && $form['in_tiprig']<9) { //testo
            $form["row_$i"] = "";
            $form['rows'][$i]['codart'] = "";
            $form['rows'][$i]['annota'] = "";
            $form['rows'][$i]['pesosp'] = "";
            $form['rows'][$i]['lot_or_serial'] = '';
            $form['rows'][$i]['unimis'] = "";
            $form['rows'][$i]['quanti'] = 0;
            $form['rows'][$i]['prelis'] = 0;
            $form['rows'][$i]['codric'] = 0;
            $form['rows'][$i]['sconto'] = 0;
            $form['rows'][$i]['pervat'] = 0;
            $form['rows'][$i]['tipiva'] = 0;
            $form['rows'][$i]['ritenuta'] = 0;
            $form['rows'][$i]['codvat'] = 0;
         }
    }
     // reinizializzo rigo di input tranne che per il tipo rigo e aliquota iva
     $form['in_descri'] = "";
     $form['in_codart'] = "";
     $form['in_unimis'] = "";
     $form['in_prelis'] = 0.000;
     $form['in_sconto'] = 0;
     $form['in_quanti'] = 0;
     $form['in_id_mag'] = 0;
     $form['in_annota'] = "";
     $form['in_pesosp'] = 0;
     $form['in_status'] = "INSERT";
     // fine reinizializzo rigo input
     $form['cosear'] = "";
     $i++;
  }
  // Se viene inviata la richiesta di spostamento verso l'alto del rigo
  if (isset($_POST['upper_row'])) {
     $upp_key = key($_POST['upper_row']);
     $k_next = $upp_key-1;
     if (isset($form["row_$k_next"])) { //se ho un rigo testo prima gli cambio l'index
         $form["row_$upp_key"] = $form["row_$k_next"];
         unset($form["row_$k_next"]);
     }
     if ($upp_key > 0) {
        $new_key = $upp_key-1;
     } else {
        $new_key = $i-1;
     }
     $updated_row = $form['rows'][$new_key] ;
     $form['rows'][$new_key] = $form['rows'][$upp_key] ;
     $form['rows'][$upp_key] = $updated_row ;
     ksort($form['rows']);
     unset($updated_row);
  }
  // Se viene inviata la richiesta elimina il rigo corrispondente
  if (isset($_POST['del'])) {
    $delri= key($_POST['del']);

    // diminuisco o lascio inalterati gli index dei testi
    foreach ($form['rows'] as $k => $val) {
            if (isset($form["row_$k"])) { //se ho un rigo testo
               if ($k > $delri) { //se ho un rigo testo dopo
                   $new_k=$k-1;
                   $form["row_$new_k"] = $form["row_$k"];
                   unset($form["row_$k"]);
               }
            }
    }

    array_splice($form['rows'],$delri,1);
    $i--;
  }
} elseif ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $tesdoc = gaz_dbi_get_row($gTables['tesdoc'],"id_tes",intval($_GET['id_tes']));
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner($tesdoc['clfoco']);
    $id_des = $anagrafica->getPartner($tesdoc['id_des']);
    $rs_rig = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = ".$tesdoc['id_tes'],"id_rig asc");
    $form['id_tes'] = $tesdoc['id_tes'];
    $form['hidden_req'] = '';
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    $form['in_artsea'] = $admin_aziend['artsea'];
    $form['in_codart'] = "";
    $form['in_pervat'] = 0;
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    if ($fornitore['cosric'] > 0) {
       $form['in_codric'] = $fornitore['cosric'];
    } else {
       $form['in_codric'] = $admin_aziend['impacq'];
    }
    if ($tesdoc['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
       $form['in_codric'] = $admin_aziend['purchases_return'];
        if ($form['in_codric'] < 300000000) {
           $form['in_codric']='3';
        }
    }
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_pesosp'] = 0;
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['rows'] = array();
    // ...e della testata
    $form['search']['clfoco']=substr($fornitore['ragso1'],0,10);
    $form['cosear'] = "";
    $form['seziva'] = $tesdoc['seziva'];
    $form['tipdoc'] = $tesdoc['tipdoc'];
    if ( $tesdoc['tipdoc'] == 'FAD' ) {
       $msg .= "Vuoi modificare un D.d.T. gi&agrave; fatturato!<br />";
    }
    if ( $tesdoc['id_con'] > 0 ) {
       $msg .= "Questo documento &egrave; gi&agrave; stato contabilizzato!<br />";
    }
    $form['gioemi'] = substr($tesdoc['datemi'],8,2);
    $form['mesemi'] = substr($tesdoc['datemi'],5,2);
    $form['annemi'] = substr($tesdoc['datemi'],0,4);
    $form['giotra'] = substr($tesdoc['initra'],8,2);
    $form['mestra'] = substr($tesdoc['initra'],5,2);
    $form['anntra'] = substr($tesdoc['initra'],0,4);
    $form['oratra'] = substr($tesdoc['initra'],11,2);
    $form['mintra'] = substr($tesdoc['initra'],14,2);
    $form['protoc'] = $tesdoc['protoc'];
    $form['numdoc'] = $tesdoc['numdoc'];
    $form['numfat'] = $tesdoc['numfat'];
    $form['datfat'] = $tesdoc['datfat'];
    $form['clfoco'] = $tesdoc['clfoco'];
    $form['pagame'] = $tesdoc['pagame'];
    $form['change_pag'] = $tesdoc['pagame'];
    $form['speban'] = 0;
    $pagame = gaz_dbi_get_row($gTables['pagame'],"codice",$form['pagame']);
    if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V') and $fornitore['speban'] == 'S') {
            $form['numrat'] = $pagame['numrat'];
    } else {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
    }
    $form['banapp'] = $tesdoc['banapp'];
    $form['vettor'] = $tesdoc['vettor'];
    $form['listin'] = $tesdoc['listin'];
    $form['spediz'] = $tesdoc['spediz'];
    $form['portos'] = $tesdoc['portos'];
    $form['imball'] = $tesdoc['imball'];
    $form['destin'] = $tesdoc['destin'];
    $form['id_des'] = $tesdoc['id_des'];
    $form['search']['id_des']=substr($id_des['ragso1'],0,10);
    $form['traspo'] = $tesdoc['traspo'];
    $form['spevar'] = $tesdoc['spevar'];
    $form['ivaspe'] = 0;
    $form['pervat'] = 0;
    $form['cauven'] = $tesdoc['cauven'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['caumag'] = $tesdoc['caumag'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['id_agente'] = $tesdoc['id_agente'];
    $form['id_pro'] = $tesdoc['id_pro'];
    $form['sconto'] = $tesdoc['sconto'];
    $i = 0;
    while ($row = gaz_dbi_fetch_array($rs_rig)) {
        $articolo = gaz_dbi_get_row($gTables['artico'],"codice",$row['codart']);
        if ($row['id_body_text'] > 0) { //se ho un rigo testo
            $text = gaz_dbi_get_row($gTables['body_text'],"id_body",$row['id_body_text']);
            $form["row_$i"] = $text['body_text'];
        }
        $form['rows'][$i]['descri'] = $row['descri'];
        $form['rows'][$i]['tiprig'] = $row['tiprig'];
        $form['rows'][$i]['codart'] = $row['codart'];
        $form['rows'][$i]['pervat'] = $row['pervat'];
        $form['rows'][$i]['unimis'] = $row['unimis'];
        $form['rows'][$i]['prelis'] = $row['prelis'];
        $form['rows'][$i]['sconto'] = $row['sconto'];
        $form['rows'][$i]['quanti'] = gaz_format_quantity($row['quanti'],0,$admin_aziend['decimal_quantity']);
        $form['rows'][$i]['codvat'] = $row['codvat'];
        $form['rows'][$i]['codric'] = $row['codric'];
        $form['rows'][$i]['id_mag'] = $row['id_mag'];
        $form['rows'][$i]['annota'] = $articolo['annota'];
        $form['rows'][$i]['pesosp'] = $articolo['peso_specifico'];
        $form['rows'][$i]['lot_or_serial'] = $articolo['lot_or_serial'];
        $form['rows'][$i]['status'] = "UPDATE";
        // recupero eventuale movimento di tracciabilità
        $form['lotmag'][$i] = gaz_dbi_get_row($gTables['lotmag'],'id_purchase',$row['id_rig']);
        $i++;
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['tipdoc'] = $_GET['tipdoc'];
    $form['hidden_req'] = '';
    $form['id_tes'] = "";
    $form['gioemi'] = date("d");
    $form['mesemi'] = date("m");
    $form['annemi'] = date("Y");
    if (substr($form['tipdoc'],0,1) == 'A') { //un documento d'acquisto ricevuto (non fiscale) imposto l'ultimo giorno del mese in modo da evidenziare un eventuale errore di mancata introduzione manuale del dato
       $utstra = mktime(0,0,0,date("m")+1,date("d"),date("Y"));
    } else {
       $utstra = mktime(0,0,0,date("m"),date("d"),date("Y"));
    }
    $form['giotra'] = date("d",$utstra);
    $form['mestra'] = date("m",$utstra);
    $form['anntra'] = date("Y",$utstra);
    $form['oratra'] = date("H");
    $form['mintra'] = date("i");
    $form['rows'] = array();
    // tracciabilità
    $form['lotmag']=array();
    // fine tracciabilità
    $i = 0;
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    $form['in_artsea'] = $admin_aziend['artsea'];
    $form['in_codart'] = "";
    $form['in_pervat'] = "";
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    $form['in_codric'] = $admin_aziend['impacq'];
    if ($form['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
       $form['in_codric'] = $admin_aziend['purchases_return'];
    }
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_pesosp'] = 0;
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['search']['clfoco']='';
    $form['cosear'] = "";
    if (isset($_GET['seziva'])) {
         $form['seziva'] = $_GET['seziva'];
    } else {
         $form['seziva'] = 1;
    }
    $form['protoc'] = "";
    $form['numdoc'] = "";
    $form['numfat'] = "";
    $form['datfat'] = "";
    $form['clfoco'] = "";
    $form['pagame'] = "";
    $form['change_pag'] = "";
    $form['banapp'] = "";
    $form['vettor'] = "";
    $form['listin'] = "";
    $form['destin'] = "";
    $form['id_des'] = "";
    $form['search']['id_des']='';
    $form['spediz'] = "";
    $form['portos'] = "";
    $form['imball'] = "";
    $form['traspo'] = 0.00;
    $form['numrat'] = 1;
    $form['speban'] = 0;
    $form['spevar'] = 0;
    if ($admin_aziend['preeminent_vat'] > 0) {
       $form['ivaspe'] = $admin_aziend['preeminent_vat'];
    } else {
       $form['ivaspe'] = 1;
    }
    $result = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['ivaspe']);
    $form['pervat'] = $result['aliquo'];
    $form['cauven'] = 0;
    $form['caucon'] = '';
    $form['caumag'] = 0;
    $form['id_agente'] = 0;
    $form['id_pro'] = 0;
    $form['sconto'] = 0;
    $fornitore['indspe']="";
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
                                  'jquery/autocomplete_anagra',
                                  'jquery/ui/jquery.ui.dialog',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.draggable',
                                  'jquery/ui/jquery.ui.resizable',
                                  'jquery/ui/jquery.ui.datepicker.min',
                                  'jquery/ui/jquery.effects.core',
                                  'jquery/ui/jquery.effects.scale',
                                  'jquery/modal_form'));

echo '<script type="text/javascript" src="./dialog_lotmag.js"></script>';
?>
<script language="JavaScript">
function pulldown_menu(selectName, destField)
{
    // Create a variable url to contain the value of the
    // selected option from the the form named broven and variable selectName
    var url = document.docacq[selectName].options[document.docacq[selectName].selectedIndex].value;
    document.docacq[destField].value = url;
}
</script>
<SCRIPT LANGUAGE="JavaScript" ID="datapopup">
var cal = new CalendarPopup();
cal.setReturnFunction("setMultipleValues");
function setMultipleValues(y,m,d) {
  document.docacq.anntra.value=y;
  document.docacq.mestra.value=LZ(m);
  document.docacq.giotra.value=LZ(d);
  }
</SCRIPT>
<?php
if ($form['id_tes'] > 0 and substr($form['tipdoc'],0,1) == 'D') {
   $title = ucfirst($script_transl[$toDo].$script_transl[0][$form['tipdoc']])." n.".$form['numdoc'];
} elseif ($form['id_tes'] > 0) {
   $title = ucfirst($script_transl[$toDo].$script_transl[0][$form['tipdoc']])." prot.".$form['protoc'];
} else {
   $title = ucfirst($script_transl[$toDo].$script_transl[0][$form['tipdoc']]);
}
echo "<script type=\"text/javascript\">";
foreach ($form['rows'] as $k => $v) {
  if ($v['tiprig'] > 5 || $v['tiprig'] < 9 ){
     echo "\n// Initialize TinyMCE with the new plugin and menu button
          tinyMCE.init({
          mode : \"specific_textareas\",
          theme : \"advanced\",
          forced_root_block : false,
          force_br_newlines : true,
          force_p_newlines : false,
          elements : \"row_".$k."\",
          plugins : \"table,advlink\",
          theme_advanced_buttons1 : \"mymenubutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,|,link,unlink,code,|,formatselect,forecolor,backcolor,|,tablecontrols\",
          theme_advanced_buttons2 : \"\",
          theme_advanced_buttons3 : \"\",
          theme_advanced_toolbar_location : \"external\",
          theme_advanced_toolbar_align : \"left\",
          editor_selector  : \"mceClass".$k."\",
          });\n";
  }
  echo '   $( "#dialog_open'.$i.'").dialog({
              autoOpen: false
           });
        ';

}
echo "\n</script>\n";

echo "<form method=\"POST\" name=\"docacq\">\n";
$gForm = new gazieForm();

// INIZIO creazione dialog-lotmag 
foreach ($form['rows'] as $i => $v) {
    if ($v['lot_or_serial'] >= 1) {
        echo '
        <div id="lm_post_container_'.$i.'">';
            echo '<div id="lm_post_'.$i.'">
                  </div>
                 ';
        echo '</div>
        ';
        echo '
        <div id="lotmag_last_id'.$i.'" value="LOTMAGVALUE"></div>
        ';
        $partnersel='descrizione_qui';
        echo '<div id="dialog_open'.$i.'" partner="'.$partnersel.'" title="Apertura: '.$partnersel.' - '.$admin_aziend['html_symbol'].'">';
        echo '<p class="validateTips"></p>
        <table id="lm_form_container_'.$i.'" class="ui-widget ui-widget-content" width="800">
        <tbody>';
        echo '
             </tbody>
            </table>
            <table  width="800" id="db-contain'.$i.'" class="ui-widget ui-widget-content">
             <tbody>
             </tbody>
            </table>
        </div>
        ';
    }
}
// FINE creazione form dialog-lotmag

echo "<input type=\"hidden\" name=\"".ucfirst($toDo)."\" value=\"\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"{$form['id_tes']}\" name=\"id_tes\">\n";
echo "<input type=\"hidden\" value=\"{$form['seziva']}\" name=\"seziva\">\n";
echo "<input type=\"hidden\" value=\"{$form['tipdoc']}\" name=\"tipdoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['ritorno']}\" name=\"ritorno\">\n";
echo "<input type=\"hidden\" value=\"{$form['change_pag']}\" name=\"change_pag\">\n";
echo "<input type=\"hidden\" value=\"{$form['protoc']}\" name=\"protoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['numdoc']}\" name=\"numdoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['datfat']}\" name=\"datfat\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">$title ";
$select_fornitore = new selectPartner("clfoco");
$select_fornitore->selectDocPartner('clfoco',$form['clfoco'],$form['search']['clfoco'],'clfoco',$script_transl['mesg'],$admin_aziend['masfor']);
echo "</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"seziva\" class=\"FacetSelect\">\n";
for ($counter = 1; $counter <= 3; $counter++) {
    $selected="";
    if ($form["seziva"] == $counter) {
       $selected = " selected ";
    }
    echo "<option value=\"".$counter."\"".$selected.">".$counter."</option>\n";
}
echo "</select></td>\n";
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
    echo '<td colspan="2" class="FacetDataTDred">'.$message."</td>\n";
} else {
    echo "<td class=\"FacetFieldCaptionTD\">$script_transl[5]</td><td>".$fornitore['indspe']."<br />";
    echo "</td>\n";
}
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[6]</td><td class=\"FacetDataTD\">\n";
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
echo "\t <select name=\"annemi\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for( $counter = $form['annemi']-10; $counter <= $form['annemi']+10; $counter++ )
    {
    $selected = "";
    if($counter == $form['annemi'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }
echo "\t </select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[7]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"listin\" class=\"FacetSelect\">\n";
for ($lis = 1; $lis <= 3; $lis++) {
    $selected="";
    if ($form["listin"] == $lis) {
        $selected = " selected ";
    }
    echo "<option value=\"".$lis."\"".$selected.">".$lis."</option>\n";
}
echo "</select></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[8]</td><td  class=\"FacetDataTD\">\n";
$select_pagame = new selectpagame("pagame");
$select_pagame -> addSelected($form["pagame"]);
$select_pagame -> output();
echo "</td><td class=\"FacetFieldCaptionTD\">$script_transl[9]</td><td  class=\"FacetDataTD\">\n";
$select_banapp = new selectbanapp("banapp");
$select_banapp -> addSelected($form["banapp"]);
$select_banapp -> output();
echo "</td></tr>\n";
echo "<tr>\n";
if (substr($form['tipdoc'],0,1) == 'A') { // documento d'acquisto ricevuto (non fiscale)
   echo "<td colspan=\"3\" class=\"FacetFieldCaptionTD\" align=\"right\">".$script_transl[0][$form['tipdoc']]." ".$script_transl[52]." </td>\n";
   echo "<td><input type=\"text\" name=\"numfat\" value=\"".$form['numfat']."\" maxlength=\"20\" size=\"20\"></td>\n";
   echo "<td class=\"FacetFieldCaptionTD\">$script_transl[6]</td>";
   echo "<td class=\"FacetDataTD\"><INPUT TYPE=\"text\" NAME=\"giotra\" VALUE=\"".$form['giotra']."\" size=\"2\">\n";
   echo "<INPUT TYPE=\"text\" NAME=\"mestra\" VALUE=\"".$form['mestra']."\" size=\"2\">\n";
   echo "<INPUT TYPE=\"text\" NAME=\"anntra\" VALUE=\"".$form['anntra']."\" size=\"2\">\n";
   echo "<A HREF=\"#\" onClick=\"cal.showCalendar('anchor','".$form['mestra']."/".$form['giotra']."/".$form['anntra']."'); return false;\" TITLE=\" cambia la data! \" NAME=\"anchor\" ID=\"anchor\">\n";
   echo "<img border=\"0\" src=\"../../library/images/cal.png\"></A>";
   echo "<input type=\"hidden\" value=\"".$form['vettor']."\" name=\"vettor\">\n";
   echo "<input type=\"hidden\" value=\"".$form['imball']."\" name=\"imball\">\n";
   echo "<input type=\"hidden\" value=\"".$form['id_des']."\" name=\"id_des\">\n";
   echo "<input type=\"hidden\" value=\"".$form['destin']."\" name=\"destin\">\n";
} else { //documento fiscale (DDR,DDL)
   echo "<td></td><td></td>\n";
   echo "<td class=\"FacetFieldCaptionTD\">$script_transl[10]</td>\n";
   echo "<input type=\"hidden\" value=\"".$form['numfat']."\" name=\"numfat\">\n";
   if ($form['id_des'] > 0) {
      echo "<td class=\"FacetDataTD\">\n";
      $select_id_des = new selectPartner('id_des');
      $select_id_des->selectDocPartner('id_des','id_'.$form['id_des'],$form['search']['id_des'],'id_des',$script_transl['mesg'],$admin_aziend['masfor']);
      echo "<input type=\"hidden\" name=\"destin\" value=\"".$form['destin']."\">\n";
   } else {
      echo "<td class=\"FacetDataTD\"><textarea rows=\"1\" cols=\"30\" name=\"destin\" class=\"FacetInput\">".$form["destin"]."</textarea></td>\n";
      echo "<input type=\"hidden\" name=\"id_des\" value=\"".$form['id_des']."\"></td>\n";
      echo "<input type=\"hidden\" name=\"search[id_des]\" value=\"".$form['search']['id_des']."\">\n";
   }
   echo "<td class=\"FacetFieldCaptionTD\">$script_transl[14]</td>";
   echo "<td  class=\"FacetDataTD\">\n";
   $select_vettor = new selectvettor("vettor");
   $select_vettor -> addSelected($form["vettor"]);
   $select_vettor -> output();
}
echo "</td></tr></table>\n";
echo "<div class=\"FacetSeparatorTD\" align=\"center\">$script_transl[1]</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<input type=\"hidden\" value=\"{$form['in_descri']}\" name=\"in_descri\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_pervat']}\" name=\"in_pervat\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_unimis']}\" name=\"in_unimis\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_prelis']}\" name=\"in_prelis\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_id_mag']}\" name=\"in_id_mag\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_annota']}\" name=\"in_annota\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_pesosp']}\" name=\"in_pesosp\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_status']}\" name=\"in_status\" />\n";
echo "<tr><td class=\"FacetColumnTD\">$script_transl[15]: ";
$select_artico = new selectartico("in_codart");
$select_artico -> addSelected($form['in_codart']);
$select_artico -> output($form['cosear'],$form['in_artsea']);
echo "ricerca per <select name=\"in_artsea\" class=\"FacetDataTDsmall\">\n";
$selArray = array('C'=>'Codice articolo', 'B'=>'Codice a barre','D'=>'Descrizione');
foreach ($selArray as $key => $value) {
    $selected="";
    if(isset($form["in_artsea"]) and $form["in_artsea"] == $key) {
        $selected = " selected ";
    }
    echo "<option value=\"$key\" $selected > $value </option>";
}
echo "</select>\n";
echo "</TD><TD class=\"FacetColumnTD\">$script_transl[16]: <input type=\"text\" value=\"{$form['in_quanti']}\" maxlength=\"11\" size=\"7\" name=\"in_quanti\" tabindex=\"5\" accesskey=\"q\">\n";
echo "</TD><TD class=\"FacetColumnTD\" align=\"right\"><input type=\"image\" name=\"in_submit\" src=\"../../library/images/vbut.gif\" tabindex=\"6\" title=\"".$script_transl['submit'].$script_transl['thisrow']."!\">\n";
echo "</td></tr>\n";
echo "<tr><td class=\"FacetColumnTD\">$script_transl[17]:";
$gForm->selTypeRow('in_tiprig',$form['in_tiprig']);
echo $script_transl[18].": ";
$gForm->selectAccount('in_codric',$form['in_codric'],intval(substr($form['in_codric'],0,1)));
echo " %$script_transl[24]: <input type=\"text\" value=\"{$form['in_sconto']}\" maxlength=\"4\" size=\"1\" name=\"in_sconto\">";
echo "</TD><TD class=\"FacetColumnTD\"> $script_transl[19]: ";
$select_in_codvat = new selectaliiva("in_codvat");
$select_in_codvat -> addSelected($form["in_codvat"]);
$select_in_codvat -> output();
echo "</td><TD class=\"FacetColumnTD\"></TD></tr>\n";
$quatot= 0;
$totimpmer=0.00;
$totivafat=0.00;
$totimpfat=0.00;
echo "</table>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[20]</td><td colspan=\"2\" class=\"FacetFieldCaptionTD\">$script_transl[21]</td><td class=\"FacetFieldCaptionTD\">$script_transl[22]</td><td class=\"FacetFieldCaptionTD\">$script_transl[16]</td><td class=\"FacetFieldCaptionTD\">$script_transl[23]</td><td class=\"FacetFieldCaptionTD\">%".substr($script_transl[24],0,2)."</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[25]</td><td class=\"FacetFieldCaptionTD\">$script_transl[19]</td><td class=\"FacetFieldCaptionTD\">$script_transl[18]</td><td class=\"FacetFieldCaptionTD\"></td></tr>\n";
$castel=array();
foreach ($form['rows'] as $key => $value) {
    //calcolo il totale del peso in kg
    switch(strtolower($value['unimis']))
    {
    case "kg":
        $quatot = $value['quanti']+$quatot;
    break;
    }
    //creo il castelletto IVA
    $codice_vat = $value['codvat'];
    $tiporigo = $value['tiprig'];
    $descrizione=$value['descri'];
    //calcolo importo rigo
    if ($tiporigo == 0) {//se del tipo normale
        $imprig = CalcolaImportoRigo($form['rows'][$key]['quanti'], $form['rows'][$key]['prelis'], $form['rows'][$key]['sconto']);
    } elseif ($tiporigo == 1) {//ma se del tipo forfait
        $imprig = CalcolaImportoRigo(1, $form['rows'][$key]['prelis'], 0);
    }
    if ($tiporigo <= 1) {//ma solo se del tipo normale o forfait
       if (!isset($castel[$codice_vat])){
          $castel[$codice_vat] = "0.00";
       }
       $castel[$codice_vat] = number_format(($castel[$codice_vat]+ $imprig),2,'.','');
    }
    if ($form['rows'][$key]['tiprig'] == 1)
        $imprig = number_format($form['rows'][$key]['prelis'], 2 , '.', '');
        echo "<input type=\"hidden\" value=\"{$value['codart']}\" name=\"rows[{$key}][codart]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['status']}\" name=\"rows[{$key}][status]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['tiprig']}\" name=\"rows[{$key}][tiprig]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['codvat']}\" name=\"rows[{$key}][codvat]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['pervat']}\" name=\"rows[{$key}][pervat]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['codric']}\" name=\"rows[{$key}][codric]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['id_mag']}\" name=\"rows[{$key}][id_mag]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['annota']}\" name=\"rows[{$key}][annota]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['pesosp']}\" name=\"rows[{$key}][pesosp]\">\n";
        echo "<input type=\"hidden\" value=\"{$value['lot_or_serial']}\" name=\"rows[{$key}][lot_or_serial]\">\n";
        //stampo i righi in modo diverso a secondo del tipo
        switch($value['tiprig']) {
        case "0":
        echo "<tr>";
        if ( file_exists ( "../../data/files/fotoart/".$value["codart"].".gif" ) ) {
			$boxover = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$value['annota']."] body=[<center><img width='50%' height='50%' src='../../data/files/fotoart/".$value["codart"].".gif'>] fade=[on] fadespeed=[0.03] \"";		
		} else {
			$boxover = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[{$value['annota']}] body=[<center><img src='../root/view.php?table=artico&value=".$value['codart']."'>] fade=[on] fadespeed=[0.03] \"";
		}
        if ($value['pesosp'] != 0){
            $boxpeso = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[quantit&agrave; &divide; peso specifico = ".gaz_format_number($value['quanti'] /  $value['pesosp'])."]  fade=[on] fadespeed=[0.03] \"";
        } else {
            $boxpeso = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[peso specifico = 0]  fade=[on] fadespeed=[0.03] \"";
        }
        echo "<td title=\"".$script_transl['update'].$script_transl['thisrow']."!\"> <input type=\"image\" name=\"upper_row[{$key}]\" src=\"../../library/images/upp.png\" title=\"".$script_transl['3']."!\" /> <input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"".$value['codart']."\" /></td>\n";
        echo "<td $boxover><input type=\"text\" name=\"rows[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td><td>\n";
        if ($value['lot_or_serial'] >= 1) {
            echo "<input type=\"image\" onclick=\"dialogLotmag(this);return false;\" href=\"#\" id=\"lotmag".$key."\" src=\"../../library/images/trace.png\" />\n";
        }
        echo "</td>\n";
        echo "<td $boxpeso><input type=\"text\" name=\"rows[{$key}][unimis]\" value=\"{$value['unimis']}\" maxlength=\"3\" size=\"1\" /></td>\n";
        echo "<td $boxpeso><input type=\"text\" name=\"rows[{$key}][quanti]\" value=\"{$value['quanti']}\" align=\"right\" maxlength=\"11\" size=\"4\" onchange=\"this.form.submit()\" />\n";
        echo "</td>\n";
        echo "<td><input type=\"text\" name=\"rows[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" onchange=\"this.form.submit()\" /></td>\n";
        echo "<td><input type=\"text\" name=\"rows[{$key}][sconto]\" value=\"{$value['sconto']}\" maxlength=\"4\" size=\"1\" onchange=\"this.form.submit()\" /></td>\n";
        echo "<td align=\"right\">".gaz_format_number($imprig)."</td>\n";
        echo "<td>{$value['pervat']}%</td>\n";
        echo "<td>".$value['codric']."</td>\n";
        break;
        case "1":
        echo "<td title=\"".$script_transl['update'].$script_transl['thisrow']."!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* forfait *\" /></td>\n";
        echo "<td><input type=\"text\"   name=\"rows[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td><td><input type=\"image\" name=\"upper_row[{$key}]\" src=\"../../library/images/upp.png\" title=\"".$script_transl['3']."!\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][unimis]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][quanti]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][sconto]\" value=\"\" /></td>\n";
        echo "<td></td>\n";
        echo "<td align=\"right\"><input type=\"text\" name=\"rows[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" onchange=\"this.form.submit()\" /></td>\n";
        echo "<td>{$value['pervat']}%</td>\n";
        echo "<td>".$value['codric']."</td>\n";
        break;
        case "2":
        echo "<td title=\"".$script_transl['update'].$script_transl['thisrow']."!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* descrittivo *\" /></td>\n";
        echo "<td><input type=\"text\"   name=\"rows[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td><td><input type=\"image\" name=\"upper_row[{$key}]\" src=\"../../library/images/upp.png\" title=\"".$script_transl['3']."!\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][unimis]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][quanti]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][prelis]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][sconto]\" value=\"\" /></td>\n";
        echo "<td></td>\n";
        echo "<td></td>\n";
        echo "<td></td>\n";
        break;
        case "3":
        echo "<td title=\"".$script_transl['update'].$script_transl['thisrow']."!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* var.tot.fattura *\" /></td>\n";
        echo "<td><input type=\"text\"   name=\"rows[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\"></td><td><input type=\"image\" name=\"upper_row[{$key}]\" src=\"../../library/images/upp.png\" title=\"".$script_transl['3']."!\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][unimis]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][quanti]\" value=\"\" /></td>\n";
        echo "<td><input type=\"hidden\" name=\"rows[{$key}][sconto]\" value=\"\" /></td>\n";
        echo "<td></td>\n";
        echo "<td align=\"right\"><input type=\"text\" name=\"rows[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" /></td>\n";
        echo "<td></td>\n";
        echo "<td></td>\n";
        break;
        case "6":
        case "7":
        case "8":
        echo "<td title=\"".$script_transl['update'].$script_transl['thisrow']."!\">
              <input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[$key]\" value=\"".$script_transl['typerow'][$value['tiprig']]."\" /></td>\n";
        echo "<td colspan=\"10\"><textarea id=\"row_$key\" name=\"row_$key\" class=\"mceClass$key\" style=\"width:100%;height:100px;\">".$form["row_$key"]."</textarea></td>\n";
        echo "<input type=\"hidden\" name=\"rows[$key][descri]\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"rows[$key][unimis]\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"rows[$key][quanti]\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"rows[$key][prelis]\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"rows[$key][sconto]\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"rows[$key][provvigione]\" value=\"\" /></td>\n";
        break;
        }
        echo "<TD align=\"right\"><input type=\"image\" name=\"del[{$key}]\" src=\"../../library/images/xbut.gif\" title=\"".$script_transl['delete'].$script_transl['thisrow']."!\" /></td></tr>\n";
    }
echo "</table>\n";
echo "<div class=\"FacetSeparatorTD\" align=\"center\">$script_transl[2]</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<input type=\"hidden\" value=\"{$form['speban']}\" name=\"speban\">\n";
echo "<input type=\"hidden\" value=\"{$form['traspo']}\" name=\"traspo\">\n";
echo "<input type=\"hidden\" value=\"{$form['numrat']}\" name=\"numrat\">\n";
echo "<input type=\"hidden\" value=\"{$form['spevar']}\" name=\"spevar\">\n";
echo "<input type=\"hidden\" value=\"{$form['ivaspe']}\" name=\"ivaspe\">\n";
echo "<input type=\"hidden\" value=\"{$form['pervat']}\" name=\"pervat\">\n";
echo "<input type=\"hidden\" value=\"{$form['cauven']}\" name=\"cauven\">\n";
echo "<input type=\"hidden\" value=\"{$form['caucon']}\" name=\"caucon\">\n";
echo "<input type=\"hidden\" value=\"{$form['caumag']}\" name=\"caumag\">\n";
echo "<input type=\"hidden\" value=\"{$form['id_agente']}\" name=\"id_agente\">\n";
echo "<input type=\"hidden\" value=\"{$form['id_pro']}\" name=\"id_pro\">\n";
//inizio piede
if (substr($form['tipdoc'],0,1) == 'A') { //piede adatto ad un documento d'acquisto ricevuto (non fiscale)
  echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[27]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" name=\"spediz\" value=\"".$form["spediz"]."\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
  $select_spediz = new SelectValue("spedizione");
  $select_spediz -> output('spediz', 'spediz');
  echo "</td><td class=\"FacetFieldCaptionTD\">$script_transl[29]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" name=\"portos\" value=\"".$form["portos"]."\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
  $select_spediz = new SelectValue("portoresa");
  $select_spediz -> output('portos', 'portos');
  echo "<td class=\"FacetFieldCaptionTD\">".$script_transl[51]."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
  echo "<select name=\"caumag\" class=\"FacetSelect\">\n";
  $result = gaz_dbi_dyn_query("*", $gTables['caumag']," clifor = 1 AND operat = ".$docOperat[$form['tipdoc']],"codice asc, descri asc");
  while ($row = gaz_dbi_fetch_array($result)) {
    $selected="";
    if($form["caumag"] == $row['codice']) {
       $selected = " selected ";
    }
    echo "<option value=\"".$row['codice']."\"".$selected.">".$row['codice']."-".substr($row['descri'],0,20)."</option>\n";
  }
  echo "</select></tr>\n";
  echo "<input type=\"hidden\" value=\"00\" name=\"oratra\">\n";
  echo "<input type=\"hidden\" value=\"00\" name=\"mintra\">\n";
} else {  //piede adatto ad un documento d'acquisto emesso  (fiscale) es. DDT di reso o conto lavorazione
  echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[26]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" name=\"imball\" value=\"".$form["imball"]."\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
  $select_spediz = new SelectValue("imballo");
  $select_spediz -> output('imball', 'imball');
  echo "</td><td class=\"FacetFieldCaptionTD\">$script_transl[27]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" name=\"spediz\" value=\"".$form["spediz"]."\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
  $select_spediz = new SelectValue("spedizione");
  $select_spediz -> output('spediz', 'spediz');
  echo "</td><td class=\"FacetFieldCaptionTD\">$script_transl[28] ".$admin_aziend['symbol']."</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" value=\"".$form['traspo']."\" name=\"traspo\" maxlength=\"6\" size=\"3\" onchange=\"this.form.submit()\" >\n";
  echo "</td></tr>\n";
  echo "<tr>";
  echo "<td class=\"FacetFieldCaptionTD\">$script_transl[29]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><input type=\"text\" name=\"portos\" value=\"".$form["portos"]."\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
  $select_spediz = new SelectValue("portoresa");
  $select_spediz -> output('portos', 'portos');
  echo "<td class=\"FacetFieldCaptionTD\">$script_transl[30]</td>\n";
  echo "<td colspan=\"2\" class=\"FacetDataTD\"><INPUT class=\"FacetText\" TYPE=\"text\" NAME=\"giotra\" VALUE=\"".$form['giotra']."\" size=\"2\">\n";
  echo "<INPUT class=\"FacetText\" TYPE=\"text\" NAME=\"mestra\" VALUE=\"".$form['mestra']."\" size=\"2\">\n";
  echo "<INPUT class=\"FacetText\" TYPE=\"text\" NAME=\"anntra\" VALUE=\"".$form['anntra']."\" size=\"2\">\n";
  echo "<A HREF=\"#\" onClick=\"cal.showCalendar('anchor','".$form['mestra']."/".$form['giotra']."/".$form['anntra']."'); return false;\" TITLE=\" cambia la data! \" NAME=\"anchor\" ID=\"anchor\">\n";
  echo "<img border=\"0\" src=\"../../library/images/cal.png\"></A>$script_transl[31]";
  // select dell'ora
  echo "\t <select name=\"oratra\" class=\"FacetText\" >\n";
  for( $counter = 0; $counter <= 23; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['oratra'])
            $selected = "selected";
    echo "\t\t <option value=\"".sprintf('%02d',$counter)."\" $selected >".sprintf('%02d',$counter)."</option>\n";
    }
  echo "\t </select>\n ";
  // select dell'ora
  echo "\t <select name=\"mintra\" class=\"FacetText\" >\n";
  for( $counter = 0; $counter <= 59; $counter++ )
    {
    $selected = "";
    if($counter ==  $form['mintra'])
            $selected = "selected";
    echo "\t\t <option value=\"".sprintf('%02d',$counter)."\" $selected >".sprintf('%02d',$counter)."</option>\n";
    }
  echo "\t </select></td>\n";
  echo "<td class=\"FacetFieldCaptionTD\">".$script_transl[51]."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
  echo "<select name=\"caumag\" class=\"FacetSelect\">\n";
  $result = gaz_dbi_dyn_query("*", $gTables['caumag']," clifor = 1 AND operat = ".$docOperat[$form['tipdoc']],"codice asc, descri asc");
  while ($row = gaz_dbi_fetch_array($result)) {
    $selected="";
    if($form["caumag"] == $row['codice']) {
       $selected = " selected ";
    }
    echo "<option value=\"".$row['codice']."\"".$selected.">".$row['codice']."-".substr($row['descri'],0,20)."</option>\n";
  }
  echo "</select></tr>\n";
}
//fine piede
echo "<tr><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[32]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[33]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[34]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">%$script_transl[24]<input type=\"text\" name=\"sconto\" value=\"".$form["sconto"]."\" maxlength=\"6\" size=\"1\" onchange=\"this.form.submit()\"></td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[32]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[19]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[35]</td><td class=\"FacetFieldCaptionTD\" align=\"right\">$script_transl[36] ".$admin_aziend['symbol']."</td>\n";
$chk_add_iva_tes = 0;
$i=count($form['rows']);
foreach ($castel as $key => $value) {
      $result = gaz_dbi_get_row($gTables['aliiva'],"codice",$key);
      $impcast = CalcolaImportoRigo(1, $value, $form['sconto']);
      if ($key == $form['ivaspe']){
          $impcast += $form['traspo'] + $form['speban']*$form['numrat'] + $form['spevar'];
          $chk_add_iva_tes = 1;
      }
      $ivacast =  round($impcast * $result['aliquo'])/ 100;
      $totimpmer += $value;
      $totimpfat += $impcast;
      $totivafat += $ivacast;
      if ($i > 0) {
        echo "<tr><td align=\"right\">".number_format ($impcast,2, '.', '')."</td><td align=\"right\">".$result['descri']." ".number_format ($ivacast,2, '.', '')."</td>\n";
      }
}

if ($chk_add_iva_tes == 0){// se le spese della testata non sono state aggiunte perchè non si è incontrato uno stesso codice IVA
   $result = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['ivaspe']);
   $impcast = $form['traspo'] + $form['speban']*$form['numrat'] + $form['spevar'];
   $ivacast =  round($impcast * $result['aliquo'])/ 100;
   $totimpfat += $impcast;
   $totivafat += $ivacast;
   if ($i > 0) {
        echo "<tr><td align=\"right\">".number_format ($impcast,2, '.', '')."</td><td align=\"right\">".$result['descri']." ".number_format ($ivacast,2, '.', '')."</td>\n";
   }
   $chk_add_iva_tes = 1;
}

if ($i > 0) {
        echo "<td align=\"right\">".number_format ($totimpmer,2, '.', '')."</td>
               <td align=\"right\">".gaz_format_number (($totimpfat-$totimpmer-$form['traspo']-($form['speban']*$form['numrat'])-$form['spevar']),2, '.', '')."</td>
               <td align=\"right\">".number_format ($totimpfat,2, '.', '')."</td>
               <td align=\"right\">".number_format ($totivafat,2, '.', '')."</td>
               <td align=\"right\">".$quatot."</td>
               <td align=\"right\">".number_format (($totimpfat+$totivafat),2, '.', '')."</td>\n";
        if ($toDo == 'update') {
           echo '<td class="FacetFieldCaptionTD" align="right"><input type="submit" accesskey="m" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="MODIFICA !"></td></tr>';
        } else {
           echo '<td class="FacetFieldCaptionTD" align="right"><input type="submit" accesskey="i" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="INSERISCI !"></td></tr>';
        }
}
echo "</table><br />";
?>
</form>
</body>
</html>