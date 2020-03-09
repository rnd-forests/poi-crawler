# Deployment

## App

generate app & jwt key

```
php artisan key:generate
#php artisan jwt:secret
```

## Database

Config `DB_...` variable in `.env`

###Import database

### Migration

```
php artisan migrate
```

### Run seeder

```
composer dump-autoload
php artisan db:seed
```

### Install passport

```
php artisan passport:install
```

## Queue

Set `QUEUE_DRIVER` in `.env` to "database"

Run queue

```
php artisan queue:work --queue=high,default --tries=3
```
