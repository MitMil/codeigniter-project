<?php
/* 
 * This is the script to update the BBWeb.
 * Run it from bbweb root from command line:
 *  # php maintenance/update.php
 */
    // set default timezone
    date_default_timezone_set('Europe/Rome');

    // Define path of update system
    define('DEFAULT_BBWEB_VERSION', 100);
    define('UPDATE_APPLICATION_PATH', realpath(dirname(__FILE__)) . '/');
    define('UPDATES_DIR_PATH', UPDATE_APPLICATION_PATH . "updates/");
    define('VERSION_FILE_PATH', UPDATE_APPLICATION_PATH . "../version.php");
    define('DB_CONFIG_PATH', UPDATE_APPLICATION_PATH . '/../../Protected/app.config');
    define('LOG_FILE', UPDATE_APPLICATION_PATH . '/../../Protected/update.log');
    
    // open DB connection
    $db = getDbConnection();

    // check for current version
    $sth_version = $db->prepare("SELECT default_value FROM bb_settings WHERE var_code='BBWEB_VERSION'");
    $sth_version->execute();
    $vObj = $sth_version->fetch(PDO::FETCH_OBJ);
    if($vObj === false) {
        // NOT EXISTS: set the default BBWEB_VERSION
        $db->query("INSERT INTO bb_settings SET var_code='BBWEB_VERSION', env='BBWEB', type_values='INT', default_value='".DEFAULT_BBWEB_VERSION."'");
        $version = DEFAULT_BBWEB_VERSION;
    } else {
        $version = $vObj->default_value;
    }
    
    logThis(date("d/m/Y H:i:s") . " - Start UPDATE procedure - Version: $version ");
    
    // loop for new versions to update
    while( true ) 
    {
        // increment new version
        $version++;
        $new_version_file = UPDATES_DIR_PATH . $version . ".php";
        if( file_exists($new_version_file) ) {
            // include the new version file
            include_once($new_version_file);

            // log update messages...
            logThis( "Update to version " . $BBWEB_NEW_VERSION );
            
            // run update queries
            if(count($queries) > 0) {
                executeQueries($db, $version, $queries);
            }
            
            // update the BBWEB_VERSION value to the new version updated
            $db->query("UPDATE bb_settings SET default_value='$version' WHERE var_code='BBWEB_VERSION'");
            
        } else {
            // stop the loop
            break;
        }
    }
    
    logThis("Procedure completed!");


/******************************************************
 * FUNCTIONS
 */
    
    /**
     * Open a DB Connection
     * @return \PDO
     */
    function getDbConnection()
    {
        // GET DB config file
        if (file_exists(DB_CONFIG_PATH)) {
            $xml = simplexml_load_file(DB_CONFIG_PATH);
            foreach($xml->appSettings->add AS $element)
            {
                // DEFINE every single value
                define("_DB_" . $element->attributes()->key, $element->attributes()->value);
            }
            
            $dsn = 'mysql:unix_socket='._DB_unix_socket.';dbname='._DB_Database;
            $db = new PDO($dsn, _DB_UserID, _DB_Password, array(PDO::ATTR_PERSISTENT => true));
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return $db;
            
        } else {
            die("Failed to open '".DB_CONFIG_PATH."' file");
        }
    }
    
    /**
     * Executes queries from array
     * @param \PDO $db
     * @param array $queries
     * @return void
     */
    function executeQueries($db, $version, $queries)
    {
        foreach($queries AS $key => $query)
        {
            logThis( "[$key] QUERY: " . $query );
            $res = $db->query($query);
            if($res === false) {
                logThis("RESULT: ERROR on UPDATE[$version] QUERY[$key] - exit from update procedure!");
                print_r($db->errorInfo());
                die;
            } else {
                logThis("RESULT: OK!");
            }
        }
    }
    
    /**
     * Log a text into the LOG FILE
     * @param string $txt
     */
    function logThis($txt) 
    {
        $txt = $txt . "\n";
        $fr = fopen( LOG_FILE, 'a' );
        echo $txt;
        fwrite($fr, $txt);
        fclose($fr);
    }