<?php
/* $Id: install.php,v 1.17 2011/01/01 11:08:15 devincen Exp $
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
require("../../config/config/gconfig.php");
require('../../library/include/'.$NomeDB.'.lib.php');
$err=array();
//
// Ottiene in qualche modo il prefisso delle tabelle.
//
if (isset($_SESSION['table_prefix'])) {
   $table_prefix=substr($_SESSION['table_prefix'],0,12);
} elseif (isset($_POST['tp'])) {
    $table_prefix=filter_var(substr($_POST['tp'],0,12),FILTER_SANITIZE_MAGIC_QUOTES);
} elseif(isset($_GET['tp'])) {
    $table_prefix=filter_var(substr($_GET['tp'],0,12),FILTER_SANITIZE_MAGIC_QUOTES);
} else {
    $table_prefix=filter_var(substr($table_prefix,0,12),FILTER_SANITIZE_MAGIC_QUOTES);
}
//
// Alcune directory devono essere scrivibili dal servente HTTP/PHP (www-data).
//
if (!dir_writable(DATA_DIR.'files/')) { //questa per archiviare i documenti
    $err[] = 'no_data_files_writable';
}
if (!dir_writable(K_PATH_CACHE)) { //questa per permettere a TCPDF di inserire le immagini
    $err[] = 'no_tcpdf_cache_writable'; 
}
//
// fine controllo directory scrivibili
//
if (!isset($_POST['hidden_req'])){           // al primo accesso allo script
    $form['hidden_req'] = '';
    $form['lang'] = 'italian';
    if(connectIsOk() && databaseIsOk()) {    // verifico la presenza della base dati
      $form['install_upgrade'] = 'upgrade';
      $form['lang'] = getLang();
      if (databaseIsAlign()) {               // la base dati e' aggiornata!
         $err[] = 'is_align';
      }
    } elseif (!connectIsOk()) {              // non si connette al server
      $err[] = 'no_conn';
    } else {                                 // se si connette ma non trova la base dati allora prova ad installarla
      $form['install_upgrade'] = 'install';
    }
} else {                                     // negli accessi successivi
    connectIsOk();
    $form['hidden_req'] = substr($_POST['hidden_req'],0,16);
    $form['lang'] = substr($_POST['lang'],0,16);
    $form['install_upgrade'] = substr($_POST['install_upgrade'],0,16);
    if (isset($_POST['upgrade'])) {              // AGGIORNO
      if (databaseIsAlign()) {               // la base dati e' aggiornata!
         $err[] = 'is_align';
      } else { 
         connectToDB ();
         executeQueryFileUpgrade($table_prefix);
      }
    }
    if (isset($_POST['install'])) {              //INSTALLO
        // recupero il file sql d'installazione nella directory setup/install/
        // e possibilmente nella lingua selezonata dall'utente
        // che deve avere il nome example: "install_5.2.english.php"
        $file=getInstallSqlFile($form['lang']);
        if (executeQueryFileInstall($file,$Database,$table_prefix)){
            // se va a buon fine controllo eventuali file di aggiornamento
            $form['install_upgrade'] = 'upgrade';
            $form['lang'] = getLang();
            if (databaseIsAlign()) {               // la base dati e' aggiornata!
               $form['lang'] = getLang();
               $err[] = 'is_align';
            }
        }
    }
}

require("../../language/".$form['lang']."/setup.php");

function databaseIsAlign()
{
      // Antonio De Vincentiis 2 Luglio 2009
      connectToDB ();
      $lastSql=getSqlFileVersion();
      if (getDbVersion() < $lastSql[2]) {
        return false;
      } else {
        return true;
      }
}

function archiviIsOk($currentDbVersion, $sqlFiles)
{
    $last = end($sqlFiles);
    if ($last[2] == $currentDbVersion) {
        return True;
    } else {
        return False;
    }
}

function getDbVersion()
{
    global $table_prefix;
    $query = "SELECT cvalue FROM `".$table_prefix."_config` WHERE variable = 'archive'";
    $result = gaz_dbi_query ($query);
    $versione = gaz_dbi_fetch_array($result);
    return $versione[0];
}

function getCompanyNumbers()
{
    global $table_prefix;
    $query = "SELECT codice FROM `".$table_prefix."_aziend`";
    $result = gaz_dbi_query ($query);
    $companyNo = array();
	while($r=gaz_dbi_fetch_array($result)){
		$companyNo[]=$r['codice'];
	}
    return $companyNo;
}

function getLang()
{
    global $table_prefix;
    $query = "SELECT cvalue FROM `".$table_prefix."_config` WHERE variable = 'install_lang'";
    $result = gaz_dbi_query ($query);
    $versione = gaz_dbi_fetch_array($result);
    if ($versione) {
        return $versione[0];
    } else {
        return 'italian';
    }
}

function getSqlFileVersion()
{
    // Luigi Rambaldi 13 Ottobre 2005
    $fileArray = Array();
    $structArray = Array();
    $disorderedStructArray = Array();
    $relativePath = '../../setup/install/';
    if ($handle = opendir($relativePath)) {
       while ($file = readdir($handle)) {
             if(($file == ".") or ($file == "..")) continue;
             if(!preg_match("/^update_to_[0-9]+\.[0-9]\.[0-9]+\.sql$/",$file) &&
                !preg_match("/^update_to_[0-9]+\.[0-9]+\.sql$/",$file)) continue; //filtro per estensione .sql dei nomi dei file
             $fileArray[] = $file; // push sull'accumulatore
       }
       // conversione del $fileArray nelle corrispondenti strutture (si ottiene un array disordinato).
       foreach($fileArray as $fileItem){
               $version = sqlFileScan($relativePath.$fileItem);
               if($version == Array()) continue; // bypass dei file sql che non contengono gli aggiornamenti
               $initVersion = $version[0];
               $finalVersion = end($version);
               $disorderedStructArray[] = Array($fileItem, $initVersion, $finalVersion);
       }
       usort($disorderedStructArray,"compareSqlFiles");
       foreach ($disorderedStructArray as $key => $value) {
               $structArray[$value[1]] = $value;
       }
       closedir($handle);
    }
    return end($structArray);
}

function getNextSqlFileName($currentDbVersion, $sqlFiles)
{
    foreach ($sqlFiles as $key => $value) {
        if (($sqlFiles[$key][1] <= $currentDbVersion + 1) and ($currentDbVersion + 1 <= $sqlFiles[$key][2])) {
            return $sqlFiles[$key][0];
        }
    }
    return '';
}

function executeQueryFileInstall($sqlFile,$Database,$table_prefix)
{
    // Luigi Rambaldi 13 Ottobre 2005 - last rev. Antonio de Vincentiis 27 Giugno 2011
    global $Database,$link;
    // Inizializzazione accumulatore
    $tmpSql=file_get_contents( "../../setup/install/". $sqlFile );
    $tmpSql = preg_replace("/gaz_/", $table_prefix.'_', $tmpSql);  //sostituisco gaz_ con il prefisso personalizzato
    $tmpSql = preg_replace("/CREATE DATABASE IF NOT EXISTS gazie/", "CREATE DATABASE IF NOT EXISTS ".$Database, $tmpSql);
    $tmpSql = preg_replace("/USE gazie/", "USE ".$Database, $tmpSql);
    // Iterazione per ciascuna linea del file.
    $lineArray = explode(";\n",$tmpSql);
    foreach($lineArray as $l){
        $l=ltrim($l);
        if (!empty($l)) {
           gaz_dbi_query($l);
        }
    }
    return true;
}

function executeQueryFileUpgrade($table_prefix) // funzione dedicata alla gestione delle sottosezioni
{
    global $disable_set_time_limit;
    if (!$disable_set_time_limit) {
        set_time_limit (300);
    }
    // Luigi Rambaldi 13 Ottobre 2005
    // Inizializzazione accumulatore
    $sql = "";
    $currentDbVersion=getDbVersion();
    $nextDbVersion =  $currentDbVersion + 1; // versione del'upgrade da individuare per l'aggiornamento corrente (contiguità nella numerazione delle versioni).
    $stopDbVersion = $currentDbVersion + 2;
    $sqlFile = getNextSqlFileName($currentDbVersion,getSqlFiles());
    // trovo l'ultima  sottosezione (individuabile a partire dalla versione corrente del Database)
    // Iterazione per ciascuna linea del file.
    $lineArray = file($sqlFile);
    $parsingFlag = False; // flag per individuare ciascuna sottosezione, corrispondente a cisacuna versione del DB
    $companies=getCompanyNumbers();
    $activateWhile = False; // flag per attivare il ciclo while
    foreach($lineArray as $line) {
        if (preg_match("/UPDATE[ \n\r\t\x0B]+(`){0,1}gaz_config(`){0,1}[ \n\r\t\x0B]+SET[ \n\r\t\x0B]+(`){0,1}cvalue(`){0,1}[ \n\r\t\x0B]*=[ \n\r\t\x0B]*\'$nextDbVersion\'/i", $line)) {
            $parsingFlag = True;
        }
        if (preg_match("/UPDATE[ \n\r\t\x0B]+(`){0,1}gaz_config(`){0,1}[ \n\r\t\x0B]+SET[ \n\r\t\x0B]+(`){0,1}cvalue(`){0,1}[ \n\r\t\x0B]*=[ \n\r\t\x0B]*\'$stopDbVersion\'/i", $line)) {
            $parsingFlag = False;
            break;
        }
        if($parsingFlag) {
            if (preg_match("/START_WHILE/i", $line)) {
              $activateWhile = True;
              $line='';
            }
            if (preg_match("/STOP_WHILE/i", $line)) {
              $activateWhile = False;
              $line='';
            }
            $sql .= $line;
            // Il punto e virgola indica la fine di ciascuna istruzione SQL , ciascuna di esse viene accumulata
            if (!preg_match("/;/", $sql)) {
                continue;// incremento dell'accumulatore
            }
            // Sostituisce il prefisso standard ed elimina il punto e virgola
            $sql = preg_replace("/gaz_/", $table_prefix.'_', $sql);
            $sql = preg_replace("/;/", "", $sql);
            if ($activateWhile){
               // Esegue l'istruzione sulle tabelle di tutte le aziende installate.
               $sql_ori=$sql;;
               foreach ($companies as $i) {
                    $sql = preg_replace("/XXX/", sprintf('%03d',$i), $sql_ori);
                    if (!gaz_dbi_query($sql)) { // si collega al DB
                        echo "Query Fallita";
                        echo "$sql <br/>";
                        exit;
                    }
               }
               $sql = "";// ripristino dell'accumulatore
            } else {
               // Esegue una singola istruzione.
               if (!gaz_dbi_query($sql)) { // si collega al DB
                   echo "Query Fallita";
                   echo "$sql <br/>";
                   exit;
               } else {
                   $sql = "";// ripristino dell'accumulatore a seguito dell'istruzione
               }
            }
        }
    }
}


function getInstallSqlFile($lang)
{
//serve per trovare il primo file .sql di installazione piu' recente e possibilmente nella lingua scelta
$lastInstallSqlFile = "";
$ctrlLastVersion = 0;
$relativePath = '../../setup/install';
if ($handle = opendir($relativePath)) {
    while ($file = readdir($handle)) {
        if(($file == ".") || ($file == "..")) continue;
        if (preg_match("/^install_([0-9]{1,2})\.([0-9]{1,2})\.sql$/", $file, $regs)) {
           //faccio il push solo se e' una versione di valore maggiore della precedente
           $versionFile =  $regs[1]*100+$regs[2];
           if ($versionFile > $ctrlLastVersion) {
              $lastInstallSqlFile = $file;
              $ctrlLastVersion = $versionFile;
           }
        } elseif (preg_match("/^install_([0-9]{1,2})\.([0-9]{1,2})\.$lang\.sql$/", $file, $regs)) {
           // ho trovato una versione in lingua di valore almeno uguale
           $versionFile =  $regs[1]*100+$regs[2];
           if ($versionFile >= $ctrlLastVersion) {
              $lastInstallSqlFile = $file;
              $ctrlLastVersion = $versionFile;
           }
        } else {
           continue;
        }
    }
    closedir($handle);
}
return $lastInstallSqlFile;
}

function compareSqlFiles($struct1, $struct2)
{
// Luigi Rambaldi 13 Ottobre 2005
if ($struct2[2] < $struct1[1])
    return True;
else
    return False;
}


function sqlFileScan($file)
{
    global $table_prefix;
    $versions = Array();
    $relativePath = '../../setup/install';
    $lineArray = file($file);
    foreach($lineArray as $line) {
         if(preg_match("/UPDATE[ \n\r\t\x0B]+(`){0,1}gaz_config(`){0,1}[ \n\r\t\x0B]+SET[ \n\r\t\x0B]+(`){0,1}cvalue(`){0,1}[ \n\r\t\x0B]+=[ \n\r\t\x0B]+'/i", $line)){
             $versionArray = preg_split("/[=']/", $line) ;// In caso dell'uso degli apici per denotare i valori delle versioni
             $versions[] = trim ($versionArray[2]);// Eliminazione spazi e posizionamento.
         }
         if(preg_match("/UPDATE[ \n\r\t\x0B]+(`){0,1}gaz_config(`){0,1}[ \n\r\t\x0B]+SET[ \n\r\t\x0B]+(`){0,1}cvalue(`){0,1}[ \n\r\t\x0B]+=[ \n\r\t\x0B]+[0-9]+/i", $line)){
             $versionArray = preg_split("/[=Ww]/", $line) ;// In caso in cui non vengono usato gli apici per denotare i valori delle versioni (wW serve per identificare il where/WHERE)
             $versions[] = trim ($versionArray[1]);
         }
    }
return $versions;
}

function getSqlFiles()
{
$fileArray = Array();
$structArray = Array();
$disorderedStructArray = Array();
$relativePath = '../../setup/install';
if ($handle = opendir($relativePath)) {
    while ($file = readdir($handle)) {
        if(($file == ".") or ($file == ".."))
            continue;
        if(!preg_match("/^update_to_[0-9]+\.[0-9]\.[0-9]+\.sql$/",$file) &&
           !preg_match("/^update_to_[0-9]+\.[0-9]+\.sql$/",$file) ) continue; //filtro per estensione .sql dei nomi dei file
        $fileArray[] = $file; // push sull'accumulatore
    }
    // conversione del $fileArray nelle corrispondenti strutture (si ottiene un array disordinato).
    foreach($fileArray as $fileItem){
        $version = sqlFileScan($fileItem);
        if($version == Array()) continue; // bypass dei file sql che non contengono gli aggiornamenti
        $initVersion = $version[0];
        $finalVersion = end($version);
        $disorderedStructArray[] = Array($fileItem, $initVersion, $finalVersion);
        }
    usort($disorderedStructArray, "compareSqlFiles");
    foreach ($disorderedStructArray as $key => $value) {
        $structArray[$value[1]] = $value;
    }
    closedir($handle);
    }
return $structArray;
}

function dir_writable($folder)
{
    $isw=false;
    $perm=substr(sprintf('%o', fileperms($folder)),-2);
    //echo $folder . "  -->> ".$perm . " <br>" ;
    if ($perm>=66) {
        $isw = true;
    }
    return $isw;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="author" content="Antonio De Vincentiis http://www.devincentiis.it">
    <link rel="stylesheet" type="text/css" href="../../library/style/default.css">
    <link rel="shortcut icon" href="../library/images/favicon.ico">
    <title><?php echo $msg['title'];?></title>
</head>
<body>
    <br /><br /><br />
    <form method="POST">
    <input type="hidden" value="<?php echo $form['hidden_req'];?>"      name="hidden_req">
    <input type="hidden" value="<?php echo $form['install_upgrade'];?>" name="install_upgrade">
    <input type="hidden" value="<?php echo $form['lang'];?>"            name="lang">
    <input type="hidden" value="<?php echo $table_prefix; ?>"           name="tp">
    <table align="center">
    <tbody>
        <tr>
            <td align="center"><img src="../../library/images/gazie.gif" width="77">
            </td>
            <td colspan="2" align="center"  style="vertical-align:middle">
        <?php
        if ($form['install_upgrade']=='install') {
            echo $msg['gi_lang'].': <select name="lang" class="FacetSelect" onchange="this.form.submit();">';
            if ($handle = opendir('../../language')) {
              while ($dir = readdir($handle)) {
                  if(($dir == ".") || ($dir == "..") || ($dir == ".svn")) continue;
                     $selected="";
                     if ($form['lang'] == $dir) {
                        $selected = " selected ";
                     }
                     echo "<option value=\"".$dir."\"".$selected." >".ucfirst($dir)."</option>\n";
                  }
                  closedir($handle);
            }
            echo '</select> _ <img src="../../language/'.$form['lang'].'/flag.png" >';
        } else {
            echo '<img src="../../language/'.$form['lang'].'/flag.png" >';
        }
        ?>
        </td>
        </tr>
        <tr>
            <td colspan="3" class="FacetDataTD" align="center">
            <strong><?php echo $msg['gi_'.$form['install_upgrade']].' GAzie '.$versSw ?></strong>
                <?php
                if ($form['install_upgrade']=='upgrade') {
                     $lastSql=getSqlFileVersion();
                     echo '<br />'.$msg['gi_upg_from'].' '.getDbVersion().' '.$msg['gi_upg_to'].' '.$lastSql[2];
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="FacetDataTD" align="center">
            <?php
            if (count($err)==0) {
               echo '<input name="'.$form['install_upgrade'].'" type="submit" value="'.strtoupper($msg[$form['install_upgrade']]).'!">';
            } else {
               foreach ($err as $v){
                  echo $errors[$v]." <br />";
                  if ($v=='is_align'){
                     echo '<input  onClick="location.href=\'../../modules/root/admin.php\'" name="'.$form['install_upgrade'].'" type="button" value="'.$msg['gi_is_align'].'">';
                     echo "\n <br />".$msg['gi_usr_psw']." <br />";
                  } else {
                     echo '<img src="../../library/images/x.gif" ><br /> ';
                 }
               }
            }
            ?>
            </td>
        </tr>
</tbody>
</table>
</form>
</body>
</html>
