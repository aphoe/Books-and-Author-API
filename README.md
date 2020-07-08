# Books and Author API

## About
A laravel based backend for books and associated authors APIs. Searches for books from [Ice and Fire API](https://anapioficeandfire.com/Documentation#books).

## System requirement
1. PHP >= 7.2.5
1. MySQL or SQLite
1. Git
1. Composer - [Installation guide](https://getcomposer.org/doc/00-intro.md)
1. A Web server. Project was tested with apache 2.4

You can read up on other [requirements for Laravel 7](https://laravel.com/docs/7.x/installation#installing-laravel).

### Tools
- Postman: for testing API endpoints.
- Terminal/command prompt.

## Installation
> If you are using a web server, make sure you are running these commands in the root folder (or any of its sub folder) of your web server.

To begin, run the following command to download the project using Git
```
git clone https://github.com/aphoe/Books-and-Author-API.git book_api
```

Next, move into the new project’s folder and install all its dependencies
```
// Go into the new folder
cd book_api

//install dependencies
composer install 
```

Create the `.env` file from the `.env.example` file, using this terminal command
```
cp .env.example .env
```

The next step is to generate the Laravel application key for the project
```
php artisan key:generate
``` 

You can now run the application by running `php artisan serve` and going to http://localhost:8000/. If you are using a web server, follow the steps peculiar to your web server.

Next step is setting up your database server.
1. Create  a database using the MySQL tool you are familiar with. 
1. Open `.env` and enter the name of your database in the `DB_DATABASE` field.
1. Also **enter correct values** for the `DB_USERNAME` and `DB_PASSWORD` fields.
1. Save the `.env` file.

See section below, if you want to use SQLite

Finally, run database migration using 
```
php artisan migrate
```

### Using SQLite
You can use SQLite instead of MySQL. 

Follow the steps below to create and configure SQLite.
1. Go to the `database` folder of the project.
1. Create `test_api.sqlite` file.
1. Open up `.env` in a text editor.
1. Enter the **absolute path** to `test_api.sqlite` as the value for the `DB_DATABASE` field.

## Endpoints
> The code below uses `http://localhost` as the  URL of the project. If you didn't install the project in the root of your application, use the appropriate URL.

#### GET `/api/external-books`
Searches Ice and Fire API for a Book.

**Parameters**<br>
`name`: Name of the book eg. A Game of Thrones

**Response**<br>
Returns JSON with full result or JSON with empty `data` field if no result found.

**Error**<br>
- Returns 422 if `name` parameter is not set.
- Returns 404 if the Ice and Fire host cannot be resolved.
- Returns 400 if any other error occurs.

A JSON data is returned, in each case, with appropriate error message.

#### POST `/api/v1/books`
Creates a Book and saves to local database.

**Parameters**<br>
`name`<br>
`isbn`<br>
`authors`<br>
`country`<br>
`number_of_pages`<br>
`publisher`<br>
`release_date`<br>

The `authors` parameter allows for having more than one Author. To save more than one Author, separate authors with a pipe (`|`).<br> E.g. `George R. R. Martin|Jon Snow|Tyrion Lannister` indicates that there are three authors and saves each Author separately.

**Response**<br>
Returns JSON with newly created Book.

**Error**<br>
Returns 422 if any of the parameters are missing or does not conform with validation rules. The error that occurred is returned as a JSON  data.

#### GET `/api/v1/books`
Lists books in the local database. List can also be optionally filtered by name (string), country (string), publisher (string) and release date (year, integer).

**Parameters**<br>
All parameters are optional
`name`<br>
`country`<br>
`publisher`<br>
`release_date`<br>

**Response**<br>
Returns JSON with full result or with empty data if no result found.

**Error**<br>
Returns 422 if any parameter does not conform to validation rules.

#### PATCH `/api/v1/books/:id`
Update a Book with integer ID of `:id`

**Parameters**<br>
All parameters are optional. Send only the fields you want to update.

`name`<br>
`isbn`<br>
`authors`<br>
`country`<br>
`number_of_pages`<br>
`publisher`<br>
`release_date`<br>

**Response**<br>
Returns JSON with newly updated Book.

**Error**<br>
Returns 422 if any parameter does not conform to validation rules.

#### DELETE `/api/v1/books/:id`
Deletes the Book with integer ID `:id` from the database

**Response**<br>
Returns JSON with information on the deleted Book.

**Error**<br>
Returns 404 if no Book with given is found in the database.


## Testing
### Configure for testing
Before running tests, configure the following
- Open `phpunit.xml`
- Look for the following lines (should be around Line 28)
```xml
<server name="DB_CONNECTION" value="mysql"/>
<server name="DB_DATABASE" value="..."/>
```

- Change the value of `DB_CONNECTION` to `sqlite` if you are using SQLite.
- Change the value of `DB_DATABASE` to the name of your database, if using MySQL. Or to `:memory:`, if using SQLite.

### Running test
To run test, go to the terminal and run `composer test`. 

If this doesn't work, see below section.

#### Troubleshooting
`composer test` might not work if you are working on a non-Windows system. In that case you can run `vendor/bin/phpunit` instead. 

Replace `/` in `vendor/bin/phpunit` with the directory separator of your operating system.

To make `composer test` work, open `composer.json` and change the directory separator for the `test` sub section of the `scripts` section.
```json
"scripts": {

        ...,

        "test": [
            "vendor\\bin\\phpunit"
        ],

        ...,

    }
```
