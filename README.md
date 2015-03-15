OAuth example php-discogs-api
-----------------------------

This repository is a quick example of how to obtain an OAuth token and how to make use of the protected endpoints from
the Discogs API.

# Installation

Clone this repository and install the dependencies:

    $ composer install

Add your consumer key and secret in <code>$consumerKey</code> and <code>$consumerSecret</code>.

For the sake of simplicity we're using the PHP internal webserver (quick and easy):

    $ cd web
    $ php -S localhost:8000

Now open your favorite browser and visit http://localhost:8000

# How it works

As soon as you open the site, you'll be redirect to Discogs. After authorizing you'll be redirected to the site and now
you have a OAuth token and secret with which you'll be able to access the protected endpoints.

This example is just some quick hacking together but I think it's enough to get you started.
