# Mercuriale
Test technique foodomarket

Utilisation du repo symfony docker de dunglas

Utilisation des messages en doctrine au lieu de REDIS: REDIS n'est plus open source dans les nouvelles versions.

Pour lancer le serveur en docker
```
 make up
```

Pour lancer le messenger handler
```
make message
```

# FAQ

Si les assets de easyadmin ne se chargent pas, il est possible que le composer install ne l'ai pas fait. Pour y remedier:

```
 docker compose exec php bin/console assets:install --symlink
```

