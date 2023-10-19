"""Web Application Generator - create web-based applications"""
import argparse
import os
import datetime
import shutil
import sys
import yaml
from mako.template import Template
from mako.lookup import TemplateLookup

def log(level,txt):
    """Provide a simple logging function"""
    print(f"[{level}] {txt}")

def read_app(x):
    """Read the provided file into a dictionary"""
    if os.path.exists(x):
        log("INFO",f"Reading file {x}")
        with open(x,'rt',encoding='UTF-8') as q:
            return yaml.safe_load(q)
    else:
        log("WARN",f"File {x} does not exist")
        return {}

def parse_defaults(app,default):
    """Apply defaults to the application definition"""
    log("INFO","Applying defaults")

    if not 'app' in app:
        app['app'] = {}

    if not 'schema' in app:
        app['schema'] = {}

    for x in default['app']:
        if not x in app['app']:
            app['app'][x] = default['app'][x]
            log("INFO",f" - writing default app {x} => {default['app'][x]}")

    app['app']['datetime'] = datetime.datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')

    return app

def update_app_variable(app,default,options):
    """Update the individual variable within the application definition"""
    log("INFO",f"Updating application variables - {options[0]}")
    if not options[0] in default['app']:
        log("ERROR",f"!! The -app option {options[0]} is not valid")
        exit(1)
    app['app'][options[0]] = options[1]
    return app

def update_schema_variable(app,default,schema,options):
    """Update the application schema variable"""
    if not schema in app['schema']:
        app['schema'][schema] = {}

    if options:
        log("INFO",f"Updating schema variables - {options[0]} ({options[1]})")
        if not options[0] in default['schema']:
            log("ERROR",f"!! The -param option {options[0]} is not valid")
            exit(1)
        app['schema'][schema][options[0]] = options[1]

    return app

def create_schema_fields(app,defaults,schema,fields):
    """Create the fields that will exist within a schema"""
    log("INFO",f"Create schema fields : {schema}")

    if not 'fields' in app['schema'][schema]:
        app['schema'][schema]['fields'] = []

    for f in fields:
        # Create a default blob for this field
        blob = {}

        # -- fields
        for i in defaults['schema.fields']:
            v = defaults['schema.fields'][i]
            if v == '$':
                v = f
            if v == '$$':
                v = tag(f)
            if isinstance(v,list):
                blob[i] = v[0]
                log("INFO",f" - Setting {f}.{i} = {v[0]}")
            else:
                blob[i] = v
                log("INFO",f" - Setting {f}.{i} = {v}")

        app['schema'][schema]['fields'].append(blob)

    return app

def qa_schema_fields(app,defaults):
    """Create the default fields within a schema's fields"""
    log("INFO","Create schema default")

    for schema in app['schema']:
        for i in defaults['schema']:
            if not i in app['schema'][schema]:
                v = defaults['schema'][i]
                if v == '$':
                    v = schema
                if v == '$$':
                    v = tag(schema)
                if isinstance(v,list):
                    app['schema'][schema][i] = v[0]
                    log("INFO",f" - Defaulting {schema} = {v[0]}")
                else:
                    app['schema'][schema][i] = v
                    log("INFO",f" - Defaulting {schema} = {v}")

        log("INFO","Create schema default fields")
        for blob in app['schema'][schema]['fields']:
            # -- fields
            for i in defaults['schema.fields']:
                if not i in blob:
                    v = defaults['schema.fields'][i]
                    if v == '$':
                        v = blob['desc']
                    if v == '$$':
                        v = tag(blob['desc'])
                    if isinstance(v,list):
                        blob[i] = v[0]
                        log("INFO",f" - Defaulting {blob['desc']}.{i} = {v[0]}")
                    else:
                        blob[i] = v
                        log("INFO",f" - Defaulting {blob['desc']}.{i} = {v}")
    return app

def update_schema_field(app,defaults,schema,f):
    """Update a specific schema field"""
    log("INFO",f"Update schema field {schema}.{f}")
    field = f[0]
    key = f[1]
    val = f[2]

    # -- find the field in the list
    my_id = -1
    for i,j in enumerate(app['schema'][schema]['fields']):
        if j['desc'] == field:
            my_id = i

    if my_id == -1:
        log("ERROR",f"Field {field} does not exist")
        exit(1)
    if not key in defaults['schema.fields']:
        log("ERROR",f"You specified a key that does not exist - {key}")
        sys.exit(1)
    if isinstance(defaults['schema.fields'][key],list):
        if isinstance(defaults['schema.fields'][key][0],bool):
            val = val.lower() == 'true'
        if not val in defaults['schema.fields'][key]:
            log("ERROR",f"You specified a value that is not allowed - {val}")
            log("ERROR",f"allowed options are {defaults['schema.fields'][key]}")
            exit(1)
    log("INFO",f" - Setting value to {val}")
    app['schema'][schema]['fields'][my_id][key] = val

    return app

