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

/*
 -- TRANSLATED BY : Dante Becerra Lagos (softenglish@gmail.com)
*/

$strScript = array ("report_letter.php" =>
                   array(  "Reporte de Cartas ",
                           "Fecha ",
                           "Numero ",
                           "Tipo ",
                           "Nombre de Empresa ",
                           "Objeto ",
                           "Escribir nueva carta",
                         'mail_alert0'=>'Invio lettera con email',
                         'mail_alert1'=>'Hai scelto di inviare una e-mail all\'indirizzo: ',
                         'mail_alert2'=>'con allegato la seguente lettera:'),
                    "admin_letter.php" =>
                   array(  'title' => " Carta ",
                           'mesg'=>array('La busqueda no arrojo resultados!',
                                         'Inserte al menos 2 caracteres!',
                                         'Cambiando cliente'
                                          ),
                           array("LET" => " Normal ","DIC" => "Declaracion","SOL" => " Solicitud "),
                           " de ",
                           " a las ",
                           " numero ",
                           "Objeto ",
                           " atencion para ",
                           "adjuntar la firma del usuario ",
                           "Tipo ",
                           "Cuerpo ",
                           "Nombre de usuario",
                           "La fecha no es correcta!",
                           "Debe seleccionar un cliente o un proveedor!"
                        ),
                    "update_control.php" =>
                    array(  'title' => " Check for updates ",
                           'new_ver1'=>'It\'s available upgrade to new version (',
                           'new_ver2'=>') of GAzie! <br>To upgrade you can download files from: ',
                           'is_align'=>'No new updates. This version is updated to the latest available GAzie.',
                           'no_conn'=>'There are problems connecting to the server to version control!',
                           'disabled'=>'The latest version control has been disabled. You can reactivate it by choosing one of the services available to check the following sites',
                           'zone'=>'Zone',
                           'city'=>'City',
                           'sms'=>'SMS',
                           'web'=>'WEB',
                           'choice'=>'CHOICE',
                           'check_value'=>array(0=>'Enable!',1=>'Enabled'),
                           'check_title_value'=>array(0=>'Enable version control from this site!',1=>'Disable version control from this site!'),
                           'all_disabling'=>array(0=>'Disable all!',1=>'Disable all sites for Version Control!')
                         ),
                    "gazie_site_update.php" =>
                   array(  'title' => "Aggiornamento del sito web",
                           'errors'=>array('Il server non &egrave; stato trovato',
                                           'Impossibile fare il login, credenziali errate',
                                           'Direttorio inesistente',
                                           'Uno o pi&ugrave; file non sono stati aggiornati'
                                          ),
                           'server'=>'Nome del server FTP es: ftp.devincentiis.it',
                           'user'=>'User - Nome utente per l\'autenticazione',
                           'pass'=>'Password per l\'autenticazione',
                           'path'=>'Directory radice del sito es. public_html/ opp. www/',
                           'head_title'=>'Descrizione aggiuntiva al titolo',
                           'head_subtitle'=>'Descrizione aggiuntiva al sottotitolo',
                           'author'=>'Autore del sito (meta author)',
                           'keywords'=>'Parole chiave del sito (meta keywords)',
                           'listin'=>'Pubblicazione',
                           'listin_value'=>array(0=>'Non pubblicare il listino',1=>'Listino senza prezzi',2=>'Listino con prezzi online'),
                           'addpage'=>'Aggiungi pagina al sito'
                         ),
                    "gaziecart_update.php" =>
                   array(  'title' => "Actualizaci&oacute;n del cat&aacute;logo en l&iacute;nea, extensi&oacute;n GAzieCart para Joomla!",
                           'errors'=>array('Servidor FTP que no se encuentra',
                                           'Autenticaci&oacute;n: las credenciales son incorrectas',
                                           'Directorio no encontrado',
                                           'Uno o m&aacute;s archivos no se han actualizado'
                                          ),
                           'server'=>'Nombre del servidor FTP por ejemplo: devincentiis.it',
                           'user'=>'Usuario - El nombre de usuario para la autenticaci&oacute;n',
                           'pass'=>'Contrase&ntilde;a para la autenticaci&oacute;n',
                           'path'=>'Joomla directorio ra&iacute;z Ejemplo: joomla/ o nada',
                           'listin'=>'Lista',
                           'listin_value'=>array(1=>' de venta 1',2=>' de venta 2',3=>' de venta 3','web'=>' de venta en l&iacute;nea')
                         ),
                    "backup.php" =>
                   array(  'title' => "Store up data to avoid losing work!",
                           'errors'=>array(),
                           'instructions'=>'Add the following statements',
                           'table_selection'=>'Backup of',
                           'table_selection_value'=>array(0=>' all tables of database ',1=>' only tables with prefix '),
                           'text_encoding'=>'Encoding',
                           'sql_submit'=>'Generate sql file',
                        )
                    );
?>