# Gazie Gestore Aziendale 
di Antonio de Vincentiis

> ## Installazione su Synology
Eliminazione file '.htaccess'
Configurare connessione al database in 'config/config/gconfig.php'
Dare i permessi ai file:
```
chmod 777 data/files/
chmod 777 library/tcpdf/cache/
```
Per scrivere Fatture / Preventivi contenenti numerosi articoli e si vogliono evitare errori aggiungere la seguente regola nel file 'php.ini' del server:
```
suhosin.request.max_var = 1000
suhosin.post.max_var = 1000
```
