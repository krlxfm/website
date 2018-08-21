# KRLX Website

Hello! Welcome to the repository for the KRLX website.
This is the actual, real code that we use on krlx.org (or at least will when this thing gets deployed).

## System requirements

You'll need to meet the system requirements of Laravel 5.6.
In particular, you'll need PHP 7.1.3 or later, with the extensions Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, and XML.

You will also need to install Beanstalkd (this is usually accomplished by installing `beanstalkd` from your system package manager), or another supported [Laravel queue worker](https://laravel.com/docs/5.6/queues).
The default Composer dependencies will install the drivers needed for Beanstalk.

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
php artisan krlx:gcc
php artisan db:seed
npm install
npm run dev
```

If you are upgrading an existing installation, you should also run `php artisan clear-compiled` after an upgrade.
Some of the Google services in particular like to cache themselves a little too aggressively.

Note that this is a PHP/Laravel project, not Node.
Node/npm are required to compile assets, but not run the site itself.

Be sure to fill in all fields of `.env` **before** running `php artisan migrate` (ideally, these should be populated right after running `php artisan key:generate`).
This is also a good time to run through the config files and change anything needed, like the default timezone.

## Configuration

When populating fields in the `.env` file, you will notice Google credentials are required.
If you plan on utilizing Carleton login, you will need to connect a Google app from the [Google Cloud Console](console.developers.google.com) and set up an OAuth credential.

When creating your Google Cloud project, you will need to create two Client IDs, because this app uses two different means of accessing Google Account data:

1. Create a "Web" client for standard sign-in.
   The client credentials here should be saved in the `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` environment variables.
   For `GOOGLE_REDIRECT_URL`, enter your installation location's root URL plus `/login/callback` (for instance, if you're using a standard [MAMP](https://mamp.info) server locally, this should be `http://localhost:8888/login/callback`).
   This needs to be copied to the Authorized Redirect URIs section of the Google Cloud Console as well.
2. Create an "Other" client for the command-line interface, such as Artisan.
   Enter these credentials in `GOOGLE_CALENDAR_CLIENT_ID` and `GOOGLE_CALENDAR_CLIENT_SECRET`.
   To get the calendar redirect URL, you should see a "Download JSON" option after creating the client in the Google Cloud Console.
   Click it and open the JSON file, and copy the first entry from the `redirect_uris` array into `GOOGLE_CALENDAR_REDIRECT_URL` (it should **not** start with `http://` or `https://`).

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
  **Dusk tests will refuse to run in production environments,** so if you are deploying to production, run the composer installation step as `composer install --no-dev` to prevent its installation.