def tag(x):
    """Replace wordy text to something that can be used as a variable"""
    return x.lower().replace(' ','_')

def generate(app,template,out):
    """Generate the app from a schema"""
    log("INFO","---------------------------------------------")
    with open(f"{template}/manifest.yaml","rt",encoding='UTF-8') as q:
        manifest = yaml.safe_load(q)

    for m in manifest['static']:
        log("INFO",f"Copying static {template}/{m}")
        shutil.copy(f"{template}/{m}",f"{out}/{m}")

    for m in manifest['app']:
        log("INFO",f"Generating app {out}/{m}")
        my_lookup = TemplateLookup(directories=['./',template])
        body = Template(
            filename=f"{template}/{m}",
            lookup = my_lookup
            ).render(
                X = app,
                APP = app['app']
            )
        with open(f"{out}/{m}","wt",encoding='UTF-8') as o:
            o.write(body)

    for m in manifest['schema']:
        for sch in app['schema']:
            s = tag(sch)

            y = manifest['schema'][m].replace('$',s)
            log("INFO",f"Generating schema {out}/{y}")
            my_lookup = TemplateLookup(directories=['./',template])
            body = Template(
                filename=f"{template}/{m}",
                lookup = my_lookup
                ).render(
                    X = app,
                    APP = app['app'],
                    SCHEMA = app['schema'][sch],
                    FIELDS = app['schema'][sch]['fields'],
                    S=sch,
                    s = s
                )
            with open(f"{out}/{y}","wt",encoding="UTF-8") as o:
                o.write(body)

def main():
    """Main application"""
    parser = argparse.ArgumentParser(description='CloudFormation Helper')
    parser.add_argument('-yaml', help='Path to the application yaml file', required=True)
    parser.add_argument('-app',help='Update the app variables',nargs='+')
    parser.add_argument('-schema',help='Update the schema')
    parser.add_argument('-param',help='Update the schema variables',nargs='+')
    parser.add_argument('-fields',help='Update the schema fields',nargs='+')
    parser.add_argument('-f',help='Update a specific schema field',nargs='+')

    parser.add_argument('-template',help='Path to the template folder')
    parser.add_argument('-output',help='Path to the output folder')

    args = parser.parse_args()

    log("INFO","Starting up")

    file_path = os.path.dirname(os.path.realpath(__file__))
    log("INFO",f"File Path = {file_path}")
    with open(f"{file_path}/wag.yaml",'rt',encoding="UTF-8") as q:
        defaults = yaml.safe_load(q)

    app = read_app(args.yaml)
    app = parse_defaults(app,defaults)

    if args.app:
        if len(args.app) != 2:
            log("ERROR","!! You cannot specify more than 2 options with the -add parameter !!")
            sys.exit(1)
        app = update_app_variable(app,defaults,args.app)

    if args.schema:
        if args.param:
            if len(args.param) != 2:
                log("ERROR","You cannot specify more than 2 options with the -param parameter.")
                sys.exit(1)
        app = update_schema_variable(app,defaults,args.schema,args.param)

        if args.fields:
            app = create_schema_fields(app,defaults,args.schema,args.fields)

        if args.f:
            if args.f[1] == 'options':
                args.f[2] = args.f[2:]
            elif len(args.f) != 3:
                log("WARN","You cannot specify more than 2 options with the -f parameter.")
            app = update_schema_field(app,defaults,args.schema,args.f)

    app = qa_schema_fields(app,defaults)

    if app is not None:
        log("INFO",f"Writing local file {args.yaml}")
        with open(args.yaml,'wt',encoding="UTF-8") as w:
            w.write(yaml.dump(app))
    else:
        log("ERROR","Something went wrong with the app file")

    # == start of generation code
    if args.template and args.output:
        generate(app,args.template,args.output)

    log("INFO","All done")

if __name__ == '__main__':
    main()
