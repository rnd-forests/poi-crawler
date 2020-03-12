## Docker for Luxstay API Development

## Usage
### Prerequisites
We use Docker and Docker Compose for constructing the development environment of the application. Therefore, we first need to install these two softwares:

- Install Docker: https://docs.docker.com/install
- Install Docker Compose: https://docs.docker.com/compose/install

Make sure `docker` and `docker-compose` commands are available in current shell.

In order to run `docker` command without **sudo** privilege:
- Create **docker** group if it hasn't existed yet: `sudo groupadd docker`
- Add current user to **docker** group: `sudo gpasswd -a ${whoami} docker`
- You may need to logout in order to these changes to take effect.

### Environment variables file
```
$ cp .env.example .env
```

### Modules configuration
```
$ cp modules.example modules
```

By default, one module `luxstay-api` will be enabled.
You can edit the `modules` file to start only neccessary modules which, of course, reduces load for your system as less docker containers will be used.

### Helper scripts
Make these scripts executable:
```
$ sudo chmod +x up.sh down.sh
```

Start modules:
```
$ ./up.sh
```

Stop modules:
```
$ ./down.sh
```

### Update hosts file
```
0.0.0.0 luxstay-api.local
```

Accessing applications (by default ports defined in `.env` file):
- http://luxstay-api.local:8088

### Laravel configurations
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=luxstay_dev
DB_USERNAME=root
DB_PASSWORD=

...

REDIS_HOST=redis
```

### Development script
Each application has an associated special container called **workspace container**. Inside these containers, we can
run development commands such as `php artisan`, `composer`, `npm`, `yarn`, and `phpunit`.

By default, the name of the container is:
- `luxstay-api-workspace`

To run these above commands inside a given worksapce, we use `develop.sh` bash script. You first need to make that script executable:
```
$ sudo chmod +x develop.sh
```

In order to run commands, we need to provide the worksapce and the command alias:
```
$ ./develop.sh <workspace> <command>
```
Where:
- `<workspace>` can be one of `api`
- `<command>` can be `artisan` (or `art`), `composer` (or `comp`), `npm`, `yarn`, and `test`

For example, if you want to install a new composer package for Luxstay API application, we can use the following command:
```
$ ./develop.sh api composer require zizaco/entrust
```
or
```
$ ./develop.sh api comp require zizaco/entrust
```

You can edit `develop.sh` file to add more commands as your needs.


### Xdebug
In this section, we're going to configure Xdebug for our applications. For convenience, we'll use PHPStorm IDE (you can use Sublime Text as well).
We will configure our application in the following steps:

Create new PHP server in PHPStorm:
![](images/phpstorm_php_server.png)

Next, we need to configure our debugger using **PHP Remote Debug** option:
![](images/phpstorm_remote_debug.png)

Xdebug can be configured through some environment variables:
- `XDEBUG_HOST`
- `XDEBUG_PORT` (default to `9000`)
- `XDEBUG_IDE_KEY` (default to `docker`)

`XDEBUG_HOST` should be the IP address of your local machine (you can grab that via `ifconfig` command). If you don't use the default
value for `XDEBUG_PORT` and `XDEBUG_IDE_KEY`, be sure to update the configurations in PHPStorm.

#### Visual Studio Code
To debug application using VSCode, we first need to install [PHP Debug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug) extension.

Next we need to add new PHP Debug configuration for VSCode. The content of `launch.json` is similar to:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9000,
            "pathMappings": {
                "/var/www/app": "${workspaceRoot}"
            }
        },
        {
            "name": "Launch currently open script",
            "type": "php",
            "request": "launch",
            "program": "${file}",
            "cwd": "${fileDirname}",
            "port": 9000
        }
    ]
}
```

We should also disable **Everything** option in **BREAKPOINTS** of VSCode debug panel.

Happy debugging!
