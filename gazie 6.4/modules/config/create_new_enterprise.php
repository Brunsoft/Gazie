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
$admin_aziend=checkAdmin(9);
$msg = '';

function createNewTable($table,$new_id)
{
    global $table_prefix;
    $results = gaz_dbi_query ("SHOW CREATE TABLE ".$table);
    $row = gaz_dbi_fetch_array($results);
    return(preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$new_id),$row['Create Table']).";\n\n");
}

if (isset($_POST['ritorno'])) {   //se non e' il primo accesso
    $form['ritorno'] = $_POST['ritorno'];
    $form['codice'] = intval($_POST['codice']);
    $form['ref_co'] = intval($_POST['ref_co']);
    $form['clfoco'] = intval($_POST['clfoco']);
    $form['base_arch'] = intval($_POST['base_arch']);
    $form['artico_catmer'] = intval($_POST['artico_catmer']);
    if (isset($_POST['users'])){
       $form['users']=substr($_POST['users'],0,8);
       $where_user="enterprise_id = ".$form['ref_co'];
    } else {
       $form['users']='';
       $where_user="enterprise_id = ".$form['ref_co']." AND adminid = '".$_SESSION['Login']."'";
    }
    if (isset($_POST['Submit'])) { // conferma tutto
       //eseguo i controlli formali
       $code_exist = gaz_dbi_dyn_query('codice',$gTables['aziend'],"codice = ".$form['codice'],'codice DESC',0,1);
       $code = gaz_dbi_fetch_array($code_exist);
       if ($code) {
          $msg .= "1+";
       }
       if ($form['codice'] <= 0 || $form['codice'] > 999) {
          $msg .= "0+";
       }
       if (empty($msg)) { // nessun errore
          // prendo i dati dell'azienda di riferimento
          $ref_company = gaz_dbi_get_row($gTables['aziend'],'codice',$form['ref_co']);
          // richiamo le tabelle dall'azienda di riferimento richiesta
          $tables = gaz_dbi_query ("SHOW TABLES FROM $Database LIKE '".$table_prefix."\_".sprintf('%03d',$form['ref_co'])."%'");
          while ($r = gaz_dbi_fetch_array($tables)) {
                // CREO LA STRUTTURA DELLA TABELLA
                 $sql=createNewTable($r[0],$form['codice']);
                 gaz_dbi_query($sql);
                 if(preg_match("/[a-zA-Z0-9]*.aliiva$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.caumag$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.caucon$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.pagame$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.portos$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.spediz$/",$r[0])) { // queste tabelle le copio identiche anche con i dati provenienti dall'azienda di riferimento
                    switch ($form['base_arch']) {
                      case 0:  // SOLO STRUTTURA
                           break;
                      default: // POPOLO CON I DATI
                            $sql =" INSERT INTO `".preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$form['codice']),$r[0])."`  SELECT * FROM `".$r[0]."` ;\n\n";
                            gaz_dbi_query($sql);
                      break;
                    }
                 } elseif(preg_match("/[a-zA-Z0-9]*.imball$/",$r[0]) ||
                          preg_match("/[a-zA-Z0-9]*.vettor$/",$r[0])) { // per queste tabella mi baso sulla scelta dell'utente
                    switch ($form['base_arch']) {
                      case 2: // POPOLO CON I DATI
                           $sql =" INSERT INTO `".preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$form['codice']),$r[0])."`  SELECT * FROM `".$r[0]."` ;\n\n";
                           gaz_dbi_query($sql);
                           break;
                      default: // SOLO STRUTTURA
                      break;
                    }
                 } elseif(preg_match("/[a-zA-Z0-9]*.artico$/",$r[0]) ||
                    preg_match("/[a-zA-Z0-9]*.catmer$/",$r[0])) {
                    switch ($form['artico_catmer']) {
                      case 1: // POPOLO CON GLI ARTICOLI DI MAGAZZINO
                           $sql =" INSERT INTO `".preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$form['codice']),$r[0])."` SELECT * FROM `".$r[0]."` ;\n\n";
                           gaz_dbi_query($sql);
                           break;
                      default:  // SOLO STRUTTURA
                           break;
                    }
                 } elseif(preg_match("/[a-zA-Z0-9]*.clfoco$/",$r[0])) { // per la tabelle del piano dei conti mi baso sulla scelta dell'utente
                    switch ($form['clfoco']) {
                      case 0: // SOLO STRUTTURA
                           break;
                      case 1: // POPOLO CON I DATI
                           $sql =" INSERT INTO `".preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$form['codice']),$r[0])."` SELECT * FROM `".$r[0]."`
                                 WHERE (codice < ".($ref_company['mascli']*1000000+1)." OR codice > ".($ref_company['mascli']*1000000+999999).") AND
                                       (codice < ".($ref_company['masfor']*1000000+1)." OR codice > ".($ref_company['masfor']*1000000+999999).") AND
                                       (codice < ".($ref_company['masban']*1000000+1)." OR codice > ".($ref_company['masban']*1000000+999999).");\n\n";
                           gaz_dbi_query($sql);
                           break;
                      case 2: // POPOLO CON I DATI BANCHE, CLIENTI, FORNITORI
                           $sql =" INSERT INTO  `".preg_replace("/$table_prefix\_[0-9]{3}/",$table_prefix.sprintf('_%03d',$form['codice']),$r[0])."` SELECT * FROM `".$r[0]."` ;\n\n";
                           gaz_dbi_query($sql);
                           break;
                    }
                 }
          }
          // inserisco la nuova azienda nel suo archivio con una descrizione da modificare manualmente
          $upd='Modificare i dati di questa azienda';
          $new_company=$ref_company;
          $new_company['codice']=$form['codice'];
          $new_company['ragso1']='N.'.$form['codice'].' AZIENDA NUOVA - NEW COMPANY';
          $new_company['ragso2']=$upd;
          $new_company['image']='';
          $new_company['sedleg']='';
          $new_company['legrap']='';
          $new_company['luonas']='';
          $new_company['pronas']='';
          $new_company['indspe']=$upd;
          $new_company['capspe']='';
          $new_company['citspe']=$upd;
          $new_company['prospe']='';
          $new_company['telefo']='';
          $new_company['fax']='';
          $new_company['e_mail']=$upd;
          $new_company['codfis']='00000000000';
          $new_company['pariva']=0;
          $new_company['rea']='INSERIRE REA';
          $new_company['upgrie']=0;
          $new_company['upggio']=0;
          $new_company['upginv']=0;
          $new_company['upgve1']=0;
          $new_company['upgve2']=0;
          $new_company['upgve3']=0;
          $new_company['upgac1']=0;
          $new_company['upgac2']=0;
          $new_company['upgac3']=0;
          $new_company['upgco1']=0;
          $new_company['upgco2']=0;
          $new_company['upgco3']=0;
          gaz_dbi_table_insert('aziend',$new_company);
          // procedo all'abilitazione degli utenti in base alla scelta fatta dal'operatore
          $user_abilit = gaz_dbi_dyn_query('*',$gTables['admin_module'],$where_user,'moduleid');
          while ($r = gaz_dbi_fetch_array($user_abilit)) {
                 $r['enterprise_id']=$form['codice'];
                 gaz_dbi_table_insert('admin_module',$r);
          }
          changeEnterprise($form['codice']);
          header("Location: admin_aziend.php?Update&codice=".$form['codice']);
          exit;
       }
    } elseif (isset($_POST['Return'])) { // torno indietro
          header("Location: ".$form['ritorno']);
          exit;
    }
} else { //se e' il primo accesso
    $form['ritorno']=$_SERVER['HTTP_REFERER'];
    $rs_last = gaz_dbi_dyn_query('codice',$gTables['aziend'],1,'codice DESC',0,1);
    $last = gaz_dbi_fetch_array($rs_last);
    $form['codice'] = $last['codice']+1;
    $form['ref_co'] = 0;
    $form['clfoco'] = 1;
    $form['base_arch'] = 1;
    $form['artico_catmer'] = 0;
    $form['users']=true;
}

