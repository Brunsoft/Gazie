<?php
class contabForm extends GAzieForm
{
    function selMasterAcc($name,$val,$val_hiddenReq='',$class='FacetSelect')
    {
        global $gTables,$admin_aziend;
        $data_color=Array(1=>"88D6FF",2=>"D6FF88",3=>"D688FF",4=>"FFD688",5=>"FF88D6",
                          6=>"88FFD6",7=>"FF88D6",8=>"88FFD6",9=>"FF88D6");
        $refresh ='';
        if (!empty($val_hiddenReq)) {
            $refresh = "onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\"";
        }
        $query = 'SELECT * FROM `'.$gTables['clfoco']."` WHERE codice LIKE '%000000' ORDER BY codice ASC";
        echo "\t <select name=\"$name\" class=\"$class\" $refresh >\n";
        echo "\t\t <option value=\"\">---------</option>\n";
        $result = gaz_dbi_query($query);
        while ($r = gaz_dbi_fetch_array($result)) {
            $v=intval($r['codice']/1000000);
            $c=intval($v/100);
            $selected = '';
            if($r['codice'] == $val) {
               $selected = "selected ";
            }
            $selected .= " style=\"background:#".$data_color[$c]."; color:#000000;";
            if($v==$admin_aziend['mascli'] || $v==$admin_aziend['masfor']) {
               $selected .= " color: red; font-weight: bold;\" ";
               $view=$v.'-'.strtoupper($r['descri']);
            } else {
               $view=$v.'-'.$r['descri'];
               $selected .= "\" ";
            }

            echo "\t\t <option value=\"".$r['codice']."\" $selected >$view</option>\n";
            $c=$v;
        }
        echo "\t </select>\n";
        }

    function lockSubtoMaster($master_value,$subName)
    {
        /*questa funzione dev'essere richiamata per legare la select case dei mastri
          con quella successiva dei sottoconti*/
        $this->master_value=$master_value;
        $this->sub_name=$subName;
    }

    function selSubAccount($name,$val,$strSearch='',$val_hiddenReq='',$mesg,$class='FacetSelect')
    {
        global $gTables,$admin_aziend;
        $mas_query='';
        $ctrl_mas=substr($val,0,3);
        $master=intval($ctrl_mas*1000000);
        if (isset($this->master_value)){
           if ($this->sub_name==$name &&  $this->master_value >100) { // // se e' gia' stato selezionato un conto legato al mastro
              $ctrl_mas=substr($this->master_value,0,3);
              $where="codice LIKE '".intval($ctrl_mas)."%' AND codice > ".$this->master_value;
           } else { // nessuno
              $where="codice < 0";
           }
        } else { //altrimenti tutti tranne i mastri
              $where="codice NOT LIKE '%000000'";
        }
        if ($ctrl_mas == $admin_aziend['mascli'] || $ctrl_mas == $admin_aziend['masfor']) {
           // cliente o fornitore
           $anagrafica = new Anagrafica();
           if ($val>100000000 && $ctrl_mas==substr($val,0,3)) { //vengo da una modifica della precedente select case quindi non serve la ricerca
                 $partner = $anagrafica->getPartner($val);
                 echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
                 echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"".substr($partner['ragso1'],0,8)."\">\n";
                 echo "\t<input type=\"submit\" value=\"".$partner['ragso1']."\" name=\"change\" onclick=\"this.form.$name.value='0'; this.form.hidden_req.value='change';\" title=\"$mesg[2]\">\n
                         <input type=\"image\" onclick=\"dialogSchedule(this);return false;\" href=\"#\" id=\"paymov".$val.$name."\"  src=\"../../library/images/schedule.png\" />\n";
           } else {
             if (strlen($strSearch) >= 2) { //sto ricercando un nuovo partner
                echo "\t<select name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
                echo "<option value=\"0\"> ---------- </option>";
                $partner = $anagrafica->queryPartners("*", $where." AND ragso1 LIKE '".addslashes($strSearch)."%'","codice ASC");
                if (count($partner) > 0) {
                      foreach ($partner as $r) {
                            $selected = '';
                            if ($r['codice'] == $val) {
                                $selected = "selected";
                            }
                            echo "\t\t <option value=\"".$r['codice']."\" $selected >".intval($r['codice'])."-".$r["ragso1"]." ".$r["citspe"]."</option>\n";
                      }
                      echo "\t </select>\n";
                 } else {
                      $msg = $mesg[0];
                 }
              } else {
                 $msg = $mesg[1];
                 echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
              }
              echo "\t<input type=\"text\" id=\"search_$name\" name=\"search[$name]\" value=\"".$strSearch."\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
              if (isset($msg)) {
                 echo "<input type=\"text\" style=\"color: red; font-weight: bold;\" size=\"".strlen($msg)."\" disabled value=\"$msg\">";
              }
              echo "\t<input type=\"image\" align=\"middle\" name=\"search_str\" src=\"../../library/images/cerbut.gif\">\n";
           }
        } else {   // altri sottoconti
              echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"\">\n";
              echo "\t<select name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
              echo "<option value=\"0\"> ---------- </option>";
              $result = gaz_dbi_dyn_query("*", $gTables['clfoco'],$where,"codice ASC");
              while ($r = gaz_dbi_fetch_array($result)) {
                  $selected='';
                  if ($val == $r['codice']) {
                     $selected = " selected ";
                  }
                  if(isset($this->master_value)){
                       $descri= substr($r["codice"],3,6);
                  } else {
                       $descri= $r["codice"];
                  }
                  echo "<option value=\"".$r['codice']."\"".$selected.">$descri-".$r['descri']."</option>";
              }
              echo "</select>\n";
        }
    }

