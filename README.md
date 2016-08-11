# Gazie Gestore Aziendale 
di Antonio de Vincentiis

> ## Installazione su Synology
1. Eliminazione file '.htaccess'
2. Configurare connessione al database in 'config/config/gconfig.php'
3. Dare i permessi ai file:
```
chmod 777 data/files/
chmod 777 library/tcpdf/cache/
```
4. Per scrivere Fatture / Preventivi contenenti numerosi articoli e si vogliono evitare errori aggiungere la seguente regola nel file 'php.ini' del server:
```
suhosin.request.max_var = 1000
suhosin.post.max_var = 1000
```
