<p style="font-size:22px;"><a href="https://shelfreader.com" target="_blank">ShelfReader</a></p>

## About ShelfReader

ShelfReader is an open source web application developed by Corey Wetherington and Scott Wagner at Penn State University Libraries. This application is being used by nearly all of Penn State's libraries. The user interface presents a visual display of shelved items and indicates which books need to be moved and where they need to be moved in order to correct shelving mistakes.  It also flags items that have item record metadata errors.

## Installation

Clone the code to a directory on your computer. 

In the application root (the directory where the "app" folder is located):

1. Open a command prompt in that directory and type "composer install". 

2. Rename the file named .env.example to .env, and insert the correct values for your database connection.

3. To create the database tables type "php artisan migrate".

4. Run "php artisan key:generate" to install an encryption key for your app. 

5. Run the command "php artisan serve" and go to the address shown in the output.


## Contributing

Thank you for considering contributing to ShelfReader! You can contact me at smw284@psu.edu.

## License

ShelfReader is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# ShelfReader
