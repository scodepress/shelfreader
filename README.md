 
## This version of Shelfreader is out of date. There is a new repository with updated code at the link below:

<p style="font-size:3em;"><a href="https://github.com/scodepress/shelfreader2023" target="_blank">ShelfReader2023</a></p>

## About ShelfReader

ShelfReader is an open source web application developed by Corey Wetherington and Scott Wagner at Penn State University Libraries. This application is being used by nearly all of Penn State's libraries. The user interface presents a visual display of shelved items and indicates which books need to be moved and where they need to be moved in order to correct shelving mistakes.  It also flags items that have item record metadata errors.


## Prerequisites

1. You must have <a href="https://php.net" target="_blank">php</a> installed on your computer.
2. The php dependency manager <a href="https://getcomposer.org/" target="_blank">Composer</a>.
3. A databse (MySQL, PostgreSQL, sqlite).
4. To run the app in production rather than with the php development server as shown below, you would need Nginix or Apache.

ShelfReader was developed using Laravel, a php framework. As an alternative to installing prerequisites on the host computer, there are several options for running Laravel applications in docker containers such as <a href="https://laravel.com/docs/8.x/sail" target="_blank">Sail</a> or <a href="https://laradock.io/" target="_blank">Laradock</a>.

## Installation

Clone the code to a directory on your computer. 

In the application root (the directory where the "app" folder is located):

1. Open a command prompt in that directory and type "composer install". 

2. Rename the file named .env.example to .env, and insert the correct values for your database connection.

3. To create the database tables type "php artisan migrate".

4. Run "php artisan key:generate" to install an encryption key for your app. 

5. Run the command "php artisan serve" to start the app in the php development server and go to the address shown in the output.


## Contributing

Thank you for considering contributing to ShelfReader! You can contact me at smw284@psu.edu.

## License

ShelfReader is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# ShelfReader