    function selCauAccount($name,$val,$class='FacetSelect')
    {
        global $gTables,$admin_aziend;
        $where="(codice < ".intval($admin_aziend['mascli'].'000001')." OR codice > ".intval($admin_aziend['mascli'].'999999').") AND
                (codice < ".intval($admin_aziend['masfor'].'000001')." OR codice > ".intval($admin_aziend['mascli'].'999999').")";
        $data_color=Array(1=>"88D6FF",2=>"D6FF88",3=>"D688FF",4=>"FFD688",5=>"FF88D6",
                          6=>"88FFD6",7=>"FF88D6",8=>"88FFD6",9=>"FF88D6");
        echo "\t<select name=\"$name\" class=\"FacetSelect\">\n";
        echo "<option value=\"0\"> ---------- </option>";
        $result = gaz_dbi_dyn_query("*", $gTables['clfoco'],$where,"codice ASC");
        while ($r = gaz_dbi_fetch_array($result)) {
            $v=intval($r['codice']/1000000);
            $c=intval($v/100);
            $selected='';
            if ($val == $r['codice']) {
               $selected = " selected ";
            }
            $selected .= " style=\"background:#".$data_color[$c]."; color:#000000;";
            if(substr($r['codice'],-6) == '000000') {
               $selected .= " color: red; font-weight: bold;\" ";
               $view=$v.'-'.strtoupper($r['descri']);
            } else {
               $view=$r['codice'].'-'.$r['descri'];
               $selected .= "\" ";
            }

            echo "\t\t <option value=\"".$r['codice']."\" $selected >$view</option>\n";
            $c=$v;
        }
        echo "</select>\n";
    }

