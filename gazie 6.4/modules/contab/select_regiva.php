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
$msg='';


function getPage_ini($vat_section,$vat_reg)
{
    global $admin_aziend;
    $page=1;
    switch($vat_reg) {
       case 2:
       $page=$admin_aziend["upgve$vat_section"]+1;
    break;
       case 4:
       $page=$admin_aziend["upgco$vat_section"]+1;
    break;
       case 6:
       $page=$admin_aziend["upgac$vat_section"]+1;
    break;
    }
    return $page;
}

function getMovements($vat_section,$vat_reg,$date_ini,$date_fin)
{
        global $gTables,$admin_aziend;
        $m=array();
        $where = "datreg BETWEEN $date_ini AND $date_fin AND seziva = $vat_section AND regiva = $vat_reg";
        $orderby="datreg, protoc";
        $rs=gaz_dbi_dyn_query("YEAR(datreg) AS ctrl_sr,
                      DATE_FORMAT(datdoc,'%d-%m-%Y') AS dd,
                      DATE_FORMAT(datreg,'%d-%m-%Y') AS dr,
                      CONCAT(".$gTables['anagra'].".ragso1, ' ',".$gTables['anagra'].".ragso2) AS ragsoc,clfoco,codiva,
                      protoc,numdoc,datreg,caucon,regiva,operat,imponi,impost,periva,
                      ".$gTables['tesmov'].".descri AS descri,
                      ".$gTables['aliiva'].".descri AS desvat,
                      ".$gTables['tesmov'].".id_tes AS id_tes,
                      ".$gTables['aliiva'].".tipiva AS tipiva",
        $gTables['rigmoi']." LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoi'].".id_tes = ".$gTables['tesmov'].".id_tes
        LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['tesmov'].".clfoco = ".$gTables['clfoco'].".codice
        LEFT JOIN ".$gTables['anagra']." ON ".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra
        LEFT JOIN ".$gTables['aliiva']." ON ".$gTables['rigmoi'].".codiva = ".$gTables['aliiva'].".codice",$where,$orderby);
        $c_sr=0;
        $c_id=0;
        $c_p=0;
        $c_ndoc=array();
        while ($r=gaz_dbi_fetch_array($rs)) {
            // inizio controllo errori di numerazione
            if (empty($r['tipiva'])){  // errore: aliquota IVA non tipizzata
                   $r['err_t']='ERROR';
            }
            if ($c_sr!=($r['ctrl_sr'])){ // devo azzerare tutto perch� � cambiato l'anno
                $c_sr=0;
                $c_id=0;
                $c_p=0;
                $c_ndoc=array();
                if ($r['protoc']<>1){ // errore: il protocollo non � 1
                   // non lo rilevo in quanto i registri IVA non sono annuali
                }
            } else {
               $ex=$c_p+1;
               if ($r['protoc']<>$ex && $r['id_tes']<>$c_id){  // errore: il protocollo non � consecutivo
                   $r['err_p']=$ex;
               }
            }
            if ($r['regiva']<4){ // il controllo sul numero solo per i registri delle fatture
               if ($r['caucon'] == 'FAD'){
                   $r['caucon'] = 'FAI';
               }
               if (isset($c_ndoc[$r['caucon']])){ // controllo se il numero precedente � questo-1
                  $ex=$c_ndoc[$r['caucon']]+1;
                  if ($r['numdoc']<>$ex && $c_id<>$r['id_tes']){  // errore: il numero non � consecutivo
                     $r['err_n']=$ex;
                  }
               } else {  // dal primo documento di questo tipo ci si aspetta il n.1
                  if ($r['numdoc']<>1){ // errore: il numero non � 1
                       // non lo rilevo in quanto i registri IVA non sono annuali
                  }
               }
            }
            $c_ndoc[$r['caucon']]=$r['numdoc'];
            $c_sr=$r['ctrl_sr'];
            $c_id=$r['id_tes'];
            $c_p=$r['protoc'];
            // fine controllo errori di numerazione
            $m[] = $r;
        }
        return $m;
}


if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    require("lang.".$admin_aziend['lang'].".php");
    if ($admin_aziend['ivam_t'] == 'M') {
       $utsdatini = mktime(0,0,0,date("m")-1,1,date("Y"));
       $utsdatfin = mktime(0,0,0,date("m"),0,date("Y"));
    } elseif (date("m") >= 1 and date("m") < 4) {
       $utsdatini = mktime(0,0,0,10,1,date("Y")-1);
       $utsdatfin = mktime(0,0,0,12,31,date("Y")-1);
    } elseif (date("m") >= 4 and date("m") < 7) {
       $utsdatini = mktime(0,0,0,1,1,date("Y"));
       $utsdatfin = mktime(0,0,0,3,31,date("Y"));
    } elseif (date("m") >= 7 and date("m") < 10) {
       $utsdatini = mktime(0,0,0,4,1,date("Y"));
       $utsdatfin = mktime(0,0,0,6,31,date("Y"));
    } elseif (date("m") >= 10 and date("m") <= 12) {
       $utsdatini = mktime(0,0,0,7,1,date("Y"));
       $utsdatfin = mktime(0,0,0,9,30,date("Y"));
    }
    $form['jump']='jump';
    $form['date_ini_D']=1;
    $form['date_ini_M']=date("m",$utsdatini);
    $form['date_ini_Y']=date("Y",$utsdatini);
    $form['date_fin_D']=date("d",$utsdatfin);
    $form['date_fin_M']=date("m",$utsdatfin);
    $form['date_fin_Y']=date("Y",$utsdatfin);
    $form['vat_section']=1;
    $form['vat_reg']=1;
    $form['sta_def']=false;
    $form['sem_ord']=$admin_aziend['regime'];
    $form['cover']=false;
    $form['page_ini'] = getPage_ini(1,2);
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['date_ini_D']=intval($_POST['date_ini_D']);
    $form['date_ini_M']=intval($_POST['date_ini_M']);
    $form['date_ini_Y']=intval($_POST['date_ini_Y']);
    $form['date_fin_D']=intval($_POST['date_fin_D']);
    $form['date_fin_M']=intval($_POST['date_fin_M']);
    $form['date_fin_Y']=intval($_POST['date_fin_Y']);
    $form['vat_section']=intval($_POST['vat_section']);
    $form['vat_reg']=intval($_POST['vat_reg']);
    if (isset($_POST['sta_def'])){
       $form['sta_def']=substr($_POST['sta_def'],0,8);
    } else {
       $form['sta_def']='';
    }
    if (isset($_POST['jump'])){
       $form['jump']=substr($_POST['jump'],0,8);
    } else {
       $form['jump']='';
    }
    $form['sem_ord']=substr($_POST['sem_ord'],0,1);
    if (isset($_POST['cover'])){
       $form['cover']=substr($_POST['cover'],0,8);
    } else {
       $form['cover']='';
    }
    if ($form['hidden_req']=='vat_reg' || $form['hidden_req']=='vat_section'){
       require("lang.".$admin_aziend['lang'].".php");
       $form['page_ini'] = getPage_ini($form['vat_section'],$form['vat_reg']);
       $form['hidden_req']='';
    } else {
       $form['page_ini'] = intval($_POST['page_ini']);
    }
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
}

