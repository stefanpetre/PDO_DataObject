<?php
/**
 * Generation tools for PDO_DataObject
 *
 * For PHP versions  5 and 7
 * 
 * 
 * Copyright (c) 2015 Alan Knowles
 * 
 * This program is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU Lesser General Public License as   
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *  
 * @category   Database
 * @package    PDO_DataObject
 * @author     Alan Knowles <alan@roojs.com>
 * @copyright  2016 Alan Knowles
 * @license    https://www.gnu.org/licenses/lgpl-3.0.en.html  LGPL 3
 * @version    1.0
 * @link       https://github.com/roojs/PDO_DataObject
 */
  
 
 /*
 * Security Notes:
 *   This class may use eval to create classes on the fly.
 *   The table name and database name are used to check the database before writing the
 *   class definitions, we now check for quotes and semi-colon's in both variables
 *   so I cant see how it would be possible to generate code even if
 *   for some crazy reason you took the classname and table name from User Input.
 *   
 *   If you consider that wrong, or can prove it.. let me know!
 */
 
 /**
 * 
 * Config _$options
 * [PDO_DataObject]
 * ; optional default = DB/DataObject.php
 * extends_location =
 * ; optional default = DB_DataObject
 * extends =
 * ; alter the extends field when updating a class (defaults to only replacing PDO_DataObject)
 * generator_class_rewrite = ANY|specific_name   // default is PDO_DataObject
 *
 */

/**
 * Needed classes
 * We lazy load here, due to problems with the tests not setting up include path correctly.
 * FIXME!
 */
class_exists('PDO_DataObject') ? '' : require_once 'PDO/DataObject.php';
//require_once('Config.php');

/**
 * Generator class
 *
 * @package PDO_DataObject
 */
class PDO_DataObject_Generator extends PDO_DataObject
{
    
    
    
     /* ---------------- ---------------- static  -------------------------------- */
    /**
     * Configuration - use config() to access this.
     *
     * @access  private
     * @static
     * @var     array
     */
    private static $config = array(
            
         
        
        // ---- Generator
              
            'build_views' => false,
                // for postgres, you can build dataobjects for views as well
                // you can set this to 'schema.views' to extract views with schema information
                // I believe  postgres also supports updating on views (nice feature)
                // *** NOTE *** You will have to manually define keys() / sequenceKeys()
                // As the generator can not recognize these automatically
                
            'strip_schema' => true,
                //	postgres has a wierd concept of schema's which end up prefixed to
                //	the list of tables. - this makes a mess of class/schema generation
                //	setting this to '', makes the generator strip the schema from the table name.
                //  now supports regex (if you set it to a regex it will strip schema of matched names)
                //  for example '/^public\./'
            'no_ini' => false,
                // (True) will generate the methods table() ,keys(), sequenceKeys() and defaults()
                // methods in the generated classes 
                // and not generate any ini file to describe the table.
                
            'extends_class' => 'PDO_DataObject',
                // what class do the generated classes extend?
            'extends_class_location' => 'PDO/DataObject.php',
                // what file is the extended class in.                
        
        // advanced customization..
            'hook' => 'PDO_DataObject_Generator_Hooks',
                // class for hooks code (used to be derivedHook****)
                // allows custom generation of PHP code.
                // can be class name or object
            'table_gen_class' => 'PDO_DataObject_Generator_Table',
                // class for table parsing/ generator
                // allows for more custom generaton.
                // if you use a custom class - you must include it before loading..
            'var_keyword' => 'public',
                // var|public  - (or private if you want to break things)
                // The variable prefix that is used when class properties are created
                //  the default is public 
            'add_database_var' => false,
                // add the line public $_database = .... (disabled by default)
            'no_column_vars' => false,
                // (True) prevents writing of private/var's so you can overload get/set 
                // note: this has the downside of making code less clear... (alot of magic!!)
            'setters' => false,
            	// (true) will generate setXXXX() methods for you.
            'getters' => false,
            	// (true) will generate getXXXX() methods for you.
            'link_methods'  =>false,
                // (true|callable) will create the wrappers around link()
                // => function($k) { return $k; } // to munge the column name into a method name.
                // Only likely to work with with mysql / mysqli / postgres  at present. ?? maybe sqlite?
            'secondary_key_match' => 'primary|unique',
                // if a column is auto-increment or nextval() - then it's determined to be a sequence key
                // if it's only primary or unique - then it's assumed to be an index, but using emulated sequences keys.
    );
      /**
     * Set/get the generator configuration...
 
     * Usage:
     *
     * Fetch the current config.
     * $cfg = PDO_DataObject_Generator::config(); 
     *
     * SET a configuration value. (returns old value.)
     * $old = PDO_DataObject_Generator::config('schema_location', '');  
     *
     * GET a specific value ** does not do this directly to stop errors...
     * somevar = PDO_DataObject_Generator::config()['schema_location'];  
     *
     * SET multiple values (returns 'old' configuration)
     * $old_array = PDO_DataObject_Generator::config( array( 'schema_location' => '' ));
     * 
     * 
     * @param   array  key/value 
     * @param   mixed value 
     * @static
     * @access  public
     * @return - the current config (or previous value/config) 
     */
     
