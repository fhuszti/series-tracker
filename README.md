# Series-Tracker
Une application simple permettant de suivre les séries déjà vues et celles à voir parmi les 250 meilleures séries de tous les temps, selon le classement IMDb.

## Prérequis
`Docker` et `docker-compose` doivent être installés sur la machine, ainsi que `composer` et `npm` (ou `yarn`).

## Installation
- Commencer par ajouter l'entrée suivante dans le fichier hosts de votre machine : `127.0.0.1 series_tracker.local`
- Cloner le projet à partir du repository et `cd` dans le dossier nouvellement créé
- `cd app/`
- `cp .env .env.local`
- Modifier les valeurs de `DATABASE_USER`, `DATABASE_PASSWORD` et `DATABASE_NAME` dans le fichier `app/.env.local` nouvellement créé
- `composer install`
- `npm install`
- `npm run build`
- Revenir dans le dossier parent avec `cd ..`
- Puis lancer les containers avec `docker-compose --env-file ./app/.env.local up -d`

## Import des données
Les données de séries doivent être importées depuis l'API IMDb. Pour cela, vous devez [vous y inscrire](https://imdb-api.com/), récupérer votre clé API et la renseigner dans votre fichier `.env.local`.

Vous avez droit à 100 appels API gratuits par jour.

Vous pourrez ensuite vous connecter à une session bash sur votre container `www` (cf. section `Commandes utiles` ci-dessous), et taper les commandes suivantes :

- `cd series_tracker/`
- `php bin/console d:m:m` *(seulement avant le premier import)*
- `php bin/console import:series`

Vous pouvez relancer la commande quand vous le souhaitez par la suite. 

Les séries déjà présentes en bdd seront simplement mises à jour, les nouvelles entrées dans le top seront créées dans votre base, et les séries déjà présentes en local mais qui ne sont plus présentes dans le top seront déclassées mais toujours visibles.

## Utilisation

Rendez-vous sur `http://series_tracker.local:8080/` pour accéder au site

## Commandes utiles
- `docker exec -it www_docker_symfony bash` pour ouvrir une session bash dans le container `www`
- `docker exec -it db_docker_symfony bash` puis `mysql -u USERNAME -p` (avec le nom d'utilisateur MariaDB choisi dans votre `.env.local`) pour accéder à votre base de données en CLI
