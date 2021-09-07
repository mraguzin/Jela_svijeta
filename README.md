# Jelasvijeta
## Rješenje zadatka kao dio prijave za posao u Factory-ju.

### Upute
Instalirajte zadnju verziju Symfony-ja i pokrenite lokalni server, kao što je objašnjeno na https://symfony.com/doc/current/setup.html#running-symfony-applications

Zatim morate kreirati bazu podataka — u datoteci ```.env``` je već postavljena varijabla okoline ```DATABASE_URL```, no preporučljivo je da namjestite svoje privatne
podatke (po mogućnosti i neki drugi RDBMS) u datoteci ```.env.local```, koja nije uključena u Git commite. Za samu kreaciju baze, izvršite ```$ php bin/console doctrine:database:create```
i nakon toga ```$ php bin/console doctrine:migrations:migrate``` za izvesti već postojeće migracije.

Sada možete posjetiti vaš lokalni server i pristupiti endpoint-u ```/meals``` radi provjere rezultata.