    function settleAccount($name,$val,$date_r=false)
    {
        if (preg_match("/^id_([0-9]+)$/",$val,$match)) { // è un partner da inserire sul piano dei conti
            $val=0;
        }
        global $gTables,$admin_aziend;
        $rs_display=array();
        // INIZIO determinazione limiti di date
        if ($date_r) {
           $final_date = $date_r;
        } else {
           $final_date = date("Ymd");
        }
        $rs_last_opening = gaz_dbi_dyn_query("*", $gTables['tesmov'], "caucon = 'APE' AND datreg <= ".$final_date,"datreg DESC",0,1);
        $last_opening = gaz_dbi_fetch_array($rs_last_opening);
        if ($last_opening) {
           $date_ini = substr($last_opening['datreg'],0,4).substr($last_opening['datreg'],5,2).substr($last_opening['datreg'],8,2);
        } else {
           $date_ini = '20040101';
        }
        // FINE determinazione limiti di date

        if ($val>100000000 && $val < 299999999 && intval(substr($val,3,6))>0 && $val!=$admin_aziend['cassa_'] ) {
            $where = " codcon = $val AND datreg BETWEEN $date_ini AND ".$final_date;
            $orderby = " datreg ASC ";
            $select = $gTables['tesmov'].".id_tes,datreg,codice,".$gTables['clfoco'].".descri,numdoc,datdoc,import*(darave='D') AS dare,import*(darave='A') AS avere";
            $table = $gTables['clfoco']." LEFT JOIN ".$gTables['rigmoc']." ON ".$gTables['clfoco'].".codice = ".$gTables['rigmoc'].".codcon "
                    ."LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes ";
            $rs=gaz_dbi_dyn_query ($select, $table, $where, $orderby);
            while ($r = gaz_dbi_fetch_array($rs)) {
                $rs_display[]=$r;
            }
        } else {
            $where = " codcon = $val AND datreg BETWEEN $date_ini AND ".$final_date." GROUP BY codcon";
            $orderby = " codcon ";
            $select = "codice,".$gTables['clfoco'].".descri,codcon,SUM(import*(darave='D')) AS dare, SUM(import*(darave='A')) AS avere,datreg";
            $table = $gTables['clfoco']." LEFT JOIN ".$gTables['rigmoc']." ON ".$gTables['clfoco'].".codice = ".$gTables['rigmoc'].".codcon "
                    ."LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes ";
            $rs=gaz_dbi_dyn_query ($select, $table, $where, $orderby);
            while ($r = gaz_dbi_fetch_array($rs)) {
                $r['datreg']= substr($final_date,0,4).'-'.substr($final_date,4,2).'-'.substr($final_date,-2);
                $r['descri']= 'Saldo ';
                $rs_display[]=$r;
            }
        }
        echo '<div style="display:none;" class="selectContainer" id="'.$name."\">\n";
        echo '<div class="selectHeader">'.$val."</div>\n";
        echo '<table cellspacing="0" cellpadding="0" width="100%" class="selectTable">';
        $saldo=0.00;
        $c=false;
        foreach ($rs_display as $r) {
            if ($c) {
               $class='odd';
            } else {
               $class='even';
            }
            $c=!$c;
            $saldo += $r['dare'];
            $saldo -= $r['avere'];
            echo "<tr class=\"$class\"> \n
                  <td>".gaz_format_date($r['datreg']).' - '.$r['descri']." </td>\n
                  <td style=\"text-align:right;\"> ".$r['dare']." </td>\n
                  <td style=\"text-align:right;\"> ".$r['avere']." </td>\n
                  <td style=\"text-align:right;cursor:pointer;\"> <a onclick=\"selectValue('$saldo','$name')\">".gaz_format_number($saldo)."</a> </td>\n
                  </tr>\n";
        }
        echo "</table></div>\n";
    }
    
    
// ------- INIZIO NUOVA VERSIONE DELLE FUNZIONI PER LA SELEZIONE DEI SOTTOCONTI-----------------------------