    static function config($cfg_in = array(), $value=false) 
    {
         
        if (!func_num_args()) {
            return self::$config;
        }
        
        if (!is_array($cfg_in) && func_num_args() < 2) {
            // one arg = not an array..
            (new PDO_DataObject())->raiseError("Invalid Call to config should be string+anther value or array",
                              self::ERROR_INVALIDARGS, self::ERROR_DIE);
        }
        
        $old = self::$config;
        
        $cfg = $cfg_in;
        
        if (func_num_args() > 1) {
            // two args..
            if (!is_string($cfg_in)) {
                (new PDO_DataObject())->raiseError("Invalid Call to config should be string+anther value or array",
                              self::ERROR_INVALIDARGS, self::ERROR_DIE);    
            }
            
            $k = $cfg_in;
            $cfg = array();
            $cfg[$k] = $value;
        } 
          
        foreach ($cfg as $k=>$v) {
            if (!isset(self::$config[$k])) {
                (new PDO_DataObject())->raiseError("Invalid Configuration setting : $k",
                        self::ERROR_INVALIDCONFIG, self::ERROR_DIE);
            }
            self::$config[$k] = $v;
        }
        
        
        return is_array($cfg_in) ? $old : $old[$cfg_in];
    }
    /**
     * Associate Array of table names => Table Objects
     *
     * @var array
     * @access private
     */
    var $tables;
    
    
    
    function __construct()
    {
        $hook = self::$config['hook'];
        if (is_object($hook)) {
            $this->hook = $hook;
            return;
        }
        if ($hook == 'PDO_DataObject_Generator_Hooks') {
            class_exists($hook) ? '' :
                require_once 'PDO/DataObject/Generator/Hooks.php';
        }
        if (!class_exists($hook)) {
            $this->raiseError("Hook class '{$hook}' does not exist - please include it or use an autoloader");
        }
        $this->hook = new $hook();
    }
    
    
    /**
     * The 'starter' = call this to start the process
     *
     * @access  public
     * @return  none
     */
    function start()
    {
        $options = PDO::config();
        
        $databases = array();
        if (!empty($options['databases'])) {
            $databases  = $options['databases'];
        }
        
        
        if (isset($options['database'])) {
            // ctor without table...
            $do = new PDO_DataObject();
            $dname = $do->PDO()->database_nickname;
            
            if (!isset($database[$dname])){
                $databases[$dname] = $options['database'];
            }
        }

        
        foreach($databases as $databasename => $database) {
            if (!$database) {
                continue;
            }
            $this->debug("CREATING FOR $databasename\n",__FUNCTION__,1);
            $class = get_class($this);
            $t = new $class();
            $t->_database_dsn = $database;
            $t->_database = $databasename;
            
            $t->_readTableList();
            $t->_readForeignKeys();

            foreach(get_class_methods($class) as $method) {
                if (substr($method,0,8 ) != 'generate') {
                    continue;
                }
                $this->debug("calling $method");
                $t->$method();
            }
        }
        $this->debug("DONE\n\n");
    }

  /**
     *  
     * 'proxy' version of databaseStructure - this is not so 'speed sensitive'
     * only used when
     * b) proxy is set..
     
    *
     *  - set's the structure.. and the links data..
          
     *
     * obviously you dont have to use ini files.. (just return array similar to ini files..)
     *  
     * It should append to the table structure array 
     *
     *     
     * @param optional string  name of database to assign / read
     * @param optional array   structure of database, and keys
     * @param optional array  table links
     * @return (varies) - depends if you are setting or getting...
     */
    
