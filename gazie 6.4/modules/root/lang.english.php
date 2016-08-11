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
                   array( 'morning'=>"good morning",
                          'afternoon'=>"good afternoon",
                          'evening'=>"good evening",
                          'night'=>"good night",
                          'errors'=>array(' It is necessary to align its Database version ',
                                          ' to version ',
                                          '  by clicking HERE ' ,
                                          ' He remembers that for the good application working the directive magic_quotes_gpc must be set  Off into php.ini file!',
                                          ) ,
                          'access'=>", this is your access n.",
                          'pass'=>"<br />Last password update: ",
                          'logout'=>"If you want exit click the button",
                          'company'=>" You are running the company:<br /> ",
                          'mesg_co'=>array('The search yielded no results!','Insert at least 2 characters!','Change company'),
                          'upd_company'=>"Update company config",
                          'business'=>"for the business management.",
                          'proj'=>"Project manager: ",
                          'devel'=>"Development, documentation, bug report: ",
                          'change_usr'=>"Change your data",
                          'auth'=>"Author web site",
                          'strBottom' => array (
                                      array( 'href' => "http://www.kernel.org/",
                                             'img' => "linux.gif",
                                             'title' => "Linux (kernel)"),
                                      array( 'href' => "http://www.apache.org",
                                             'img' => "apache.gif",
                                             'title' => "Apache the Web Server more used in the world!"),
                                      array( 'href' => "http://www.mysql.com",
                                             'img' => "mysqldbms.gif",
                                             'title' => "This is MySQL official web site. The database inside which GAzie memorizes hits data!"),
                                      array( 'href' => "http://www.php.net",
                                             'img' => "phppower.gif",
                                             'title' => "Go to PHP official web site, the language for Dynamic Web!"),
                                      array( 'href' => "http://sourceforge.net/projects/tcpdf/",
                                             'img' => "tcpdf.jpg",
                                             'title' => "You find TCPDF here, the PHP class FPDF based used to produce the GAzie's documents!"),
                                      array( 'href' => "http://www.mozilla.org/products/firefox/all.html",
                                             'img' => "firefox.gif",
                                             'title' => "Download FIREFOX, the browser GAzie has been tested with!")
                                             )
                            ),
                    "login_admin.php" =>
                   array( /* 0*/ " The new password to be long at least ",
                          /* 1*/ " characters,<BR> various from previous and the equal one to that one of control !<br>",
                          /* 2*/ " You have had approached the program but yours password it is past due,<br /> you must insert of one new!",
                          /* 3*/ " User and/or Password incorrect!<br>",
                          /* 4*/ " Denied access to this module !",
                          /* 5*/ " New password",
                          /* 6*/ " New confirmation password",
                          'log'=>"Access to system localized in:",
                          'welcome'=>"Welcome to GAzie",
                          'intro'=>"the Enterprise Resource Planning that allows you to keep track of the accounts, documentation, sales, purchases, warehouses and more, for many companies simultaneously.",
                          'usr_psw'=>"Enter your username and password that you have been assigned to start:",
                          'ins_psw'=>"Enter Password",
                          'label_conf_psw'=>"Confirm Password",
                          'conf_psw'=>"Re-enter Password",
                          'label_new_psw'=>"New Password",
                          'new_psw'=>"Enter New Password",
                          ));
$errors = array (
            'access' => 'You have no right of access to the module'
);
?>