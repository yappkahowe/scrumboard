# Scrum Board

This is a simple real-time scrum board for a small team. It is built with Laravel 5.3, Vue 2, and Pusher.

## Requirement
* LAMP
* Composer

## Getting Started
* 1. Clone or download this project.
* 2. Copy .env.example to .env and change the environment values accordingly, e.g. app name, .
* 3. Enter your Pusher key, secret, app ID, and cluster to .env file.
* 4. The default authentication mechanism is Google OAuth 2 using Laravel Socialite package. Enter your Google client ID and secret for OAuth2, or you can modify the code to enable other authentication mechanism e.g. the username/password.
* 5. Edit the UsersTableSeeder and StagesTableSeeder to add users and stages to database. Stages are the columns in scrum board.
* 6. Run command: composer install
* 7. Run command: php artisan key:generate
* 8. Run command: php artisan migrate --seed
* 9. Done!

## License
This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).