    function databaseStructureProxy($database, $table = false)
    {
        
        $this->database( $database );
        $this->PDO();
        $this->_readTableList();
         
            // prevent recursion...
            
        $old = PDO_DataObject::config('proxy', false);
        $ret = $this->databaseStructure(); 
        PDO_DataObject::config('proxy', $old);
        return $ret;
            // databaseStructure('mydb',   array(.... schema....), array( ... links')
         
            // will not get here....
    }    
    
     /**
     * create an instance of introspection. 
     * - manual set
     * - proxy
     * - ini_****
     */
    protected function introspection()
    {
        
        $type  = $this->PDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        if (empty($type)) {
            throw new Exception("could not work out database type");
        }
        $class = 'PDO_DataObject_Introspection_'. $type;
        class_exists($class)  ? '' : require_once 'PDO/DataObject/Introspection/'. $type. '.php';
        $this->debug("Creating Introspection for $class", "_introspection");
        return new $class( clone($this) ); /// clone so we can run multipel queries?
       
    }

    
   
    /**
     * Build a list of tables and definitions.;
     * and store it in $this->tables and $this->_definitions[tablename];
     *
     * @access  private
     * @return  none
     */
    function _readTableList()
    {
        $options = self::config();
        
        $pdo = $this->PDO();
        $io  = $this->introspection();
        
        $tables = array();
        
        // try schema first...
        try {
            $tables = $io->getListOf('schema.tables');
        } catch (Exception $e) {     
        }
        
        if (empty($this->tables)) {
            $tables = $io->getListOf('tables');
        }
        
 
        // build views as well if asked to.
        if (!empty($options['build_views'])) {
            $views =$io->getListOf(
                    is_string($options['build_views']) ?
                                $options['build_views'] : 'views'
            );
            
            $tables = array_merge ($tables, $views);
        }
        $tcls = self::config('table_class');
        
        if ($tcls== 'PDO_DataObject_Generator_Table') {
            class_exists($tcls) ? '' :
                require_once 'PDO/DataObject/Generator/Table.php';
        }
        if (!class_exists($tcls)) {
            $this->raiseError("Table class '{$tcls}' does not exist - please include it or use an autoloader");
        }
        

        // declare a temporary table to be filled with matching tables names
        $tmp_table = array();


        foreach($tables as $table) {
            if ($options['include_regex'] &&
                    !preg_match($options['include_regex'],$table)) {
                $this->debug("SKIPPING (include_regex) : $table", __FUNCTION__,1);
                continue;
            } 
            
            if ($options['exclude_regex'] &&
                    preg_match($options['exclude_regex'],$table)) {
                $this->debug("SKIPPING (exclude_regex) : $table", __FUNCTION__,1);
                continue;
            }
            
            $strip = $options['strip_schema'];
            $strip = (is_string($strip) && strtolower($strip) == 'true') ? true : $strip;
        
            // postgres strip the schema bit from the
            if (!empty($strip) ) {
                
                if (!is_string($strip) || preg_match($strip, $table)) { 
                    $bits = explode('.', $table,2);
                    $table = $bits[0];
                    if (count($bits) > 1) {
                        $table = $bits[1];
                    }
                }
            }
            $this->debug("EXTRACTING : $table");
            
            // we do not quote table - as these are now internal methods - and it is done by the introspection classes 
            $this->tables[$table] = new $tcls($this, $table);
            
             


        }
         
        //print_r($this->_definitions);
    }
    
