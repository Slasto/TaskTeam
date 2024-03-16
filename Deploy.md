Dopo aver installato/avviato docker desktop

- aprire un terminale su questa cartella fare

> docker compose up

accedere ora tramite docker desktop al terminale di `php:apache`

> bash

andiamo ora ad installare ed abilitare pdo_mysql
> php --ini
> cd /usr/local/etc/php
> apt-get update && apt-get install -y nano

modificare ora i file "php.ini-production" "php.ini-development" decommentare la stringa `extension=pdo_mysql`

> nano filename
 
Ctrl+W è possibile cercare nel file la stringa sopra citata, una volta eliminato il ";" CTL+X per salvare

per installare i driver pdo
> docker-php-ext-install pdo pdo_mysql
 
> a2enmod rewrite
> cd /etc/apache2/sites-available/
verificare la presenza del file di configurazione abilitarlo:
> a2ensite swbd_Project.conf
> service apache2 reload

il file css di tailwind è gia compilato quindi non ci sarà bisogno di Ulteriori passaggi su questo container

Ultimo passo importare il "db.sql" tramite phpmyadmin andando sulla macchina host "localhost:8080"
**Username: root**
**password: 1234**

dopo l'import spostarsi sul "localhost:80" per accedere al sito!

!!! Ci sono gia degli account con dati precariati !!!


---

Questo account è il proprietario di un Team con delle attivita gia impostate
(nb. qui si può accedere alla sezione per la gestione del Team)
Username: Ago
Password: vallePassword123!

---

Questo account ha delle attivita nel area privata preimpostate e partecipa al team di Ago
Username: Pino
Password: PinoPassword1


---
Questo account partecipa al team sopracitato
Username: Giacomo
Password: GiaPasswordComo