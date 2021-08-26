# Series-Tracker
Une application simple permettant de suivre les séries déjà vues et celles à voir parmi les 250 meilleures séries de tous les temps, selon le classement IMDb.

## Prérequis
`Docker` et `docker-compose` doivent être installés sur la machine.

## Installation
- `git clone git@github.com:fhuszti/series-tracker.git series_tracker/`
- `cd series_tracker/`
- `cp app/.env app/.env.local`
- Modifier les valeurs de `DATABASE_USER`, `DATABASE_PASSWORD` et `DATABASE_NAME` dans le fichier `.env.local` nouvellement créé

## Lancement
- `docker-compose --env-file ./app/.env.local up -d`
- Puis rendez-vous sur `http://series_tracker.local:8080/` pour voir le site

## Import des données
Les données de séries doivent être importées depuis l'API IMDb. Pour cela, vous devez [vous y inscrire](https://imdb-api.com/), récupérer votre clé API et la renseigner dans votre fichier `.env.local`.

Vous avez droit à 100 appels API gratuits par jour.

Vous pourrez ensuite vous connecter à une session bash sur votre container `www` (cf. section `Commandes utiles` ci-dessous), et taper les commandes suivantes :

- `cd series_tracker/`
- `php bin/console d:m:m` *(seulement avant le premier import)*
- `php bin/console import:series`

## Commandes utiles
- `docker exec -it www_docker_symfony bash` pour ouvrir une session bash dans le container `www`
- `docker exec -it db_docker_symfony bash` puis `mysql -u USERNAME -p` (avec le nom d'utilisateur MariaDB choisi dans votre `.env.local`) pour accéder à votre base de données en CLI