    /**
     * Auto generation of table data.
     *
     * it will output to db_oo_{database} the table definitions
     *
     * @access  private
     * @return  none
     */
    function generateINI()
    {
        $this->debug("Generating Definitions INI file:        ");
        if (!$this->tables) {
            $this->debug("-- NO TABLES -- \n");
            return;
        }

        $options = PDO_DataObject::config();

        
        if (empty($options['schema_location']) ) {
            return;
        }
        
        if (!empty($options['generator_no_ini'])) { // built in ini files..
            return;
        }
        
        $out = '';
        foreach($this->tables as $table) {
            $out .= $table->toIniString();
        }

        $this->PDO();
        // dont generate a schema if location is not set
        // it's created on the fly!

        // where to generate the schema...
        $base = $options['schema_location'];
        
        if (is_array($base)) {
            if (!isset($base[$this->_database])) {
                $this->raiseError("Could not find schema location from config[schema_location] - array but no matching database",
                    PDO_DataObject::ERROR_INVALIDCONFIG, PDO_DataObject::ERROR_DIE);
            }
            $base =  explode(PATH_SEPARATOR, $base[$this->_database])[0]; // get the first path...

            $file = $base[$this->_database];
        } else {
            $base =  explode(PATH_SEPARATOR, $options['schema_location'])[0]; // get the first path...

            $file = "{$base}/{$this->_database}.ini";
        }
        
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        $this->debug("Writing ini as {$file}\n");
        //touch($file);
        $tmpname = tempnam(session_save_path(),'PDO_DataObject_');
        //print_r($this->_newConfig);
        $fh = fopen($tmpname,'w');
        if (!$fh) {
            return $this->raiseError(
                "Failed to create temporary file: $tmpname\n".
                "make sure session.save_path is set and is writable\n"
                ,null, PDO_DataObject::ERROR_DIE);
        }
        fwrite($fh,$this->_newConfig);
        fclose($fh);
        $perms = file_exists($file) ? fileperms($file) : 0755;
        // windows can fail doing this. - not a perfect solution but otherwise it's getting really kludgy..
        
        if (!@rename($tmpname, $file)) { 
            unlink($file); 
            rename($tmpname, $file);
        }
        chmod($file,$perms);
        //$ret = $this->_newConfig->writeInput($file,false);

        //if (PEAR::isError($ret) ) {
        //    return PEAR::raiseError($ret->message,null,PEAR_ERROR_DIE);
        // }
    }
     /**
     * create the data for Foreign Keys (for links.ini) 
     * Currenly only works with mysql / mysqli / posgtreas
     * to use, you must set option: generate_links=true
     * 
     * @author Pascal Sch�ni 
     */
    
    function _readForeignKeys()
    {
        $options = PDO_DataObject::config();
        
        if (empty($options['generate_links'])) {
            return false;
        }
        $io = $this->introspection();
        foreach($this->tables as $table) {
            $fk[$table] = $io->foreignKeys($table);
            
        }
        $this->_fkeys = $fk;
    }
    
     
    /**
     * generate Foreign Keys (for links.ini) 
     * Currenly only works with mysql / mysqli
     * to use, you must set option: generate_links=true
     * 
     * @author Pascal Sch�ni 
     */
    function generateForeignKeys() 
    {
        $options = PEAR::getStaticProperty('DB_DataObject','options');
        if (empty($options['generate_links'])) {
            return false;
        }
        $__DB = &$GLOBALS['_DB_DATAOBJECT']['CONNECTIONS'][$this->_database_dsn_md5];
        if (!in_array($__DB->phptype, array('mysql', 'mysqli', 'pgsql'))) {
            echo "WARNING: cant handle non-mysql and pgsql introspection for defaults.";
            return; // cant handle non-mysql introspection for defaults.
        }
        $this->debug("generateForeignKeys: Start");
        
        $fk = $this->_fkeys;
        $links_ini = "";

        foreach($fk as $table => $details) {
            $links_ini .= "[$table]\n";
            foreach ($details as $col => $ref) {
                $links_ini .= "$col = $ref\n";
            }
            $links_ini .= "\n";
        }
      
        // dont generate a schema if location is not set
        // it's created on the fly!
        $options = PEAR::getStaticProperty('DB_DataObject','options');

        if (!empty($options['schema_location'])) {
             $file = "{$options['schema_location']}/{$this->_database}.links.ini";
        } elseif (isset($options["ini_{$this->_database}"])) {
            $file = preg_replace('/\.ini/','.links.ini',$options["ini_{$this->_database}"]);
        } else {
            $this->debug("generateForeignKeys: SKIP - schema_location or ini_{database} was not set");
            return;
        }
         

        if (!file_exists(dirname($file))) {
            mkdir(dirname($file),0755, true);
        }

        $this->debug("Writing ini as {$file}\n");
        
        //touch($file); // not sure why this is needed?
        $tmpname = tempnam(session_save_path(),'DataObject_');
       
        $fh = fopen($tmpname,'w');
        if (!$fh) {
            return PEAR::raiseError(
                "Failed to create temporary file: $tmpname\n".
                "make sure session.save_path is set and is writable\n"
                ,null, PEAR_ERROR_DIE);
        }
        fwrite($fh,$links_ini);
        fclose($fh);
        $perms = file_exists($file) ? fileperms($file) : 0755;
        // windows can fail doing this. - not a perfect solution but otherwise it's getting really kludgy..
        if (!@rename($tmpname, $file)) { 
            unlink($file); 
            rename($tmpname, $file);
        }
        chmod($file, $perms);
    }

      
    

   
    
