# sabc-dashboard

<p align="center">
<img src="https://studentaidbc.ca/sites/studentaidbc.ca/themes/nutmeg/assets/img/logo-dt.png" width="200">
<img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="200"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About StudentAidBC Dashboard

The StudentAidBC Dashboard is a web application built on the Laravel framework. Laravel features common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

## BC Services Certificates
Files must be uploaded via ftp to the directory /config/certs/
and only PROD and UAT use SSL certificates.
- pem cert (private key) is only used to decode SAML payload coming from BC Services (apply to UAT/PROD only)
- public cert received from BC Services is not used
- use the apache user to access the directory
- change permissions on the directory 'chmod 777 certs'
- upload the files
- change permissions back to 755 'chmod 755 certs'
- change/replace only the cert file after receiving it from BC Services. Keep the .pem
- please refer to [BC Services SSL Certificate](https://hive.aved.gov.bc.ca/wiki/pages/viewpage.action?spaceKey=SP&title=BC+Services+SSL+Certificate)

## VUE files updates
Any updates happen to any of the vue files the following commands must be used:
#### - for PROD: uncomment MIX_APP_ENV from .env file then run 'php artisan config:clear' and 'npm run prod'
#### - for DEV/UAT: npm run dev
Then commit all the changes and push to the repo. 

#NPM commands
Initial install for packages.json: npm install
Install to latest version: npm install [package]@latest
Update to latest version: npm update [package]@latest

Install a specific version of npm (downgrade/upgrade) globally: npm install npm@[version number] -g

Uninstall package: npm uninstall [package]
View version: npm view [package] version

Uninstall package globally: npm uninstall -g [package]

## Extra Info:
- To lock the dashboard for maintenance use the command 'php artisan down secret="whateverSecretYouLike" 
- Any visitor to the dashboard will get the under maintenance page
- To override that for testing go to https://studentaidbc.ca/dashboard/whateverSecretYouLike this will grant the current user access to the dashboard
- To bring the dashboard back on use the command 'php artisan up'

## Learning Laravel

Read Laravel [documentation](https://laravel.com/docs).

Watch videos on [Laracasts](https://laracasts.com). 

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
