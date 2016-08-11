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

function getItems($cm_ini,$cm_fin,$art_ini,$art_fin) {
        global $gTables,$admin_aziend;
        $m=array();
        if ($art_fin=='') {
              $art_fin='zzzzzzzzzzzzzzz';
        }
        $where="codice BETWEEN '$art_ini' AND '$art_fin' AND catmer BETWEEN $cm_ini AND ".$cm_fin;
        //recupero gli articoli in base alle scelte impostate
        $rs=gaz_dbi_dyn_query ('*',$gTables['artico'],$where,"catmer ASC, codice ASC");
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
}
function compute_new_price($base_price,$obj_price=0,$value=0,$mode='C',$round=3) {
/* calcolo del nuovo prezzo in base ai valori passati come referenza:

$base_price � il prezzo del listino preso a base di calcolo, ovvero il prezzo vecchio
$obj_price � il prezzo del listino da modificare, ovvero il prezzo vecchio (default=0)
$value � il valore di incremento/decremento o percentuale (default=0)
$mode � il tipo di modifica da effettuare e pu� assumere i seguenti valori:
      A = sostituzione;
      B = somma in percentuale
      C = somma valore  (default)
      D = moltiplicazione per valore
      E = divisione per valore
      F = azzeramento e somma in percentuale
$round � il numero di decimali per l'arrotondamento (default valore scelto in anagrafica azienda)

*/
    switch ($mode) {
           case 'A': //sostituzione
           $new_price = round($value,$round);
           break;
           case 'B': //somma in percentuale
           $new_price = round($obj_price+$base_price*$value/100,$round);
           break;
           case 'C': //somma valore
           $new_price = round($obj_price+$value,$round);
           break;
           case 'D': //moltiplicazione per valore
           $new_price = round($obj_price*$value,$round);
           break;
           case 'E': //divisione per valore
           $new_price = round($obj_price/$value,$round);
           break;
           case 'F': //azzeramento e somma in percentuale
           $new_price = round($base_price+$base_price*$value/100,$round);
           break;
    }
    return $new_price;
}

function getExtremeValue($table_name,$min_max='MIN')
    {
        $rs=gaz_dbi_dyn_query ($min_max.'(codice) AS value',$table_name);
        $data=gaz_dbi_fetch_array($rs);
        return $data['value'];
    }

if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['mode']='C';
    $form['valore']='0';
    $form['lis_bas']=1;
    $form['lis_obj']=1;
    $form['round_mode']=$admin_aziend['decimal_price'];
    if (isset($_GET['id'])) {
       $item=gaz_dbi_get_row($gTables['artico'],'codice',substr($_GET['id'],0,15));
       $form['art_ini']=$item['codice'];
       $form['art_fin']=$item['codice'];
       $form['cm_ini']=$item['catmer'];
       $form['cm_fin']=$item['catmer'];
    }  else {
       if (isset($_GET['ai'])) {
          $form['art_ini']=substr($_GET['ai'],0,15);
       } else {
          $form['art_ini']=getExtremeValue($gTables['artico']);
       }
       if (isset($_GET['af'])) {
          $form['art_fin']=substr($_GET['af'],0,15);
       } else {
          $form['art_fin']=getExtremeValue($gTables['artico'],'MAX');
       }
       if (isset($_GET['ci'])) {
          $form['cm_ini']=intval($_GET['ci']);
       } else {
          $form['cm_ini']=getExtremeValue($gTables['catmer']);
       }
       if (isset($_GET['cf'])) {
          $form['cm_fin']=intval($_GET['cf']);
       } else {
          $form['cm_fin']=getExtremeValue($gTables['catmer'],'MAX');
       }
    }
    $form['search']['art_ini']='';
    $form['search']['art_fin']='';
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['lis_bas']=substr($_POST['lis_bas'],0,3);
    $form['lis_obj']=substr($_POST['lis_obj'],0,3);
    $form['mode']=substr($_POST['mode'],0,1);
    $form['valore']=floatval(preg_replace("/\,/",'.',$_POST['valore']));
    $form['round_mode']=intval($_POST['round_mode']);
    $form['cm_ini']=intval($_POST['cm_ini']);
    $form['cm_fin']=intval($_POST['cm_fin']);
    $form['art_ini']=substr($_POST['art_ini'],0,15);
    $form['art_fin']=substr($_POST['art_fin'],0,15);
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
}

//controllo i campi
if ($form['valore']==0 && ($form['mode']=='D' || $form['mode']=='E')) {
    $msg .='0+';
}
if (strcasecmp($form['art_ini'],$form['art_fin'])>0) {
    $msg .='1+';
}
if ($form['cm_ini'] > $form['cm_fin']) {
    $msg .='2+';
}
// fine controlli