    function setWhat($m)
    {

        $this->what="a.id AS id,pariva,codfis,a.citspe AS citta, ragso1 AS ragsoc,
                     (SELECT ".$this->gTables['clfoco'].".codice FROM ".$this->gTables['clfoco']." WHERE a.id=".$this->gTables['clfoco'].".id_anagra AND ".$this->gTables['clfoco'].".codice BETWEEN ".substr($m,0,3)."000001 AND ".substr($m,0,3)."999999 LIMIT 1) AS codpart ,
                     (SELECT ".$this->gTables['clfoco'].".codice FROM ".$this->gTables['clfoco']." WHERE a.id=".$this->gTables['clfoco'].".id_anagra LIMIT 1) AS codice,
                     (SELECT ".$this->gTables['clfoco'].".status FROM ".$this->gTables['clfoco']." WHERE a.id=".$this->gTables['clfoco'].".id_anagra LIMIT 1) AS status ";
    }

    function queryAnagra($where=1)
    {
       $rs=gaz_dbi_dyn_query($this->what,$this->gTables['anagra'].' AS a',$where,"ragsoc ASC");
       $anagrafiche = array();
       while ($r=gaz_dbi_fetch_array($rs)) {
            $anagrafiche[] = $r;
       }
       return $anagrafiche;
    }

