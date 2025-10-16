Create the .env file and connect to your database, then run these commands.

create this file inside root[storage] project /framework/views and /framework/cache

mkdir -p storage/framework/cache
mkdir -p storage/framework/views

composer install
npm install
npm run build
php artisan storage:link
php artisan migrate
php artisan serve


if you have a problem installing composer, see if you have enabled these extensions the php.ini file

extension=zip
extension=gd