     /**
    * Convert a column name into a method name (usually prefixed by get/set/validateXXXXX)
    *
    * @access  public
    * @return  string method name;
    */
    
    
    function getMethodNameFromColumnName($col)
    {
        return ucfirst($col);
    }
    
    
    
    
    /*
     * building the class files
     * for each of the tables output a file!
     */
    function generateClasses()
    {
        //echo "Generating Class files:        \n";
        $options = &PEAR::getStaticProperty('DB_DataObject','options');
        

        foreach($this->tables as $table) {
            
            
            $cn = $table->toPhpClassName();
            $fn = $table->toPhpFileName();
            
            $oldcontents = '';
            if (file_exists($fn)) {
                // file_get_contents???
                $oldcontents = implode('',file($fn));
            }
            
            $out = $table->toPhp($oldcontents);
            
            $this->debug( "writing $cn\n");
            $tmpname = tempnam(session_save_path(),'PDO_DataObject_');
       
            $fh = fopen($tmpname, "w");
            if (!$fh) {
                return PEAR::raiseError(
                    "Failed to create temporary file: $tmpname\n".
                    "make sure session.save_path is set and is writable\n"
                    ,null, PEAR_ERROR_DIE);
            }
            fputs($fh,$out);
            fclose($fh);
            $perms = file_exists($fn) ? fileperms($fn) : 0755;
            if (!file_exists(dirname($fn))) {
                mkdir(dirname($fn),$perms, true);
            }
            
            // windows can fail doing this. - not a perfect solution but otherwise it's getting really kludgy..
            if (!@rename($tmpname, $fn)) {
                unlink($fn); 
                rename($tmpname, $fn);
            }
            
            chmod($fn, $perms);
        }
        //echo $out;
    }

    /**
     * class being extended (can be overridden by [DB_DataObject] extends=xxxx
     *
     * @var    string
     * @access private
     */
    var $_extends = 'DB_DataObject';

    /**
     * line to use for require('DB/DataObject.php');
     *
     * @var    string
     * @access private
     */
    var $_extendsFile = "DB/DataObject.php";

    /**
     * class being generated
     *
     * @var    string
     * @access private
     */
    var $_className;

