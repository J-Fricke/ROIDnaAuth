#Simple login web app 
A simple login web app using Laravel 5 with the ablity to register, login (username+password & Social: Facebook and Twitter), reset your password, and send emails via the Mandrill API. Requires  [Composer](https://getcomposer.org/).  

# Setup
run `composer install` and `composer update` to make sure you have all the packages required.
Create .env file in the root of the install directory and fill in your values, you can use the .env.example and replace as needed 

# .env.example
```
APP_ENV=local
APP_DEBUG=true
APP_KEY=LARAVEL_KEY

DB_HOST=localhost
DB_DATABASE=DATABASE_NAME
DB_USERNAME=DATABASE_USER
DB_PASSWORD=DATABASE_PW

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=mandrill
MAIL_HOST=smtp.mandrillapp.com
MAIL_PORT=587
MAIL_USERNAME=MANDRILL_USER
MAIL_PASSWORD=MANDRILL_KEY
MAIL_ENCRYPTION=null
MAIL_FROM=fromEmail@domain.com
MAIL_FROM_NAME='SenderName'

FACEBOOK_ID=FB_APP_ID
FACEBOOK_SECRET=FB_APP_SECRET
FACEBOOK_REDIRECT=INSTALL_URL/login/callback/facebook

TWITTER_KEY=TWTTER_KEY
TWITTER_SECRET=TWITTER_SECRET
TWITTER_REDIRECT=INSTALL_URL/login/callback/twitter
```

#Post .env Database setup
Once you have the .env data setup, you can run `php artisan migrate` to setup your database 


## Laravel PHP Framework Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).