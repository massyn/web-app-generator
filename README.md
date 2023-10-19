# web-app-generator
Generate CRUD-style web-based applications

## Features

* Generate PHP code for a simple CRUD-like application
* Database support for SQLite, and mySQL
* Generate basic forms with text, textbox and dropdown fields.

## Requirements

* Python 3.x
* Generated app requires at least PHP 7.x
* mySQL (optional) - can run with SQLite, but not recommended

## Quick Start

Install the application and the required Python libraries.

```
$ git clone https://github.com/massyn/web-app-generator
$ cd web-app-generator
$ pip install -r requirements.txt
```

Create your first phone book application

```
$ python wag.py -yaml my_phonebook.yaml -app title "My Phonebook App"
```

Add a schema

```
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -fields name notes "Phone Number" "Email Address" dob "Contact Type"
```

Generate the application using the `php` template, and save the output to `/var/www/html`.

```
$ python wag.py -yaml my_phonebook.yaml -template php -output /var/www/html
```

**That's it!** - you should now be able to use the application.  For more informaton, refer to the [reference guide](docs/wag.md).

## Advanced options

Change the `notes` field to a multi-line `textarea` data type.

```
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -f notes type textarea
```

Let's make the `name` field a required field.

```
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -f name required True
```

We would like to add some help text to the `dob` field.

```
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -f dob helptext "Use the format as YYYY-MM-DD"
```

We would like to change the `Contact Type` field to be a drop down box, with some options added to it.
```
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -f "Contact Type" type dropdown
$ python wag.py -yaml my_phonebook.yaml -schema "The Phonebook" -f "Contact Type" options Supplier Customer
```

### The result

Once this is all done, you can generate the application again.  This time the adjustments will be applied as well.  The resulting `my_phonebook.yaml` file can be adjusted manually if needed, however do take note that manual changes to the file may result in some unexpected behaviour.

## FAQ

### What are the known security issues?

* No Multi-Factor authentication
* No Brute force detection for password misuse
* No logging of activities (yet)
* No enforcement of strong passwords
* No support for OATH2 or SAML (yet)
* No session timeout (yet)
* No role-based or any kind of authorization access control (yet)

### Why PHP?

You could argue that PHP is very old, and should not be used.  The reality is there's still plenty of PHP around.  It's one of the common languages available on almost every hosting platform available today.  My goal has been to be able to generate code that could run on a basic service like GoDaddy with very little configuration.

That doesn't mean that PHP is the only language supported.

### Is the generated code "production" ready?

Probably not.

The current release does not cater for any sort of federated access, so if you'd like to integrate the application with your SAML identity provider, you're out of luck.  The internal user database does store the passwords with a secure salted hash, but offers no ability to force password changes, MFA, or any additional identity-related features (yet).  The target state is to utilise a fully federated identify provider for the generated apps, hence support for things like MFA will not be built on this release.

So while I don't have any concerns with the code it generates, the app in its current form will not pass most compliance assessments.

### Why should I use the app then?

This is a rapid application development tool.  I've been experimenting with different usage patterns over the years, and this one is the best pattern so far.  The goal is to achieve a state where the application can be adjusted to specific business use cases, but for the moment, the recommended pattern is to generate the bulk of the code through the tool, and then manually adjusting the generated code to your specific needs.

### Why bootstrap?

The simple answer is that bootstrap was available at the time, and it was a framework I started using.

### Database "agnostic" ?

Earlier versions of the tool supported only mySQL.  Then later, only Postgres.  I also experimented with DynamoDB.  It turns out that as soon as you go down one database platform, you are very much stuck on that platform.  I opted for an approach where the application and the database is segregated.  The database module thats care of things like "inserting" a record, and making the decisions on which approach to take based on the database platform in use.  All the applications cares about, is knowing when to call the "insert" function.

This approach does bring with it some downsides.  The inability to simply execute an SQL query with special parameters does come at a cost.  The advantage though, is you have access to run the app on either a basic flatfile structure, or SQLite, allowing you to test your app in a very lightweight environment, before changing the configuration to run on a mySQL database.

## Future Plans

* Support for OATH2 (AWS Cognito) and SAML
* More data types
* Relational tables
* Generate Python Flask applications (new template)
* WAF-like capability
* Brute force detection
* Application logging
* Table audit trails
* A better way to `scanTable` that does not require the entire table to be read
* "Search" a table
* Support Postgres database
* Some basic reporting capabilities
* Blueprints (a way to create basic use cases as templates)
* Table (list) pagination

## Other ideas (not on the current roadmap)

* Generate Serverless applications - Create an app using Vue or React, with all necessary SPA pages, API Gateway, Lambda, and DynamoDB code to host a full app on AWS

## Helping out

Feel free to log an [issue](https://github.com/massyn/web-app-generator/issues/new) to report bugs, or suggest new features.