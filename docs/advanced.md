# Advanced topics

## Locally hosted resources

By default, WAG will generate templates that utilise JQuery and Bootstrap files from the internet-based CDN resources.  If you'd like to host your own files, simply run `nocdn.sh` to download the files, and store them in `resources`.  Provided this folder exists on your webserver, the `bootstap.php` folder will serve these files instead of redirecting your users to the internet.

## Schemas with duplicate tables

TODO

## Creating your own template

Templates are built in Mako.

Start by updating the `manifest.yaml` file.  It contains 3 sections.

* `static` - these are basic static files - they don't change at all.
* `app` - these are applicaton-based files.  They can have variables from the `app` section.
* `schema` - these files are repeated for every schema that may exist within the application.

### app

TODO

### schema

TODO
