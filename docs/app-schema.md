# Application schema

## app

|**Key**|**Description**|
|--|--|
|`title`|Application title - cosmetic and shown on all pages|
|`datetime`|A time stamp added to the file every time the `wag.py` program is run|

## schema
|**Key**|**Description**|
|--|--|
|`blueprint`|Not in use yet|
|`table`|The database table to use|
|`fields`|See below|
|`parent`|Specify the parent item in the menu|
|`table`|Specify the database table to use|
|`tag`|The short name used for the forms|

## schema.fields

|**Key**|**Description**|
|--|--|
|`default`|The default value to insert|
|`desc`|The caption to use in forms and lists|
|`helptext`|Text to show at the field to provide the user with some context.|
|`options`|When using the `type` of `dropdown`, this list will contain the options for the dropdown.|
|`required`|`True` or `False` to indicate if the field is required of not|
|`tag`|The field name to use.|
|`type`|The type of data field|
|`on_list`|Specify if the field should be shown on a list.  Change to `false` to hide the field from a list.|
|`can_delete`|Specify if the page should allow deletes|
|`can_add`|Specify if the page should allow insert|
|`can_edit`|Specify if the page should allow edits|

## schema.fields.type

|**Key**|**Description**|
|--|--|
|`text`|A simple text field|
|`textarea`|A multi-line text box|
|`dropdown`|A simple dropdown box.  You must have the `options` field also specified|
|`lookup`|Just like the dropdown, except it looks up the values from the database.|
