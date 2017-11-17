<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service extends CI_Controller
{
    // utilities directory path to execute shell scripts
    private $utilities_dir = APPLICATION_PATH . '/../utilities/';
    // write directory path
    private $write_dir = APPLICATION_PATH . '/../write-dir/';
    //logs dir path
    private $logs_dir = APPLICATION_PATH . '/../write-dir/logs/';
    // process id file
    private $pid_file = APPLICATION_PATH . '/../write-dir/logs/logfile.pid.php';

    // File for storing running process information.
    private $running_process_file = APPLICATION_PATH . '/../write-dir/bbweb/running_process.php';

    public function __construct()
    {
        parent::__construct();

        $this->fetch_running_process();

        $this->load->model('mdl_service', '', true);
        $this->load->helper('file');
    }

    public function index()
    {
        $data = array();
        redirect('service/backuprestoredb');
    }

    /**
     * backuprestoredb function for backup and restore database.
     *
     * @var array
     */
    public function backuprestoredb()
    {
        $data = array();
        $command = '';
        $get_method = $this->router->method;
        //check background process status if it's not backup process
        if (RUNNING_PROCESS) {
            $running_process = unserialize(RUNNING_PROCESS);
            if ($running_process['procedure'] != $get_method) {
                $this->check_running_process($get_method);
            }
        }
        if (isset($_GET['last_backup_file']) && $_GET['last_backup_file']) {
            $filename = $_GET['last_backup_file'];
            $file = $this->write_dir.'Backup/'.$filename;
            if (file_exists($file)) {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                // read the file from disk
                readfile($file);
                die;
            }
        }
        // Handle POST Request as per action
        if (isset($_POST['action']) && $_POST['action']) {

            $action = $_POST['action'];
            //check and create dir for backup log file
            $this->check_dir_exists($this->logs_dir);

            if ($action == 'backup') {
                $backuplogs_path = $this->logs_dir.'backupdb';
                $this->check_dir_exists($backuplogs_path);
                //generate logfile for read process
                $log_file = $backuplogs_path.'/backupdb'.'_'.date('Y_m_d_H_i_s').'.log';
                $command = $this->utilities_dir."backup/db_backup.sh --serial_number '".get_ifc_serialno()."'";

            } elseif ($action == 'restore') {
                $restorelogs_path = $this->logs_dir.'restoredb';
                $this->check_dir_exists($restorelogs_path);
                //generate logfile for read process
                $log_file = $restorelogs_path.'/restoredb'.'_'.date('Y_m_d_H_i_s').'.log';

                if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name']) {
                    $info    = pathinfo($_FILES['fileToUpload']['name']);
                    $ext     = $info['extension']; // get the extension of the file
                    $newname = 'restoredb.'.$ext;
                    $db_path = $this->write_dir.$newname;
                    move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $db_path);
                }
                $command = $this->utilities_dir."restore.sh --db_path '".$db_path."'";
            }
            
            $command .= " --dbname '"._DB_Database."'"." --dbuser '"._DB_UserID."'"." --dbpass '"._DB_Password."' > ".$log_file." > ".$log_file." 2>&1 & echo $! > ".$this->pid_file;

            //execute command
            exec($command, $command_output, $command_return);
            // Return will return non-zero upon an error
            if ($command_return == 0) {
                $data['status'] = 'success';
            } else {
                $data['status']  = 'failed';
            }
            //read process id from pid file
            $pid = $this->read_pid($this->pid_file);

            //update running process data in database
            $running_process = serialize(array('pid'=>$pid, 'log_file'=>$log_file,'status'=>$data['status'],'action'=>$action, 'procedure'=> $get_method));

            $this->update_running_process($running_process);

            $data['log_file'] = $log_file;
            $data['pid'] = $pid;
            $data['action'] = $action;
        }

        if (RUNNING_PROCESS) {
            $data = $this->assign_running_process_value();
        }

        load_views('service/backuprestoredb', $data);
    }

    /**
     * reset_procedures function for reset ENVs values of bb_settings.
     *
     * @var array
     */
    public function reset_procedures()
    {
        $data = array();
        $get_method = $this->router->method;
        //check background process status if it's not backup process
        if (RUNNING_PROCESS) {
            $running_process = unserialize(RUNNING_PROCESS);
            if ($running_process['procedure'] != $get_method) {
                $this->check_running_process($get_method);
            }
        }
        // Handle POST Request as per action
        if (isset($_POST['action']) && $_POST['action']) {
            $action = $_POST['action'];
            //check and create dir for backup log file
            $this->check_dir_exists($this->logs_dir);
            $reset_procedures_path = $this->logs_dir.'reset_procedures';
            $this->check_dir_exists($reset_procedures_path);
            //generate logfile for read process
            $log_file = $reset_procedures_path.'/'.$action.'_'.date('Y_m_d_H_i_s').'.log';
            $command = $this->utilities_dir . "resetSettingsdata.sh --action '".$action."'";
            if ($action == 'HardReset') {
                $command .= " --serial_number '".get_ifc_serialno()."' --server_url '".BBWEB_IFC_URL."'";
            }
            $command .= " > ".$log_file." 2>&1 & echo $! > ".$this->pid_file;

            //execute command
            exec($command, $command_output, $command_return);
            // Return will return non-zero upon an error
            if (!$command_return) {
                $data['status'] = 'success';
                if ($action == 'HardReset') {
                    $ip_data        = $this->mdl_service->GetDefaultValue();
                    if ($ip_data) {
                        $SYSTEM_IP      = $ip_data['SYSTEM_IP'];
                        $SYSTEM_NETMASK = $ip_data['SYSTEM_NETMASK'];
                        $SYSTEM_GATEWAY = $ip_data['SYSTEM_GATEWAY'];
                        /**
                         * call configNetwork.sh to save ip, netmask and gateway
                         */
                        $cmd = "sudo ".APPLICATION_PATH . "/../utilities/openvpn/configNetwork.sh"
                                . " --system_ip '".$SYSTEM_IP."'"
                                . " --system_netmask '".$SYSTEM_NETMASK."'"
                                . " --system_gateway '".$SYSTEM_GATEWAY."'";
                        exec($cmd, $output_status, $cmd_return);
                        if ($cmd_return != 0) {
                            $data['msg_class'] = "alert-danger";
                            $data['msg'] = 'Error on run configNetwork.sh';
                        } else {
                            $json_data = getJSON($output_status);
                            $return['json_response'] = serialize($output_status);
                            if ($json_data['error']!=0) {
                                $data['msg_class'] = "alert-danger";
                                $data['msg'] = $json_data["message"];
                            } else {
                                $msg = $json_data["message"];
                                redirect(base_url('settings/reboot?autostart=1&msg='.$msg.'&result=success'));
                                exit;
                            }
                        }
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg'] = "Could Not Found Default Values";
                    }
                }
            } else {
                $data['status'] = 'failed';
            }
            //read process id from pid file
            $pid = $this->read_pid($this->pid_file);

            //update running process data in database
            $running_process = serialize(array('pid'=>$pid, 'log_file'=>$log_file, 'status'=>$data['status'], 'procedure'=> $get_method));
            $this->update_running_process($running_process);

            $data['log_file'] = $log_file;
            $data['pid'] = $pid;
        }

        if (RUNNING_PROCESS) {
            $data = $this->assign_running_process_value();
        }

        load_views('service/reset_procedures', $data);
    }

    /**
     * devices function for Reload/Stop/Start services on iFC.
     *
     * @var array
     */
    public function devices()
    {
        $data = array();
        $get_method = $this->router->method;
        //check background process status if it's not backup process
        if (RUNNING_PROCESS) {
            $running_process = unserialize(RUNNING_PROCESS);
            if ($running_process['procedure'] != $get_method) {
                $this->check_running_process($get_method);
            }
        }
        // Handle POST Request as per action
        if ((isset($_POST['servicename']) && $_POST['servicename']) && (isset($_POST['serviceaction']) && $_POST['serviceaction'])) {

            $servicename   = $_POST['servicename'];
            $serviceaction = $_POST['serviceaction'];
            //check and create dir for backup log file
            $this->check_dir_exists($this->logs_dir);
            $reset_procedures_path = $this->logs_dir.'devices';
            $this->check_dir_exists($reset_procedures_path);
            //generate logfile for read process
            $log_file              = $reset_procedures_path.'/devices'.'_'.date('Y_m_d_H_i_s').'.log';

            $command       = $this->utilities_dir.'service.sh'.' '.$servicename.' '.$serviceaction." > ".$log_file." 2>&1 & echo $! > ".$this->pid_file;
            //execute command
            exec($command, $command_output, $command_return);
            // Return will return non-zero upon an error
            if (!$command_return) {
                $data['status'] = 'success';
            } else {
                $data['status'] = 'failed';
            }
            //read process id from pid file
            $pid = $this->read_pid($this->pid_file);

            //update running process data in database
            $running_process = serialize(array('pid'=>$pid, 'log_file'=>$log_file,'status'=>$data['status'], 'procedure'=> $get_method));
            $this->update_running_process($running_process);

            $data['log_file'] = $log_file;
            $data['pid'] = $pid;
        }

        if (RUNNING_PROCESS) {
            $data = $this->assign_running_process_value();
        }

        load_views('service/devices', $data);
    }

    /**
     * install function for install iFC server.
     *
     * @var array
    public function install()
    {
        $data = array();
        $get_method = $this->router->method;
        //check background process status if it's not update process
        // if (RUNNING_PROCESS) {
        //     $running_process = unserialize(RUNNING_PROCESS);
        //     if ($running_process['procedure'] != $get_method) {
        //         $this->check_running_process();
        //     }
        // }
        // Handle POST Request as per action
        if (isset($_POST['serial_number']) && $_POST['serial_number']) {

            $server_url    = BBWEB_IFC_URL; //'ifc-server.server.bb03.primeapps.in';
            $serial_number = $_POST['serial_number'];
            $default_value = 'yes';
            //check and create dir for backup log file
            $this->check_dir_exists($this->logs_dir);
            $install_path = $this->logs_dir.'install';
            $this->check_dir_exists($install_path);
            //generate logfile for read process
            $log_file = $install_path.'/install'.'_'.date('Y_m_d_H_i_s').'.log';

            $command = $this->utilities_dir.'installprocedures.sh'." --dbname '"._DB_Database."'"." --dbuser '"._DB_UserID."'"." --dbpass '"._DB_Password."'"." --serial_number '".$serial_number."'"." --server_url '".$server_url."'"." --default_value '".$default_value."' > ".$log_file." 2>&1 & echo $! > ".$this->pid_file;
            //execute command
            exec($command, $command_output, $command_return);
            // Return will return non-zero upon an error
            if (!$command_return) {
                $data['status'] = 'success';
            } else {
                $data['status'] = 'failed';
            }
            //read process id from pid file
            $pid = $this->read_pid($this->pid_file);

            //update running process data in database
            $running_process = serialize(array('pid'=>$pid, 'log_file'=>$log_file,'status'=>$data['status'], 'procedure'=> $get_method));
            $this->update_running_process($running_process);

            $data['log_file'] = $log_file;
            $data['pid'] = $pid;
        }

        // if (RUNNING_PROCESS) {
        //     $data = $this->assign_running_process_value();
        // }
        load_views('service/install', $data, array('top_menu'=>false));
    }
     */

    /**
     * updatesoftware function for Update iFC Applications Software.
     *
     * @var array
     */
    public function updatesoftware()
    {
        $data = array();
        $get_method = $this->router->method;
        $log_file = '';
        //check background process status if it's not update process
        if (RUNNING_PROCESS) {
            $running_process = unserialize(RUNNING_PROCESS);
            if ($running_process['procedure'] != $get_method) {
                $this->check_running_process($get_method);
            }
        }
        // Handle POST Request as per action
        if (isset($_POST['application']) && $_POST['application'] || isset($_POST['application_version']) && $_POST['application_version']) {

            $application = $version = '';
            if (isset($_POST['application_version']) && $_POST['application_version']) {
                $version = $_POST['application_version'];
            }
            if (isset($_POST['application']) && $_POST['application']) {
                $application = $_POST['application'];
                $version = $_POST[$application];
            }
            //generate logfile for read process
            $this->check_dir_exists($this->logs_dir);
            $application_path = $this->logs_dir.'update/';
            $this->check_dir_exists($application_path);
            $log_file         = $application_path.'update_'.date('Y_m_d_H_i_s').'.log';

            if (!write_file($log_file, '')) {
                $data['status'] = 'failed';
            } else {
                //assign permission to logfile
                chmod($log_file, 0777);

                //run command for update application
                if (isset($_POST['application_version']) && $_POST['application_version']) {
                    $command = $this->utilities_dir.'update.sh'." --all 1 "." --version ".$version." >> ".$log_file.'& echo $! >'.$this->pid_file;
                }
                if (isset($_POST['application']) && $_POST['application']) {
                    $command = $this->utilities_dir.'update.sh'." --project '".$application."' --version '".$version."' >> ".$log_file.'& echo $! >'.$this->pid_file;
                }
                //echo $command;die;
                exec($command, $command_output, $command_return);

                // Return will return non-zero upon an error
                if (!$command_return) {
                    $data['status'] = 'success';
                } else {
                    $data['status'] = 'failed';
                }

                //read process id from pid file
                $pid = $this->read_pid($this->pid_file);

                //update running process data in database
                $running_process = serialize(array('pid'=>$pid,'log_file'=>$log_file,'application'=>$application,'procedure'=>$get_method));
                $this->update_running_process($running_process);

                $data['log_file']    = $log_file;
                $data['pid']         = $pid;
                $data['application'] = $application;
            }
        }

        if (RUNNING_PROCESS) {
            $data = $this->assign_running_process_value();
        }

        load_views('service/updatesoftware', $data);
    }

    /**
     * Get data from running process and read line by line log file
     * @return json
     */
    public function readlog()
    {
        $data_readlog = array();
        $pid          = $_POST['pid'];
        $myFile       = $_POST['log_file'];

        $checkpid               = $this->isRunning($pid);
        $data_readlog['result'] = '';
        $linecount              = 0;

        $file = file($myFile);
        for ($i = 0; $i < count($file); ++$i) {
            $data_readlog['result'] .= nl2br($file[$i]);
        }

        $linecount = count($file);

        if ($checkpid) {
            $data_readlog['count']  = $linecount;
            $data_readlog['status'] = 1;
        } else {
            $data_readlog['count']  = $linecount;
            $data_readlog['status'] = 0;
        }
        echo json_encode($data_readlog);
        die;
    }

    /**
     * Check linux process running
     * @param  [int]  $pid [process id]
     * @return boolean
     */
    public function isRunning($pid)
    {
        $result = shell_exec(sprintf('ps %d', $pid));
        if (count(preg_split("/\n/", $result)) > 2) {
            return true;
        }
        return false;
    }

    /**
     * After complete execution kill process and set running process status null
     * @return json
     */
    public function finish_process()
    {
        $pid = $_POST['pid'];

        /**
         * @todo Check if KILL is absolultely requied.
         */
        exec("kill -9 $pid");

        // unlink PID file as process is finished.
        unlink($this->pid_file);
        
        file_put_contents($this->running_process_file, null);

        $result['status'] = 'success';
        echo json_encode($result);
        die;
    }

    /**
     * check_running_process is check background process is running
     * @return boolean
     */
    public function check_running_process($redirect_to)
    {
        if (RUNNING_PROCESS) {
            $running_process = unserialize(RUNNING_PROCESS);
            $procedure       = $running_process['procedure'];
            $serial_number = get_ifc_serialno();
            if ($serial_number) {
                file_put_contents($this->running_process_file, null);
                redirect('service/'.$redirect_to);
            } else {
                redirect('service/'.$procedure);
            }
        }
        return true;
    }

    /**
     * Fetch if any process is running and define available information.
     */
    private function fetch_running_process()
    {
        if (!file_exists($this->running_process_file)) {
            $fp = fopen($this->running_process_file, "w");
            fclose($fp);
        }
        $running_process = file_get_contents($this->running_process_file);
        if (!$running_process) {
            file_put_contents($this->running_process_file, null);
        }
        if (!defined("RUNNING_PROCESS")) {
            define("RUNNING_PROCESS", $running_process);
        }
        return true;
    }

    /**
     * check_dir_exists is create dir if not exists
     * @return boolean
     */
    private function check_dir_exists($path)
    {
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0777);
        }
        return true;
    }



    /**
     * read_pid is read pid from pid file
     * @return Integer
     */
    private function read_pid($pid_file)
    {
        $f = fopen($pid_file, 'r');
        $pid = trim(fgets($f));
        fclose($f);
        return $pid;
    }

    /**
     * update_running_process is update value of RUNNING_PROCESS in bb_settings table
     * @return boolean
     */
    private function update_running_process($running_process)
    {
        file_put_contents($this->running_process_file, $running_process);
        return true;
    }

    /**
     * showlogs is return array of filename based on selected service
     * @return Array
     */
    public function showlogs()
    {
        $data = array();
        if (isset($_POST['displaylog']) && $_POST['displaylog']) {

            $selected_log        = $_POST['displaylog'];
            if (file_exists($this->logs_dir.$selected_log)) {
                $selected_dir    = $this->logs_dir.$selected_log;
                $selected_files  = scandir($selected_dir);
                $data['files'][$selected_log] = $selected_files;
            } else {
                $data['log_not_found'] = true;
            }
        }
        if (isset($_GET['log_file']) && $_GET['log_file']) {
            $filename = $_GET['log_file'];
            $file = $this->logs_dir.$filename;
            if (file_exists($file)) {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: text/plain");
                header("Content-Transfer-Encoding: binary");
                // read the file from disk
                readfile($file);
                 die;
            }
        }
        load_views('service/showlogs', $data);
    }

    /**
    * assign_running_process_value is unserialize RUNNING_PROCESS array
    * @return Array
    */
    private function assign_running_process_value()
    {
        $data                = array();
        $running_process     = unserialize(RUNNING_PROCESS);
        $data['log_file']    = $running_process['log_file'];
        $data['pid']         = $running_process['pid'];
        if (isset($running_process['status']) && $running_process['status']) {
            $data['status']      = $running_process['status'];
        }
        if (isset($running_process['action']) && $running_process['action']) {
            $data['action']      = $running_process['action'];
        }
        if (isset($running_process['application']) && $running_process['application']) {
            $data['application'] = $running_process['application'];
        }
        return $data;
    }

    /**
     * get git tag list of selected option
     */
    public function get_tags_list()
    {
        if (isset($_POST) && isset($_POST['option'])) {
            $dpto = $_POST['option'];
            $cmd = 'cd '.APPLICATION_PATH . '/../'.$dpto.' && git tag';
            exec($cmd, $output_status, $command_return);
            echo json_encode($output_status);
            die;
        }
    }
}