    /**
     * The table class geneation part - single file.
     *
     * @access  private
     * @return  none
     */
    function _generateClassTable($input = '')
    {
        // title = expand me!
        $foot = "";
        $head = "<?php\n/**\n * Table Definition for {$this->table}\n";
        $head .= $this->derivedHookPageLevelDocBlock();
        $head .= " */\n";
        $head .= $this->derivedHookExtendsDocBlock();

        
        // requires - if you set extends_location = (blank) then no require line will be set
        // this can be used if you have an autoloader
        
        if (!empty($this->_extendsFile)) {
            $head .= "require_once '{$this->_extendsFile}';\n\n";
        }
        // add dummy class header in...
        // class 
        $head .= $this->derivedHookClassDocBlock();
        $head .= "class {$this->classname} extends {$this->_extends} \n{";

        $body =  "\n    ###START_AUTOCODE\n";
        $body .= "    /* the code below is auto generated do not remove the above tag */\n\n";
        // table

        $p = str_repeat(' ',max(2, (18 - strlen($this->table)))) ;
        
        $options = &PEAR::getStaticProperty('DB_DataObject','options');
        
        
        $var = (substr(phpversion(),0,1) > 4) ? 'public' : 'var';
        $var = !empty($options['generator_var_keyword']) ? $options['generator_var_keyword'] : $var;
        
        
        $body .= "    {$var} \$__table = '{$this->table}';  {$p}// table name\n";
    
        // if we are using the option database_{databasename} = dsn
        // then we should add var $_database = here
        // as database names may not always match.. 
        
        if (empty($GLOBALS['_DB_DATAOBJECT']['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }

         // Only include the $_database property if the omit_database_var is unset or false
        
        if (isset($options["database_{$this->_database}"]) && empty($GLOBALS['_DB_DATAOBJECT']['CONFIG']['generator_omit_database_var'])) {
            $p = str_repeat(' ',   max(2, (16 - strlen($this->_database))));
            $body .= "    {$var} \$_database = '{$this->_database}';  {$p}// database name (used with database_{*} config)\n";
        }
        
        
        if (!empty($options['generator_novars'])) {
            $var = '//'.$var;
        }
        
        $defs = $this->_definitions[$this->table];

        // show nice information!
        $connections = array();
        $sets = array();

        foreach($defs as $t) {
            if (!strlen(trim($t->name))) {
                continue;
            }
            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $t->name)) {
                echo "*****************************************************************\n".
                     "**               WARNING COLUMN NAME UNUSABLE                  **\n".
                     "** Found column '{$t->name}', of type  '{$t->type}'            **\n".
                     "** Since this column name can't be converted to a php variable **\n".
                     "** name, and the whole idea of mapping would result in a mess  **\n".
                     "** This column has been ignored...                             **\n".
                     "*****************************************************************\n";
                continue;
            }
            
            $pad = str_repeat(' ',max(2,  (30 - strlen($t->name))));

            $length = empty($t->len) ? '' : '('.$t->len.')';
            $flags = strlen($t->flags) ? (' '. trim($t->flags)) : '';
            $body .="    {$var} \${$t->name}; {$pad}// {$t->type}{$length}{$flags}\n";
            
            // can not do set as PEAR::DB table info doesnt support it.
            //if (substr($t->Type,0,3) == "set")
            //    $sets[$t->Field] = "array".substr($t->Type,3);
            $body .= $this->derivedHookVar($t,strlen($p));
        }
         
        $body .= $this->derivedHookPostVar($defs);

        // THIS IS TOTALLY BORKED old FC creation
        // IT WILL BE REMOVED!!!!! in DataObjects 1.6
        // grep -r __clone * to find all it's uses
        // and replace them with $x = clone($y);
        // due to the change in the PHP5 clone design.
        $static = 'static';
        if ( substr(phpversion(),0,1) < 5) {
            $body .= "\n";
            $body .= "    /* ZE2 compatibility trick*/\n";
            $body .= "    function __clone() { return \$this;}\n";
        }
        
        
        // depricated - in here for BC...
        if (!empty($options['static_get'])) {
            
            // simple creation tools ! (static stuff!)
            $body .= "\n";
            $body .= "    /* Static get */\n";
            $body .= "    $static  function staticGet(\$k,\$v=NULL) { " .
                    "return DB_DataObject::staticGet('{$this->classname}',\$k,\$v = null); }\n";
        }
        // generate getter and setter methods
        $body .= $this->_generateGetters($input);
        $body .= $this->_generateSetters($input);
        $body .= $this->_generateLinkMethods($input);
        /*
        theoretically there is scope here to introduce 'list' methods
        based up 'xxxx_up' column!!! for heiracitcal trees..
        */

        // set methods
        //foreach ($sets as $k=>$v) {
        //    $kk = strtoupper($k);
        //    $body .="    function getSets{$k}() { return {$v}; }\n";
        //}
        
        if (!empty($options['generator_no_ini'])) {
            $def = $this->_generateDefinitionsTable();  // simplify this!?
            $body .= $this->_generateTableFunction($def['table']);
            $body .= $this->_generateKeysFunction($def['keys']);
            $body .= $this->_generateSequenceKeyFunction($def);
            $body .= $this->_generateDefaultsFunction($this->table, $def['table']);
        }  else if (!empty($options['generator_add_defaults'])) {   
            // I dont really like doing it this way (adding another option)
            // but it helps on older projects.
            $def = $this->_generateDefinitionsTable();  // simplify this!?
            $body .= $this->_generateDefaultsFunction($this->table,$def['table']);
             
        }
        $body .= $this->derivedHookFunctions($input);

        $body .= "\n    /* the code above is auto generated do not remove the tag below */";
        $body .= "\n    ###END_AUTOCODE\n";


        // stubs..
        
        if (!empty($options['generator_add_validate_stubs'])) {
            foreach($defs as $t) {
                if (!strlen(trim($t->name))) {
                    continue;
                }
                $validate_fname = 'validate' . $this->getMethodNameFromColumnName($t->name);
                // dont re-add it..
                if (preg_match('/\s+function\s+' . $validate_fname . '\s*\(/i', $input)) {
                    continue;
                }
                $body .= "\n    function {$validate_fname}()\n    {\n        return false;\n    }\n";
            }
        }




        $foot .= "}\n";
        $full = $head . $body . $foot;

        if (!$input) {
            return $full;
        }
        if (!preg_match('/(\n|\r\n)\s*###START_AUTOCODE(\n|\r\n)/s',$input))  {
            return $full;
        }
        if (!preg_match('/(\n|\r\n)\s*###END_AUTOCODE(\n|\r\n)/s',$input)) {
            return $full;
        }


        /* this will only replace extends DB_DataObject by default,
            unless use set generator_class_rewrite to ANY or a name*/

        $class_rewrite = 'DB_DataObject';
        $options = &PEAR::getStaticProperty('DB_DataObject','options');
        if (empty($options['generator_class_rewrite']) || !($class_rewrite = $options['generator_class_rewrite'])) {
            $class_rewrite = 'DB_DataObject';
        }
        if ($class_rewrite == 'ANY') {
            $class_rewrite = '[a-z_]+';
        }

        $input = preg_replace(
            '/(\n|\r\n)class\s*[a-z0-9_]+\s*extends\s*' .$class_rewrite . '\s*(\n|\r\n)\{(\n|\r\n)/si',
            "\nclass {$this->classname} extends {$this->_extends} \n{\n",
            $input);

        $ret =  preg_replace(
            '/(\n|\r\n)\s*###START_AUTOCODE(\n|\r\n).*(\n|\r\n)\s*###END_AUTOCODE(\n|\r\n)/s',
            $body,$input);
        
        if (!strlen($ret)) {
            return PEAR::raiseError(
                "PREG_REPLACE failed to replace body, - you probably need to set these in your php.ini\n".
                "pcre.backtrack_limit=1000000\n".
                "pcre.recursion_limit=1000000\n"
                ,null, PEAR_ERROR_DIE);
       }
        
        return $ret;
    }

    
    /**

    /**
    * getProxyFull - create a class definition on the fly and instantate it..
    *
    * similar to generated files - but also evals the class definitoin code.
    * 
    * 
    * @param   string database name
    * @param   string  table   name of table to create proxy for.
    * 
    *
    * @return   object    Instance of class. or PEAR Error
    * @access   public
    */
    function getProxyFull($database,$table) 
    {
        
        if ($err = $this->fillTableSchema($database,$table)) {
            return $err;
        }
        
        
        $options = &PEAR::getStaticProperty('DB_DataObject','options');
        $class_prefix  = empty($options['class_prefix']) ? '' : $options['class_prefix'];
        
        $this->_extends = empty($options['extends']) ? $this->_extends : $options['extends'];
        $this->_extendsFile = !isset($options['extends_location']) ? $this->_extendsFile : $options['extends_location'];
 
        $classname = $this->classname = $this->getClassNameFromTableName($this->table);
        
        $out = $this->_generateClassTable();
        //echo $out;
        eval('?>'.$out);
        return new $classname;
        
    }
    
     /**
    * fillTableSchema - set the database schema on the fly
    *
    * 
    * 
    * @param   string database name
    * @param   string  table   name of table to create schema info for
    *
    * @return   none | PEAR::error()
    * @access   public
    */
    function fillTableSchema($database,$table) 
    {
         
         // a little bit of sanity testing.
        if ((false !== strpos($database,"'")) || (false !== strpos($database,";"))) {   
            return $this->raiseError("Error: Database name contains a quote or semi-colon", null, PDO_DataObject::ERROR_DIE);
        }
        
        $this->_database  = $database; 
        $options = PDO_DataObject::config();;
        $pdo = $this->PDO();
      
        $table = trim($table);
        
        // a little bit of sanity testing.
        if ((false !== strpos($table,"'")) || (false !== strpos($table,";"))) {   
            return $this->raiseError("Error: Table contains a quote or semi-colon", null, PDO_DataObject::ERROR_DIE);
        }
        
        
        //WHY HERE????
        //try {
        //    $this->tables = $this->_introspection()->getListOf('schema.tables');
        //}  catch(Exception $e) {
        //       
       // }
        
        // quote table not needed as the intropection classes handle it..
        
        $defs = $this->introspection()->tableInfo($table);
         
        if (is_a($defs,'PEAR_Error')) {
            return $defs;
        }
        
        $this->debug("getting def for $database/$table",'fillTable',3);
        $this->debug(print_r($defs,true),'defs',3);
        
        // cast all definitions to objects - as we deal with that better.
        
            
        foreach($defs as $def) {
            if (is_array($def)) {
                $this->_definitions[$table][] = (object) $def;
            }
        }

        $this->table = $table;
        
        $ret = $this->_generateDefinitionsTable();
        
        $add = array();
        $add[$table] = $ret['table'];
        $add[$table.'__keys'] = $ret['keys'];
        
       
        $this->databaseStructure($database, $add);
        
        return false;
        
    }
    
     
    /**
    * Generate link setter/getter methods for class definition
    *
    * @param    string  Existing class contents
    * @return   string
    * @access   public
    */
    function _generateLinkMethods($input) 
    {

        $options = &PEAR::getStaticProperty('DB_DataObject','options');
        $setters = '';

        // only generate if option is set to true
        
        // generate_link_methods true::
        
        
        if  (empty($options['generate_link_methods'])) {
            //echo "skip lm? - not set";
            return '';
        }
        
        if (empty($this->_fkeys)) {
            // echo "skip lm? - fkyes empty";
            return '';
        }
        if (empty($this->_fkeys[$this->table])) {
            //echo "skip lm? - no fkeys for {$this->table}";
            return '';
        }
            
        // remove auto-generated code from input to be able to check if the method exists outside of the auto-code
        $input = preg_replace('/(\n|\r\n)\s*###START_AUTOCODE(\n|\r\n).*(\n|\r\n)\s*###END_AUTOCODE(\n|\r\n)/s', '', $input);

        $setters .= "\n";
        $defs     = $this->_fkeys[$this->table];
         
        
        // $fk[$this->table][$tref[1]] = $tref[2] . ":" . $tref[3];

        // loop through properties and create setter methods
        foreach ($defs as $k => $info) {

            // build mehtod name
            $methodName =  is_callable($options['generate_link_methods']) ?
                    $options['generate_link_methods']($k) : $k;

            if (!strlen(trim($k)) || preg_match("/function[\s]+[&]?$methodName\(/i", $input)) {
                continue;
            }

            $setters .= "   /**\n";
            $setters .= "    * Getter / Setter for \${$k}\n";
            $setters .= "    *\n";
            $setters .= "    * @param    mixed   (optional) value to assign\n";
            $setters .= "    * @access   public\n";
            
            $setters .= "    */\n";
            $setters .= (substr(phpversion(),0,1) > 4) ? '    public '
                                                       : '    ';
            $setters .= "function $methodName() {\n";
            $setters .= "        return \$this->link('$k', func_get_args());\n";
            $setters .= "    }\n\n";
        }
         
        return $setters;
    }
 
    /**
    * Generate table Function - used when generator_no_ini is set.
    *
    * @param    array  table array.
    * @return   string
    * @access   public
    */
    function _generateTableFunction($def) 
    {
        $defines = explode(',','INT,STR,DATE,TIME,BOOL,TXT,BLOB,NOTNULL,MYSQLTIMESTAMP');
    
        $ret = "\n" .
               "    function table()\n" .
               "    {\n" .
               "         return array(\n";
        
        foreach($def as $k=>$v) {
            $str = '0';
            foreach($defines as $dn) {
                if ($v & constant('DB_DATAOBJECT_' . $dn)) {
                    $str .= ' + DB_DATAOBJECT_' . $dn;
                }
            }
            if (strlen($str) > 1) {
                $str = substr($str,3); // strip the 0 +
            }
            // hopefully addslashes is good enough here!!!
            $ret .= '             \''.addslashes($k).'\' => ' . $str . ",\n";
        }
        return $ret . "         );\n" .
                      "    }\n";
            
    
    
    }
    /**
    * Generate keys Function - used generator_no_ini is set.
    *
    * @param    array  keys array.
    * @return   string
    * @access   public
    */
    function _generateKeysFunction($def) 
    {
         
        $ret = "\n" .
               "    function keys()\n" .
               "    {\n" .
               "         return array(";
            
        foreach($def as $k=>$type) {
            // hopefully addslashes is good enough here!!!
            $ret .= '\''.addslashes($k).'\', ';
        }
        $ret = preg_replace('#, $#', '', $ret);
        return $ret . ");\n" .
                      "    }\n";
            
    
    
    }
  
     
    
    
     
    
    
}
