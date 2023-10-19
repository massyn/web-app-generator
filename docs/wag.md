# Web Application Generator

## wag.py

The `wag.py` tool is the main app used to update and generate applications.

### Creating a new app

Every app needs a yaml file.  Create a new yaml file simply by running

`wag.py -yaml filename.yaml`

For more information, refer to [app-schema.md](app-schema.md)

### Updating application variables

Application variables are variables that define the overall behaviour of the application.   They are universal across all forms and pages.

To update a variable, use the `-app` option.

`wag.py -yaml filename.yaml -app title "My Application Title"`

### Updating schema variables

`wag.py -yaml filename.yaml -schema phonebook -param blueprint crud`

### Updating schema fields

`wag.py -yaml filename.yaml -schema phonebook -fields name surname phoneno notes dob`

### Updating an individual schema field

`wag.py -yaml filename.yaml -schema phonebook -fields name surname phoneno notes dob`