require("../../library/include/header.php");
$script_transl = HeadMain();
echo "<form method=\"POST\">";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"".$form['ritorno']."\">\n";
$gForm = new GAzieForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title']."</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="3" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['codice']."* </td>\n";
echo "\t<td class=\"FacetDataTD\" colspan=\"2\"><input type=\"text\" name=\"codice\" value=\"".$form['codice']."\" align=\"right\" maxlength=\"3\" size=\"3\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['ref_co']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->selectFromDB('aziend','ref_co','codice',$form['ref_co'],'codice',0,' - ','ragso1');
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['clfoco']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->variousSelect('clfoco',$script_transl['clfoco_value'],$form['clfoco']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['base_arch']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->variousSelect('base_arch',$script_transl['base_arch_value'],$form['base_arch']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['artico_catmer']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->variousSelect('artico_catmer',$script_transl['artico_catmer_value'],$form['artico_catmer']);
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['users']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->selCheckbox('users',$form['users']);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['sqn']."</td>";
echo "\t </td>\n";
echo "\t<td  class=\"FacetDataTD\">\n";
echo '<input name="Return" type="submit" value="'.$script_transl['return'].'!">';
echo "\t </td>\n";
echo "\t<td  class=\"FacetDataTD\" align=\"right\">\n";
echo '<input name="Submit" type="submit" value="'.strtoupper($script_transl['submit']).'!">';
echo "\t </td>\n";
echo "</tr>\n";
?>
</table>
</form>
</body>
</html>