## Setup
1. Installare ed Avviare docker
2. Avviare una shell su questa cartella
    ```bash
    $ docker compose up --build
    ```
3. Verificare la presenza del file `/var/www/html/src/public/css/output.css` nel container `web`
   - nel caso non ci fosse è necessario allora compilare il css, usare in tale container:
    ```bash
    $ npx tailwindcss -i /var/www/html/src/public/css/input.css -o /var/www/html/src/public/css/output.css
    ```
## Utenti pre caricati
- Questo account è il proprietario di un Team con delle attivita gia impostate (nb. qui si può accedere alla sezione per la gestione del Team)
  - Username: Ago
  - Password: vallePassword123!
- Questo account ha delle attivita nel area privata preimpostate e partecipa al team di Ago
  - Username: Pino
  - Password: PinoPassword1
- Questo account partecipa al team sopracitato
  - Username: Giacomo
  - Password: GiaPasswordComo