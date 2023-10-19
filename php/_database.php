<?php

class database {
    public function __construct( $config) {
        $this->config = $config;

        if($this->config['database'] == 'flatfile'){
            # ignore
        } elseif($this->config['database'] == 'sqlite'){
            if(!isset($this->config['file'])) {
                print "sqlite needs a file parameter";
                exit(1);
            }
            try {
                $this->dbi = new PDO('sqlite:' . $config['file'], '', '', [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch(Exception $e){
                echo "Error connecting to db: " . $e->getMessage();
                exit(1);
            }
        } elseif ($this->config['database'] == 'mysql'){
            try {
                $this->dbi = new PDO(
                    'mysql:host=' . $this->config['host'] . 
                    ';port=' . $this->config['port'] . 
                    ';dbname=' . $this->config['db'],
                    $this->config['username'],
                    $this->config['password'], [
                        PDO::ATTR_PERSISTENT => false
                    ]);
            } catch(Exception $e){
                echo "Error connecting to db: " . $e->getMessage();
                exit(1);
            }
        } else {
            print "unknown database type (connect) - " . $this->config['database'];
            exit(1);
        }
    }

    function validGuid($in) {
        $guid_regex = "/^(?:\\{{0,1}(?:[0-9a-fA-F]){8}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){4}-(?:[0-9a-fA-F]){12}\\}{0,1})$/"; 
        return preg_match($guid_regex, $in);
    }

    function table_exists($table){
        if($this->config['database'] == 'mysql'){
            try {
                $stmt = $this->dbi->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e){
                return false;
            }
        } elseif($this->config['database'] == 'sqlite') {
            try {
                $stmt = $this->dbi->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ?");
                $stmt->execute([$table]);
                return $stmt->fetchColumn() !== false;
            } catch (PDOException $e){
                return false;
            }
        } elseif($this->config['database'] == 'flatfile') {
            return is_dir($this->config['datapath'] . '/' .  $this->config['prefix'] . $tableName );
        } else {
            print "unknown database type (table_exists) - " . $this->config['database'];
            exit(1);
        }
    }

    function create_table($tableName) {
        if($this->config['database'] == 'flatfile') {
            if(!is_dir($this->config['datapath'] . '/' .  $this->config['prefix'] . $tableName )) {
                mkdir($this->config['datapath'] . '/' .  $this->config['prefix'] . $tableName);
                $result = False;
            }
        } elseif($this->config['database'] == 'sqlite') {
            $result = $this->table_exists($this->config['prefix'] . $tableName );
            if ($result == 0) {
                $sth = $this->dbi->prepare("CREATE TABLE IF NOT EXISTS " .  $this->config['prefix'] . $tableName . "(id VARCHAR(40) PRIMARY KEY)");
                $sth->execute();
            }
        } elseif($this->config['database'] == 'mysql'){
            $result = $this->table_exists($this->config['prefix'] . $tableName );
            if ($result == 0) {
                $sth = $this->dbi->prepare("CREATE TABLE IF NOT EXISTS " . $this->config['prefix'] . $tableName . " (id VARCHAR(40) PRIMARY KEY NOT NULL)");
                $sth->execute();
            }
        } else {
            print "unknown database type (create_table) - " . $this->config['database'];
            exit(1);
        }

        $this->create_field($tableName,'_createdon','text');
        $this->create_field($tableName,'_createdby','text');
        $this->create_field($tableName,'_createdip','text');
        $this->create_field($tableName,'_updatedon','text');
        $this->create_field($tableName,'_updatedby','text');
        $this->create_field($tableName,'_updatedip','text');

        return $result;
    }

    function create_field($tableName,$fieldName,$fieldType) {
        // determine the SQL data type
        $maps = [
            'textarea'  => 'TEXT',
            '*'         => 'VARCHAR(255)',
        ];
        if(!isset($maps[$fieldType])) {
            $type = $maps['*'];
        } else {
            $type = $maps[$fieldType];
        }

        if($this->config['database'] == 'flatfile') {
            return ;
        } elseif($this->config['database'] == 'sqlite') {

            // does the column exist?
            $stmt = $this->dbi->prepare("SELECT COUNT(*) AS CNTREC FROM pragma_table_info('" . $this->config['prefix'] . $tableName . "') WHERE name= :fieldName");
            $stmt->execute(['fieldName' => $fieldName]);
            $data = $stmt->fetchAll()[0];
            if($data['CNTREC'] == 0) {
                $sth = $this->dbi->prepare("ALTER TABLE " . $this->config['prefix'] . $tableName . " ADD COLUMN $fieldName $type default null");
                try {
                    $sth->execute() ;
                } catch (PDOException $e) {
                    print $e->getMessage ();
                }
            }
        } elseif($this->config['database'] == 'mysql'){
            $sth = $this->dbi->prepare("ALTER TABLE " .  $this->config['prefix'] . $tableName . " ADD COLUMN $fieldName $type default null");
            try {
                $sth->execute() ;
            } catch (PDOException $e) {
                print $e->getMessage ();
            }
        } else {
            print "unknown database type (create_field) - " . $this->config['database'];
            exit(1);
        }
    }

    function insert($table,$data) {
        $data['id'] = GUID();
        $data['_createdon'] = date("Y-m-d H:i:s");
        $data['_createdby'] = $_SESSION['emailaddress'] ?? 'unknown';
        $data['_createdip'] = clientIP();
        $data['_updatedon'] = date("Y-m-d H:i:s");
        $data['_updatedby'] = $_SESSION['emailaddress'] ?? 'unknown';;
        $data['_updatedip'] = clientIP();

        if($this->config['database'] == 'flatfile') {
            $file = $this->config['datapath'] . '/' . $this->config['prefix'] . $table . '/' . $data['id'] . '.json';
            return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)); 
        } elseif ($this->config['database'] == 'sqlite') {
            $F = [];
            $V = [];
            foreach ($data as $field => $value) {
                array_push($F,$field);
                array_push($V,":$field");
            }
            $sql = "INSERT INTO " . $this->config['prefix'] . $table . " (" .
            join(',',$F) . ") VALUES (" .
            join(',',$V) . ")";
            $stmt = $this->dbi->prepare($sql);
            return $stmt->execute($data);
        } elseif ($this->config['database'] == 'mysql') {
            $F = [];
            $V = [];
            foreach ($data as $field => $value) {
                array_push($F,$field);
                array_push($V,":$field");
            }
            $sql = "INSERT INTO " . $this->config['prefix'] . $table . " (" .
            join(',',$F) . ") VALUES (" .
            join(',',$V) . ")";
            $stmt = $this->dbi->prepare($sql);
            return $stmt->execute($data);
        } 
    }

    function listOfFields($data,$fields) {
        $result = [ ];
    
        foreach ($data as $record) {
            $row = '';

            foreach($fields as $f) {
                $row .= $record[$f] . " ";
            }
            array_push($result,[$record['id'] , $row]);
        }
        return $result;
    }

    function scanTable($table) {
        $data = [];
        if($this->config['database'] == 'flatfile') {
            $files = glob($this->config['datapath'] . '/' . $this->config['prefix'] . $table . '/*.json', GLOB_BRACE);
            foreach ($files as $file) {
                array_push($data,json_decode(file_get_contents($file),true));
            }   
        } elseif($this->config['database'] == 'sqlite') {
            $sql = "SELECT * FROM " . $this->config['prefix'] . $table;
            $sth = $this->dbi->prepare($sql);
            $sth->execute();
            $data = $sth->fetchAll();
        } elseif($this->config['database'] == 'mysql') {
            $sql = "SELECT * FROM " . $this->config['prefix'] . $table;
            $sth = $this->dbi->prepare($sql);
            $sth->execute();
            $data = $sth->fetchAll();
        }
        return $data;
    }

    function delete($table,$id) {
        if($this->validGuid($id)) {
            if($this->config['database'] == 'flatfile') {
                $file = $this->config['datapath'] . '/' . $this->config['prefix'] . $table . '/' . $id . '.json';
                return unlink($file);
            } elseif($this->config['database'] == 'sqlite') {
                return $this->dbi->prepare("DELETE FROM " . $this->config['prefix'] . $table . " WHERE id = :id")->execute([ 'id' => $id ]);
            } elseif($this->config['database'] == 'mysql') {
                return $this->dbi->prepare("DELETE FROM " . $this->config['prefix'] . $table . " WHERE id = :id")->execute([ 'id' => $id ]);
            }
        }
        return False;
    }

    function read($table,$id) {
        if($this->validGuid($id)) {
            if($this->config['database'] == 'flatfile') {
                $file = $this->config['datapath'] . '/' . $this->config['prefix'] . $table . '/' . $id . '.json';
                return json_decode(file_get_contents($file),true);
            } elseif($this->config['database'] == 'sqlite') {
                $sql = "SELECT * FROM " . $this->config['prefix'] . $table . " WHERE id = :id";
                $stmt = $this->dbi->prepare($sql);
                $stmt->execute(['id' => $id]);
                $data = $stmt->fetchAll()[0];
                return $data;
            } elseif($this->config['database'] == 'mysql') {
                $sql = "SELECT * FROM " . $this->config['prefix'] . $table . " WHERE id = :id";
                $stmt = $this->dbi->prepare($sql);
                $stmt->execute(['id' => $id]);
                $data = $stmt->fetchAll()[0];
                return $data;
            } 
        }
        return False;
    }

    function search_record($table,$field,$value) {
        // This is a particularly bad piece of code.  We do it like this for now to keep it database agnostic.
        $data = $this->scanTable($table);
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] === $value) {
                return $item;
            }
        }
        return null;
    }

    function modify($table,$data) {
        if($this->validGuid($data['id'])) {
            if(!isset($data['id'])) {
                return false;
            }
            $data['_updatedon'] = date("Y-m-d H:i:s");
            $data['_updatedby'] = $_SESSION['emailaddress'];
            $data['_updatedip'] = clientIP();

            if($this->config['database'] == 'flatfile') {
                $file = $this->config['datapath'] . '/' . $this->config['prefix'] . $table . '/' . $data['id'] . '.json';
                $original = $this->read($table,$data['id']);
                foreach ($data as $key => $value) {
                    $original[$key] = $value;
                }
                return file_put_contents($file, json_encode($original, JSON_PRETTY_PRINT)); 
            } elseif($this->config['database'] == 'sqlite') {
                $sql = "UPDATE " . $this->config['prefix'] . $table . " SET ";
                foreach ($data as $field => $value) {
                    $sql .= "$field = :$field,";
                }
                $sql = rtrim($sql,",") . " WHERE id = :id";  
                $stmt = $this->dbi->prepare($sql);
                return $stmt->execute($data);
            } elseif($this->config['database'] == 'mysql') {
                $sql = "UPDATE " . $this->config['prefix'] . $table . " SET ";
                foreach ($data as $field => $value) {
                    $sql .= "$field = :$field,";
                }
                $sql = rtrim($sql,",") . " WHERE id = :id";  
                $stmt = $this->dbi->prepare($sql);
                return $stmt->execute($data);
            }
        }
        return False;
    }
}