//controllo i campi
if (!checkdate( $form['date_ini_M'], $form['date_ini_D'], $form['date_ini_Y']) ||
    !checkdate( $form['date_fin_M'], $form['date_fin_D'], $form['date_fin_Y'])) {
    $msg .='0+';
}
$utsini= mktime(0,0,0,$form['date_ini_M'],$form['date_ini_D'],$form['date_ini_Y']);
$utsfin= mktime(0,0,0,$form['date_fin_M'],$form['date_fin_D'],$form['date_fin_Y']);
if ($utsini > $utsfin) {
    $msg .='1+';
}
// fine controlli

if (isset($_POST['print']) && $msg=='') {
    $_SESSION['print_request']=array('script_name'=>'stampa_regiva',
                                     'vs'=>$form['vat_section'],
                                     'vr'=>$form['vat_reg'],
                                     'jp'=>$form['jump'],
                                     'pi'=>$form['page_ini'],
                                     'sd'=>$form['sta_def'],
                                     'so'=>$form['sem_ord'],
                                     'cv'=>$form['cover'],
                                     'ri'=>date("dmY",$utsini),
                                     'rf'=>date("dmY",$utsfin)
                                     );
    header("Location: sent_print.php");
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
echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
$gForm = new contabForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="4" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['vat_reg']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('vat_reg',$script_transl['vat_reg_value'],$form['vat_reg'],'FacetSelect',false,'vat_reg');
echo "</td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['vat_section']."</td><td class=\"FacetDataTD\">\n";
$gForm->selectNumber('vat_section',$form['vat_section'],false,1,3,'FacetSelect','vat_section');
echo "\t</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['page_ini']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"page_ini\" value=\"".$form['page_ini']."\" maxlength=\"5\" size=\"5\" /></td>\n";
echo "</td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['sta_def']."</td><td class=\"FacetDataTD\">\n";
$gForm->selCheckbox('sta_def',$form['sta_def'],$script_transl['sta_def_title']);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['jump']."</td>\n<td class=\"FacetDataTD\" colspan=\"3\">";
$gForm->selCheckbox('jump',$form['jump'],$script_transl['jump_title']);
echo "</td>\n";
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
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['sem_ord']."</td><td class=\"FacetDataTD\">\n";
$gForm->variousSelect('sem_ord',$script_transl['sem_ord_value'],$form['sem_ord'],'FacetSelect',false);
echo "</td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['cover']."</td><td class=\"FacetDataTD\">\n";
$gForm->selCheckbox('cover',$form['cover']);
echo "</td>\n";
echo "</tr>\n";
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo "<td align=\"left\"><input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\">\n";
echo '<td colspan="3" align="right"> <input type="submit" accesskey="i" name="preview" value="';
echo $script_transl['view'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";
if (isset($_POST['preview']) and $msg=='') {
  $date_ini =  sprintf("%04d%02d%02d",$form['date_ini_Y'],$form['date_ini_M'],$form['date_ini_D']);
  $date_fin =  sprintf("%04d%02d%02d",$form['date_fin_Y'],$form['date_fin_M'],$form['date_fin_D']);
  $m=getMovements($form['vat_section'],$form['vat_reg'],$date_ini,$date_fin);
  echo "<table class=\"Tlarge\">";
  if (sizeof($m) > 0) {
        $err=0;
        echo "<tr>";
        $linkHeaders=new linkHeaders($script_transl['header']);
        $linkHeaders->output();
        echo "</tr>";
        $totimponi=0.00;
        $totimpost=0.00;
        $totindetr=0.00;
        $ctrlmopre=0;
        while (list($key, $mv) = each($m)) {
             if ($mv['operat'] == 1) {
                $imponi = $mv['imponi'];
                $impost = $mv['impost'];
             } elseif ($mv['operat'] == 2) {
                $imponi = -$mv['imponi'];
                $impost = -$mv['impost'];
             } else {
                $imponi = 0;
                $impost = 0;
             }
             if ($mv['regiva']==4) {
                $mv['ragsoc']=$mv['descri'];
                $mv['descri']='';
             }
             $totimponi+=$imponi;
             if ($mv['tipiva']<>'D' || $mv['tipiva']<>'T' ) { // se indetraibili o split payment PA
                $totimpost+=$impost;
             }
             if (!isset($castle_imponi[$mv['codiva']])) {
                $castle_imponi[$mv['codiva']]=0;
                $castle_impost[$mv['codiva']]=0;
                $castle_descri[$mv['codiva']]=$mv['desvat'];
                $castle_percen[$mv['codiva']]=$mv['periva'];
             }
             $castle_imponi[$mv['codiva']]+=$imponi;
             $castle_impost[$mv['codiva']]+=$impost;
             $red_p='';
             if (isset($mv['err_p'])) {
                $red_p='red';
                $err++;
                echo "<tr>";
                echo "<td colspan=\"7\" class=\"FacetDataTDred\">".$script_transl['errors']['P'].":&nbsp;</td>";
                echo "</tr>";
             }
             $red_d='';
             if (isset($mv['err_n'])) {
                $red_d='red';
                $err++;
                echo "<tr>";
                echo "<td colspan=\"7\" class=\"FacetDataTDred\">".$script_transl['errors']['N'].":&nbsp;</td>";
                echo "</tr>";
             }
             $red_t='';
             if (isset($mv['err_t'])) {
                $red_t='red';
                $err++;
                echo "<tr>";
                echo "<td colspan=\"7\" class=\"FacetDataTDred\">".$script_transl['errors']['T'].":&nbsp;</td>";
                echo "</tr>";
             }
             echo "<tr>";
             echo "<td class=\"FacetDataTD$red_p\">".$mv['protoc']." &nbsp;</td>";
             echo "<td class=\"FacetDataTD\">".$mv['dr']."<br /><a href=\"admin_movcon.php?id_tes=".$mv['id_tes']."&Update\" title=\"Modifica il movimento contabile\">id ".$mv['id_tes']."</a> &nbsp;</td>";
             echo "<td class=\"FacetDataTD$red_d\">".$mv['descri']." n.".$mv['numdoc'].$script_transl['of'].$mv['dd']." &nbsp;</td>";
             echo "<td class=\"FacetDataTD\">".substr($mv['ragsoc'],0,30)." &nbsp;</td>";
             echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($imponi)." &nbsp;</td>";
             echo "<td align=\"right\" class=\"FacetDataTD$red_t\">".$mv['periva']." &nbsp;</td>";
             echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($impost)." &nbsp;</td></tr>";
             echo "</tr>";
        }
        echo "<tr><td colspan=7><HR></td></tr>";
        $totale = number_format(($totimponi+$totimpost),2,'.','');
        foreach ($castle_imponi as $key=>$value) {
           echo "<tr><td colspan=3></td><td class=\"FacetDataTD\">".$script_transl['tot'].$castle_descri[$key]."</td><td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($value)." &nbsp;</td><td align=\"right\" class=\"FacetDataTD\">".$castle_percen[$key]."% &nbsp;</td><td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($castle_impost[$key])." &nbsp;</td></tr>";
        }
        echo "<tr><td colspan=3></td><td colspan=4><HR></td></tr>";
        echo "<tr><td colspan=2></td><td class=\"FacetDataTD\">".$script_transl['tot'].$script_transl['t_gen']."</td><td class=\"FacetDataTD\"align=\"right\">".gaz_format_number($totale)." &nbsp;</td><td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($totimponi,2, '.', '')." &nbsp;</td><TD></TD><td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($totimpost,2, '.', '')." &nbsp;</td></tr>";
        if ($err==0) {
            echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
            echo '<td colspan="7" align="right"><input type="submit" name="print" value="';
            echo $script_transl['print'];
            echo '">';
            echo "\t </td>\n";
            echo "\t </tr>\n";
        } else {
            echo "<tr>";
            echo "<td colspan=\"7\" align=\"right\" class=\"FacetDataTDred\">".$script_transl['errors']['err']."</td>";
            echo "</tr>\n";
        }
  }
  echo "</table>\n";
}
?>
</form>
</body>
</html>