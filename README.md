# KRLX Website

Hello! Welcome to the repository for the KRLX website.
This is the actual, real code that we use on krlx.org (or at least will when this thing gets deployed).

## System requirements

You'll need to meet the system requirements of Laravel 5.6.
In particular, you'll need PHP 7.1.3 with the extensions Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, and XML.

To compile assets you'll need a recent version of Node and NPM.
If you're deploying to a machine that doesn't allow NPM (such as DreamHost Shared), you should compile assets off-site and then upload the compiled versions as a part of your final package.

## Installing

To install the software, run these commands (assuming you have a relatively recent version of `npm`, and have installed `composer` globally - if not, set those up first):

```
git clone git@github.com:krlxfm/website
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run dev
```

Note that this is a PHP/Laravel project, not Node. Node/npm are required to compile assets, but not run the site itself.

Be sure to fill in all fields of `.env` **before** running `php artisan migrate` (ideally, these should be populated right after running `php artisan key:generate`).
This is also a good time to run through the config files and change anything needed, like the default timezone.

## Google login

When populating fields in the `.env` file, you will notice Google credentials are required.
If you plan on utilizing Carleton login, you will need to connect a Google app from the [Google Cloud Console](console.developers.google.com) and set up an OAuth credential.

## Running

Once everything is installed and ready to go, you can use `php artisan serve` to spin up a quick server.
Otherwise, point Apache, nginx, or another web service to `public/index.php`, and Laravel will take it from there.

## Test suite

There are two test suites available.
The standard test suite tests the main controller logic and API, while the browser test suite tests browser interactivity with [Laravel Dusk](https://github.com/laravel/dusk).

- Standard tests can be run with `phpunit`. Travis runs these automatically.
- Browser tests can be run with `php artisan dusk`. These are not run automatically.
  (Be sure to create the `tests/Browser/console` and `tests/Browser/screenshots` directories before you run your first Dusk tests.
  Output from failed tests will be palced here.)
  **Dusk tests will refuse to run in production environments,** so be sure to remove Dusk in production.
