# SymBNB

Symbnb is a website to book accommodations. Thanks to Lior Chamla for this awesome tutorial about Symfony 4.

## Demo

You can try the live demo : [https://kevingili-symbnb.herokuapp.com/](https://kevingili-symbnb.herokuapp.com/)

## Demo login info

admin user: kevin@mail.fr | password: password

normal user: jboulay@sfr.fr | password: password

## How to use this application

### First clone the git repository

    git clone https://github.com/Kevingili/symbnb.git
    
### Then move in folder
    cd symbnb
    
### Install composer dependencies
    composer install
    
### Update .env with your credentials about database
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

### Launch migrations
    php bin/console doctrine:migrations:migrate
    
### You can launch fixtures to get some data
    php bin/console doctrine:fixtures:load

### Install npm dependencies to use css and js files
    npm install
    
### Generate minified app.css and app.js
    npm run build

### Start application !
    php bin/console server:run
