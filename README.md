# Series-Tracker
Une application simple permettant de suivre les films et séries déjà vus et ceux à voir.

## Prérequis
`Docker` et `docker-compose` doivent être installés sur la machine.

## Installation
- `git clone git@github.com:fhuszti/series-tracker.git series_tracker/`
- `cd series_tracker/`
- `cp series_tracker/.env series_tracker/.env.local`
- Modifier les valeurs de `DATABASE_USER`, `DATABASE_PASSWORD` et `DATABASE_NAME` dans le fichier `.env.local` nouvellement créé

## Lancement
- `docker-compose --env-file ./series_tracker/.env.local up -d`
- Puis rendez-vous sur `http://series_tracker.local:8080/` pour voir le site

## Commandes utiles
- `docker exec -it www_docker_symfony bash` pour ouvrir une session bash dans le container `www`
- `docker exec -it db_docker_symfony bash` puis `mysql -u USERNAME -p` (avec le nom d'utilisateur MariaDB choisi dans votre `.env.local`) pour accéder à votre base de données en CLI