    function sub_Account($name,$val,$strSearch='',$val_hiddenReq='',$mesg)
    {
        global $gTables,$admin_aziend;
        $this->gTables = $gTables;
        $this->name = $name;
        $this->what="a.id AS id,pariva,codfis,a.citspe AS citta, ragso1 AS ragsoc,
                     (SELECT ".$this->gTables['clfoco'].".codice FROM ".$this->gTables['clfoco']." WHERE a.id=".$this->gTables['clfoco'].".id_anagra LIMIT 1) AS codice,
                     (SELECT ".$this->gTables['clfoco'].".status FROM ".$this->gTables['clfoco']." WHERE a.id=".$this->gTables['clfoco'].".id_anagra LIMIT 1) AS status, 0 AS codpart ";
        $mas_query='';
        $ctrl_mas=substr($val,0,3);
        if (isset($this->master_value)){
           if ($this->sub_name==$name &&  $this->master_value >100) { // // se e' gia' stato selezionato un conto legato al mastro
              $ctrl_mas=substr($this->master_value,0,3);
              $where="codice LIKE '".intval($ctrl_mas)."%' AND codice > ".$this->master_value;
           } else { // nessuno
              $where="codice < 0";
           }
        } else { //altrimenti tutti tranne i mastri
              $where="codice NOT LIKE '%000000'";
        }
        if ($ctrl_mas == $admin_aziend['mascli'] || $ctrl_mas == $admin_aziend['masfor']) { // se è un partner commerciale
            // cliente o fornitore
            $anagrafica = new Anagrafica();
            if ($val>100000000 && $ctrl_mas==substr($val,0,3)) { //vengo da una modifica della precedente select case quindi non serve la ricerca
                $partner=gaz_dbi_get_row($gTables['clfoco'].' LEFT JOIN '.$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id',"codice",$val);
                echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
                echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"".substr($partner['ragso1'],0,8)."\">\n";
                echo "\t<input type=\"submit\" tabindex=\"999\" value=\"".$partner['ragso1']."\" name=\"change\" onclick=\"this.form.$name.value='0'; this.form.hidden_req.value='change';\" title=\"$mesg[2]\">\n
				<input type=\"image\" onclick=\"dialogSchedule(this);return false;\" href=\"#\" id=\"paymov".$val.$name."\"  src=\"../../library/images/schedule.png\" />\n";
            } elseif (preg_match("/^id_([0-9]+)$/",$val,$match)) { // e' stata selezionata la sola anagrafica
                $partner=gaz_dbi_get_row($gTables['anagra'],'id',$match[1]);
                echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
                echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"".substr($partner['ragso1'],0,8)."\">\n";
                echo "\t<input type=\"submit\" tabindex=\"999\" style=\"background:#FFBBBB\"; value=\"".$partner['ragso1']."\" name=\"change\" onclick=\"this.form.$name.value='0'; this.form.hidden_req.value='change';\" title=\"$mesg[2]\">\n";
            }  else {
                if (strlen($strSearch) >= 2) { //sto ricercando un nuovo partner
                    if ($this->master_value>100){ //ho da ricercare nell'ambito di un mastro
                        $this->setWhat($this->master_value);
                    }
                    if (is_numeric($strSearch)){                      //ricerca per partita iva
                        $partner = $this->queryAnagra(" pariva = ".intval($strSearch));
                    } elseif (is_numeric(substr($strSearch,6,2))) {   //ricerca per codice fiscale
                        $partner = $this->queryAnagra(" a.codfis LIKE '%".addslashes($strSearch)."%'");
                    } else {                                      //ricerca per ragione sociale
                        $partner = $this->queryAnagra(" a.ragso1 LIKE '".addslashes($strSearch)."%'");
                    }
                    if (count($partner) > 0) {
                        echo "\t<select name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
                        echo "<option value=\"0\"> ---------- </option>";
                        preg_match("/^id_([0-9]+)$/",$val,$match);
                        foreach ($partner as $r) {
                            if ($r['codpart']>0){
                                $r['codice']=$r['codpart'];
                            }
                            $style='';
                            $selected = '';
                            $disabled='';
                            if($r['status']=='HIDDEN') {
                                $disabled=' disabled ';
                            }
                            if (isset($match[1]) && $match[1]==$r['id']) {
                                $selected = "selected";
                            } elseif ($r['codice']==$val && $val >0) {
                                $selected = "selected";
                            }
                            if ($this->master_value<0) { // vado cercando tutti i partner del piano dei conti
                                if ($r["codice"]<1) {  // disabilito le anagrafiche presenti solo in altre aziende
                                    $disabled=' disabled ';
                                    $style = 'style="background:#FF6666";';
                                }
                            } elseif($r["codice"]<1) {
                                $style = 'style="background:#FF6666";';
                                $r['codice'] = 'id_'.$r['id'];
                            } elseif(substr($r["codice"],0,3)!=substr($this->master_value,0,3)) {
                                $style = 'style="background:#FFBBBB";';
                                $r['codice'] = 'id_'.$r['id'];
                            }
                            echo "\t\t <option $style value=\"".$r['codice']."\" $selected $disabled>".substr($r["codice"],3,6).'-'.$r["ragsoc"]." ".$r["citta"]."</option>\n";
                        }
                        echo "\t </select>\n";
                    } else {
                        $msg = $mesg[0];
                        echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
                    }   
                } else {
                    $msg = $mesg[1];
                    echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
                }
                echo "\t<input type=\"text\" id=\"search_$name\" name=\"search[$name]\" value=\"".$strSearch."\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
                if (isset($msg)) {
                    echo "<input type=\"text\" style=\"color: red; font-weight: bold;\" size=\"".strlen($msg)."\" disabled value=\"$msg\">\n";
                }
                echo "\t<input type=\"image\" align=\"middle\" name=\"search_str\" src=\"../../library/images/cerbut.gif\">\n";
            }
        } else {   // altri sottoconti
              echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"\">\n";
              echo "\t<select name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
              echo "<option value=\"0\"> ---------- </option>";
              $result = gaz_dbi_dyn_query("*", $gTables['clfoco'],$where,"codice ASC");
              while ($r = gaz_dbi_fetch_array($result)) {
                  $selected='';
                  if ($val == $r['codice']) {
                     $selected = " selected ";
                  }
                  if(isset($this->master_value)){
                       $descri= substr($r["codice"],3,6);
                  } else {
                       $descri= $r["codice"];
                  }
                  echo "<option value=\"".$r['codice']."\"".$selected.">$descri-".$r['descri']."</option>";
              }
              echo "</select>\n";
        }
    }
}

function rigmocUpdate($id, $newValue)
{
    $columns = array('id_tes','darave','codcon','import');
    tableUpdate('rigmoc', $columns, $id, $newValue);
}

?>