if (isset($_POST['submit']) && $msg=='') {
  //Modifico i prezzi di tutti gli articoli selezionati...
  $m=getItems($form['cm_ini'],$form['cm_fin'],$form['art_ini'],$form['art_fin']);
  if (sizeof($m) > 0) {
        if ($form['lis_bas']=='0') {
           $name_bas='preacq';
        } elseif ($form['lis_bas']=='web') {
           $name_bas='web_price';
        } else {
           $name_bas='preve'.$form['lis_bas'];
        }
        if ($form['lis_obj']=='0') {
           $name_obj='preacq';
        } elseif ($form['lis_obj']=='web') {
           $name_obj='web_price';
        } else {
           $name_obj='preve'.$form['lis_obj'];
        }
        while (list($key, $mv) = each($m)) {
            $new_price=compute_new_price($mv[$name_bas],$mv[$name_obj],$form['valore'],$form['mode'],$form['round_mode']);
            // questo e' troppo lento: gaz_dbi_put_row($gTables['artico'],'codice',$mv['codice'],$name_obj,$new_price);
            gaz_dbi_query ("UPDATE ".$gTables['artico']." SET ".$name_obj." = ".$new_price." WHERE codice = '".$mv['codice']."';");
        }
        header("Location:report_artico.php");
        exit;
  }
}

require("../../library/include/header.php");
$script_transl=HeadMain();

echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
$gForm = new magazzForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tsmall\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['lis_obj']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('lis_obj',$script_transl['listino_value'],$form['lis_obj'],'FacetSelect',false);
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['cm_ini']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectFromDB('catmer','cm_ini','codice',$form['cm_ini'],false,false,'-','descri','cm_ini');
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['cm_fin']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selectFromDB('catmer','cm_fin','codice',$form['cm_fin'],false,false,'-','descri','cm_fin');
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['art_ini']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selItem('art_ini',$form['art_ini'],$form['search']['art_ini'],$script_transl['mesg'],$form['hidden_req']);
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['art_fin']."</td><td  class=\"FacetDataTD\">\n";
$gForm->selItem('art_fin',$form['art_fin'],$form['search']['art_fin'],$script_transl['mesg'],$form['hidden_req']);
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['lis_bas']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('lis_bas',$script_transl['listino_value'],$form['lis_bas'],'FacetSelect',false);
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['mode']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('mode',$script_transl['mode_value'],$form['mode'],'FacetSelect',false);
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['valore']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"valore\" value=\"".$form['valore']."\" align=\"right\" maxlength=\"9\" size=\"3\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['round_mode']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('round_mode',$script_transl['round_mode_value'],$form['round_mode'],'FacetSelect',false);
echo "</tr>\n";
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo "<td align=\"left\"><input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\">\n";
echo '<td align="right"> <input type="submit" accesskey="i" name="preview" value="';
echo $script_transl['view'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";

if (isset($_POST['preview']) and $msg=='') {
  $m=getItems($form['cm_ini'],$form['cm_fin'],$form['art_ini'],$form['art_fin']);
  echo "<table class=\"Tlarge\">";
  if (sizeof($m) > 0) {
        if ($form['lis_bas']=='0') {
           $name_bas='preacq';
        } elseif ($form['lis_bas']=='web') {
           $name_bas='web_price';
        } else {
           $name_bas='preve'.$form['lis_bas'];
        }
        if ($form['lis_obj']=='0') {
           $name_obj='preacq';
        } elseif ($form['lis_obj']=='web') {
           $name_obj='web_price';
        } else {
           $name_obj='preve'.$form['lis_obj'];
        }
        echo "<tr>";
        $linkHeaders=new linkHeaders($script_transl['header']);
        $linkHeaders->output();
        echo "</tr>";
        $ctr_mv=0;
        while (list($key, $mv) = each($m)) {
            if ($mv['catmer']>$ctr_mv){
                $cm=gaz_dbi_get_row($gTables['catmer'],'codice',$mv['catmer']);
                echo "<tr><td class=\"FacetFieldCaptionTD\">".$mv['catmer'].' - '.$cm['descri']." &nbsp</td><td colspan=\"5\"></td></tr>\n";
            }
            echo "<tr><td></td>\n";
            echo "<td class=\"FacetDataTD\">".$mv['codice']." &nbsp;</td>";
            echo "<td class=\"FacetDataTD\">".$mv['descri']." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".$mv['unimis']." &nbsp;</td>\n";
            echo "<td align=\"right\" class=\"FacetDataTD\">".number_format($mv[$name_bas],$admin_aziend['decimal_price'],',','')." &nbsp;</td>\n";
            echo "<td align=\"right\" class=\"FacetDataTD\">".
                 number_format(compute_new_price($mv[$name_bas],$mv[$name_obj],$form['valore'],$form['mode'],$form['round_mode']),$admin_aziend['decimal_price'],',','')." &nbsp;</td>";
            echo "</tr>\n";
            $ctr_mv=$mv['catmer'];
         }
         echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
         echo '<td colspan="6" align="right"><input type="submit" name="submit" value="';
         echo $script_transl['submit'];
         echo '">';
         echo "\t </td>\n";
         echo "\t </tr>\n";
  }
  echo "</table>";
}
?>
</form>
</body>
</html>