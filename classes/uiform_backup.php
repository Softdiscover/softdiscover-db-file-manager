<?php

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Flmbkp_Backup')) {
    return;
}

class Flmbkp_Backup
{

    private $tables = array();
    private $suffix = 'd-M-Y_H-i-s';

    /**
     * Constructor
     *
     * @mvc Controller
     */
    public function __construct($filename, $backup_directory)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->backup_dir = $backup_directory;
        $this->backup_slug = $filename;
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->passwd = DB_PASSWORD;
        $this->dbName = DB_NAME;
        $this->charset = DB_CHARSET;
        $this->conn = $this->getConnectionObj();
    }

    protected function getConnectionObj()
    {
        try {
            $conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName);
            if (mysqli_connect_errno()) {
                throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
                die();
            }
            if (!mysqli_set_charset($conn, $this->charset)) {
                mysqli_query($conn, 'SET NAMES ' . $this->charset);
            }

           
             // Disable foreign key checks
            mysqli_query($conn, 'SET foreign_key_checks = 0');
        } catch (Exception $e) {
            var_dump($e->getMessage());
            die();
        }

        return $conn;
    }

    public function uploadBackupFile()
    {
        $target_dir = FLMBKP_DIR . '/backups/';
        $target_file = $target_dir . basename($_FILES["uifm_bkp_fileupload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["uifm_bkp_fileupload"]["size"] > 5048576) {
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "sql") {
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk === 0) {
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["uifm_bkp_fileupload"]["tmp_name"], $target_file)) {
            } else {
            }
        }
    }

    /*
     * Restore backup
     */

    public function restoreBackup(&$log)
    {
        try {
            $sql = '';
            $multiLine_comment = false;

            $backup_dir = $this->backup_dir;
            $backup_slug = $this->backup_slug;

            /**
             * unzip file
             */
            $tmp_res = Flmbkp_Form_Helper::unzipFiles($backup_dir . '/' . $backup_slug . '_database.zip', $backup_dir);
            if (!$tmp_res) {
                throw new Exception("ERROR: couldn't unzip backup file " . $backup_dir . '/' . $backup_slug);
            }

            //start importing sql file
            if (file_exists($backup_dir . '/' . $backup_slug . '_database.sql')) {
                 $handle = fopen($backup_dir . '/' . $backup_slug . '_database.sql', "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $line = ltrim(rtrim($line));
                        // avoid blank lines
                        if (strlen($line) > 1) {
                            $lineIsComment = false;
                            if (preg_match('/^\/\*/', $line)) {
                                $multiLine_comment = true;
                                $lineIsComment = true;
                            }
                            if ($multiLine_comment || preg_match('/^\/\//', $line)) {
                                $lineIsComment = true;
                            }
                            if (!$lineIsComment) {
                                $sql .= $line;
                                if (preg_match('/;$/', $line)) {
                                    if (mysqli_query($this->conn, $sql)) {
                                        if (preg_match('/^CREATE TABLE `([^`]+)`/i', $sql, $tableName)) {
                                            $log[] = "Table created: `" . $tableName[1] . "`";
                                        }
                                        $sql = '';
                                    } else {
                                        throw new Exception("Error on SQL execution: " . mysqli_error($this->conn).' - '.$sql);
                                    }
                                }
                            } else if (preg_match('/\*\/$/', $line)) {
                                $multiLine_comment = false;
                            }
                        }
                    }
                    fclose($handle);
                } else {
                    throw new Exception("Error on opening backup file " . $backup_dir . '/' . $backup_slug);
                }
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
 
        @unlink($backup_dir . '/' . $backup_slug . '_database.sql');
        

        return true;
    }

    public function restoreBackupAlt($file)
    {
        try {
            /* Begin restore */

            $dir = FLMBKP_DIR . '/backups/';
            $database_file = $dir . $file;


            $database_name = DB_NAME;
            $database_user = DB_USER;
            $datadase_password = DB_PASSWORD;
            $database_host = DB_HOST;

            ini_set("max_execution_time", "5000");
            ini_set("max_input_time", "5000");
            ini_set('memory_limit', '1000M');
            set_time_limit(0);


            if ((trim((string) $database_name) != '') && (trim((string) $database_user) != '') && (trim((string) $database_host) != '') && ($conn = @mysql_connect((string) $database_host, (string) $database_user, (string) $datadase_password))) {
                /* BEGIN: Select the Database */
                if (!mysql_select_db((string) $database_name, $conn)) {
                    $sql = "CREATE DATABASE IF NOT EXISTS `" . (string) $database_name . "`";
                    mysql_query($sql, $conn);
                    mysql_select_db((string) $database_name, $conn);
                }
                /* END: Select the Database */

                /* BEGIN: Remove All Tables from the Database */

                require_once(FLMBKP_DIR . '/classes/uiform-installdb.php');
                $installdb = new Flmbkp_InstallDB();
                $dbTables = array();


                if (count($dbTables) > 0) {
                    foreach ($dbTables as $table_name) {
                        mysql_query("DROP TABLE `" . (string) $database_name . "`.{$table_name}", $conn);
                    }
                }


                /* END: Remove All Tables from the Database */

                /* BEGIN: Restore Database Content */
                if (isset($database_file)) {
                    $sql_file = file_get_contents($database_file, true);

                    $sql_file = strtr($sql_file, array(
                        "\r\n" => "\n",
                        "\r" => "\n",
                    ));
                    $sql_queries = explode(";\n", $sql_file);

                    for ($i = 0; $i < count($sql_queries); $i++) {
                        @mysql_query($sql_queries[$i], $conn);
                    }
                }
            }

            /* END: Restore Database Content */

            /* End Begin restore */
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }

    public function makeDbBackup($name = '')
    {
        require_once(FLMBKP_DIR . '/classes/uiform-installdb.php');
        $installdb = new Flmbkp_InstallDB();
        $dbTables = array();
        $dbTables[] = $installdb->form;
        $dbTables[] = $installdb->form_history;
        $dbTables[] = $installdb->form_fields_type;
        $dbTables[] = $installdb->form_fields;
        $dbTables[] = $installdb->settings;
        $this->tables = $dbTables;


        $dump = '';
        $database = DB_NAME;
        $server = DB_HOST;
        $dump .= '-- --------------------------------------------------------------------------------' . NL;
        $dump .= '-- ' . NL;
        $dump .= '-- @version: ' . $database . '.sql ' . date('M j, Y') . ' ' . date('H:i') . ' Softdiscover' . NL;
        $dump .= '-- @package Uiform - Wordpress Form Builder' . NL;
        $dump .= '-- @author softdiscover.com.' . NL;
        $dump .= '-- @copyright 2015' . NL;
        $dump .= '-- ' . NL;
        $dump .= '-- --------------------------------------------------------------------------------' . NL;
        $dump .= '-- Host: ' . $server . NL;
        $dump .= '-- Database: ' . $database . NL;
        $dump .= '-- Time: ' . date('M j, Y') . '-' . date('H:i') . NL;
        $dump .= '-- MySQL version: ' . Flmbkp_Form_Helper::mysql_version() . NL;
        $dump .= '-- PHP version: ' . phpversion() . NL;
        $dump .= '-- --------------------------------------------------------------------------------' . NL . NL;

        $dump .= 'DROP TABLE IF EXISTS `' . $installdb->form_history . '`;' . NL;
        $dump .= 'DROP TABLE IF EXISTS `' . $installdb->form_fields . '`;' . NL;
        $dump .= 'DROP TABLE IF EXISTS `' . $installdb->form_fields_type . '`;' . NL;
        $dump .= 'DROP TABLE IF EXISTS `' . $installdb->form . '`;' . NL;
        $dump .= 'DROP TABLE IF EXISTS `' . $installdb->settings . '`;' . NL;

        $database = DB_NAME;
        if (!empty($database)) {
            $dump .= '#' . NL;
            $dump .= '# Database: `' . $database . '`' . NL;
        }
        $dump .= '#' . NL . NL . NL;
        $tables = $this->getTables();
        if (!empty($tables)) {
            foreach ($this->tables as $key => $table) {
                if (intval($key) === 0) {
                    $table_dump = $this->dumpTable($table, true);
                } else {
                    $table_dump = $this->dumpTable($table);
                }

                if (!($table_dump)) {
                    return false;
                }
                $dump .= $table_dump;
            }
        }


        $fname = FLMBKP_DIR . '/backups/';
        $fname .= (!empty($name)) ? $name : date($this->suffix);
        $fname .= '.sql';
        if (!($f = fopen($fname, 'w'))) {
            return false;
        }
        fwrite($f, $dump);
        fclose($f);
    }

    public function getTables()
    {
        $value = array();
        if (!($result = $this->wpdb->get_results("SHOW TABLES"))) {
            return false;
        }
        foreach ($result as $mytable) {
            foreach ($mytable as $t) {
                if (in_array($t, $this->tables)) {
                    $value[] = $t;
                }
            }
        }
        if (!sizeof($value)) {
            return false;
        }

        return $value;
    }

    public function dumpTable($table, $flag = false)
    {

        // $dump = '';
        $this->wpdb->query('LOCK TABLES ' . $table . ' WRITE');

        // $tables = $this->wpdb->get_col('SHOW TABLES');
        $output = '';
        //foreach($tables as $table) {
        $result = $this->wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);
        if ($flag === true) {
            //verifying the first table has content
            $row = isset($result[0]) ? $result[0] : '';
            if (empty($row[0])) {
                return false;
            }
        }
        $output .= '-- --------------------------------------------------' . NL;
        $output .= '# -- Table structure for table `' . $table . '`' . NL;
        $output .= '-- --------------------------------------------------' . NL;
        $output .= 'DROP TABLE IF EXISTS `' . $table . '`;' . NL;
        $row2 = $this->wpdb->get_row('SHOW CREATE TABLE ' . $table, ARRAY_N);
        $output .= "\n\n" . $row2[1] . ";\n\n";
        for ($i = 0; $i < count($result); $i++) {
            $row = $result[$i];
            $output .= 'INSERT INTO ' . $table . ' VALUES(';
            for ($j = 0; $j < count($result[0]); $j++) {
                $row[$j] = $this->wpdb->_real_escape($row[$j]);
                $output .= (isset($row[$j])) ? '"' . $row[$j] . '"' : '""';
                if ($j < (count($result[0]) - 1)) {
                    $output .= ',';
                }
            }
            $output .= ");\n";
        }
        $output .= "\n";
        //}

        $this->wpdb->query('UNLOCK TABLES');
        return $output;
    }

    public function insert($table)
    {
        $output = '';
        if (!$query = $this->wpdb->get_results("SELECT * FROM `" . $table . "`")) {
            return false;
        }
        foreach ($query as $result) {
            $fields = '';

            foreach (array_keys((array) $result) as $value) {
                $fields .= '`' . $value . '`, ';
            }
            $values = '';


            foreach (array_values((array) $result) as $value) {
                $values .= '\'' . $value . '\', ';
            }

            $output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
        }
        return $output;
    }
}
