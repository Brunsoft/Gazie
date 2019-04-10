# Gazie - Gestore Aziendale 
di Antonio de Vincentiis

> ## Installazione su Synology
* Eliminazione file `.htaccess`
* Configurare connessione al database in `config/config/gconfig.php`
* Dare i permessi ai file tramite SSH. La directory web si trova in `/volume/web/`:
```
chmod 777 data/files/
chmod 777 library/tcpdf/cache/
```
* Per scrivere Fatture / Preventivi contenenti numerosi articoli e si vogliono evitare errori aggiungere la seguente regola nel file `php.ini` del server (1000 rappresenta il valore di default, inserire quindi un valore a piacimento):
Il file `php.ini` si può trovare in `/usr/local/etc/php56/` oppure in `/usr/etc/php/`
```
suhosin.request.max_vars = 5000
suhosin.post.max_vars = 5000
suhosin.get.max_vars 5000
```
Oppure accedere al setting PHP del Synology in `Web Station > PHP Settings > Select the PHP > Default Profile > Edit > Core`
e settare `max_input_vars = 5000`.

* Per fare in modo di aprire il proprio programma di posta per inviare mail tramite apposito pulsante di dovrà procedere con la modifica dei seguenti file: `modules/vendit/report_doctra.php`, `modules/vendit/report_docven.php`, `modules/vendit/report_broven.php` vediamo come:
  - `modules/vendit/report_doctra.php`
    ```
    (264) riga originale commentata
    (265) echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$anagra["e_mail"].'?subject=Invio '.$r['tipdoc'].' N.'.$r["numdoc"].' del '.gaz_format_date($r["datemi"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    
    (328) riga originale commentata
    (329) echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$anagra["e_mail"].'?subject=Invio '.$r['tipdoc'].' N.'.$r["numdoc"].' del '.gaz_format_date($r["datemi"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    
    (386) riga originale commentata
    (387) echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$anagra["e_mail"].'?subject=Invio DDT N.'.$r["numdoc"].' del '.gaz_format_date($r["datemi"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    ```
  
  - `modules/vendit/report_docven.php`
    ```
    (507) riga originale commentata
    (508) aggiunta riga: echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$anagra["e_mail"].'?subject=Invio fattura N.'.$r["numfat"].' del '.gaz_format_date($r["datfat"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    ```
  
  - `modules/vendit/report_broven.php`
    ```
    (356) riga originale commentata
    (357) echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$r["e_mail"].'?subject=Invio '.$script_transl['type_value'][$r["tipdoc"]].' N.'.$r["numdoc"].' del '.gaz_format_date($r["datemi"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    
    (359) riga originale commentata
    (360) echo '<a class="btn btn-xs btn-default btn-email" href="mailto:'.$r["base_mail"].'?subject=Invio '.$script_transl['type_value'][$r["tipdoc"]].' N.'.$r["numdoc"].' del '.gaz_format_date($r["datemi"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
    ```
* Nel caso in cui la pagina `modules/acquis/admin_broacq.php` dia errori di accesso alla directory `data/files/n`, dove `n` è un numero > 1, creare la cartella `n` e dare tutti i permessi a tale directory.
