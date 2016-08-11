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


$strScript = array ("admin.php" =>
                   array( 'morning'=>"buongiorno",
                          'afternoon'=>"buon pomeriggio",
                          'evening'=>"buonasera",
                          'night'=>"buonanotte",
                          'errors'=>array(' &Egrave; necessario allineare la Base Dati dalla versione ',
                                          ' alla versione ',
                                          '  cliccando QUI ' ,
                                          ' Ricordati che per il buon funzionamento dell\'appicazione la direttiva magic_quotes_gpc deve essere posta a Off nel file php.ini!',
                                          ) ,
                          'access'=>", questo &egrave; il tuo ",
                          'pass'=>"&ordm; accesso!<br />La tua password &egrave; del ",
                          'logout'=>" Se vuoi uscire clicca sul pulsante ",
                          'company'=>" Stai amministrando la ditta:<br /> ",
                          'mesg_co'=>array('Non &egrave; stato trovato nulla!','Minimo 2 caratteri','Cambia azienda di lavoro'),
                          'upd_company'=>"Modifica la configurazione dell'azienda",
                          'business'=>"per la gestione aziendale.",
                          'proj'=>"Progetto di: ",
                          'devel'=>"Sviluppo, documentazione, segnalazione errori: ",
                          'change_usr'=>"Modifica i tuoi dati",
                          'auth'=>"Sito dell'autore",
                          'strBottom' => array (
                                      array( 'href' => "http://www.kernel.org/",
                                             'img' => "linux.gif",
                                             'title' => "Linux (kernel)"),
                                      array( 'href' => "http://www.apache.org",
                                             'img' => "apache.gif",
                                             'title' => "APACHE il Server Web pi&ugrave; utilizzato nel mondo!"),
                                      array( 'href' => "http://www.mysql.com",
                                             'img' => "mysqldbms.gif",
                                             'title' => "Questo &egrave; il sito ufficiale di MySQL, il database dentro il quale Gazie archivia i suoi dati!"),
                                      array( 'href' => "http://www.php.net",
                                             'img' => "phppower.gif",
                                             'title' => "Vai al sito ufficiale di PHP, il linguaggio per il Web Dinamico!"),
                                      array( 'href' => "http://sourceforge.net/projects/tcpdf/",
                                             'img' => "tcpdf.jpg",
                                             'title' => "Qui trovi TCPDF, la classe PHP derivata da FPDF utilizzata per generare i documenti di GAzie!"),
                                      array( 'href' => "http://www.mozilla.org/products/firefox/all.html",
                                             'img' => "firefox.gif",
                                             'title' => "Scarica FIREFOX il browser con il quale &egrave; stato testato Gazie!")
                                             )
                          ),
                    "login_admin.php" =>
                   array( /* 0*/ "La nuova password dev'essere lunga almeno ",
                          /* 1*/ " caratteri,<BR> diversa dalla vecchia e uguale alla quella di conferma !<br>",
                          /* 2*/ " hai avuto accesso a Gazie<br> ma la tua password &egrave; scaduta, devi inserirne una nuova!<br>",
                          /* 3*/ " User e/o Password Errate!<br>",
                          /* 4*/ " Non sei autorizzato ad accedere a questo modulo!",
                          /* 5*/ " Nuova password ",
                          /* 6*/ " Conferma nuova password ",
                          'log'=>"Accesso al sistema localizzato in:",
                          'welcome'=>"Benvenuto in GAzie",
                          'intro'=>"il Gestionale multiAZIEndale che ti permette di tenere sotto controllo i conti, la documentazione, le vendite, gli acquisti, i magazzini e tanto altro e di molte ditte contemporaneamente.",
                          'usr_psw'=>"Inserisci il nome utente e la password che ti sono stati assegnati per iniziare:",
                          'ins_psw'=>"Inserisci Password",
                          'label_conf_psw'=>"Conferma Password",
                          'conf_psw'=>"Reinserisci la Password",
                          'label_new_psw'=>"Nuova Password",
                          'new_psw'=>"Inserisci Nuova Password",
                          ));
$errors = array (
            'access' => 'Non hai il diritto di accedere al modulo'
);
?>