<?php
/**
 * Zira project.
 * console.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Dash\Dash;
use Zira;

class Console extends Model {
    const SESSION_NAME = 'zira-console';

    public function run() {
        if (!ENABLE_DASH_CONSOLE || strlen(CONSOLE_PASSWORD)==0 || !Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            Dash::forbidden();
        }
        $window_id = Zira\Request::post('id');
        $exec = Zira\Request::post('exec');
        $cd = Zira\Request::post('cd');
        $mode = Zira\Request::post('mode');
        $secret = (string)Zira\Request::post('secret');
        $code = Zira\Request::post('code');
        $result = null;

        if ($code == 'password' && $this->checkPassword($exec)) {
            $exec = '';
            $code = 'secret';
            $result = Dash::generateToken();
            Zira\Session::set(self::SESSION_NAME.'_'.$window_id, $result);
        } else if (!$this->checkRequest($window_id, $exec, $mode, $secret)) {
            sleep(1);
            $exec = '';
            $code = 'password';
            $result = '';
        }

        if ($exec=='exit') {
            if ($mode=='sh') $code = 'exit';
            else $mode = 'sh';
            $exec = '';
        } else if ($exec=='db') {
            $mode = 'db';
            $exec = '';
            $result = Zira\Db\Db::version();
        } else if ($exec=='php') {
            $mode = 'php';
            $exec = '';
            $result = 'PHP '.phpversion();
        }

        if (!empty($exec) && $mode=='sh' && preg_match('/^zira[\x20]*(.*)$/', $exec, $z)) {
            $result = $this->exec_zira($z[1]);
        } else if (!empty($exec) && $mode=='sh') {
            $return = $this->exec_sh($exec, $cd);
            $result = $return[0];
            $cd = $return[1];
        } else if (!empty($exec) && $mode=='db') {
            $return = $this->exec_db($exec);
            $result = $return[0];
            $code = $return[1];
        } else if (!empty($exec) && $mode=='php') {
            $return = $this->exec_php($exec);
            $result = $return[0];
            $code = $return[1];
        }

        if (empty($cd) && !is_string($code) && $code==0) $cd = exec('pwd');
        if (!empty($result) && (string)$code!='secret') $result = '<span style="color:'.($code==0 ? '#617B86' : '#BF39A1').'">'.nl2br(Zira\Helper::html($result)).'</span>';

        return array(
            'exec' => '',
            'result' => $result,
            'code' => Zira\Helper::html($code),
            'cd' => Zira\Helper::html($cd),
            'mode' => Zira\Helper::html($mode)
        );
    }

    private function checkPassword($password) {
        return ($password == CONSOLE_PASSWORD);
    }

    private function checkRequest($window_id, $exec, $mode, $secret) {
        $password = Zira\Session::get(self::SESSION_NAME.'_'.$window_id);
        if ($password===null) return false;
        return (md5(rawurlencode($exec.$mode.$password)) == $secret);
    }

    protected function exec_zira($cmd) {
        if (empty($cmd)) {
            $result = 'Zira CMS '.Zira\Zira::VERSION;
        } else {
            $result = null;
            $known_commands = array('install', 'uninstall', 'reinstall');
            $cmd = preg_replace('/[\x20]+/',' ', $cmd);
            $z_parts = explode(' ', $cmd);
            if (count($z_parts)==2 && in_array($z_parts[0],$known_commands)) {
                if ($z_parts[0] == 'install' || $z_parts[0] == 'uninstall' || $z_parts[0] == 'reinstall') {
                    $_z_parts = explode('/',$z_parts[1]);
                    if (count($_z_parts)!=2) $_z_parts = explode('\\',$z_parts[1]);
                    if (count($_z_parts)==2) {
                        $z_module = $_z_parts[0];
                        $z_table = $_z_parts[1];
                        $class = '\\'.ucfirst($z_module).'\\Install\\'.ucfirst($z_table);
                    } else {
                        $class = $z_parts[1];
                    }

                    try {
                        if (class_exists($class)) {
                            $z_table_object = new $class;
                            if (!($z_table_object instanceof Zira\Db\Table)) {
                                unset($z_table_object);
                            }
                        }
                    } catch(\Exception $e) {
                        $result = 'Table '.$class.' not found';
                    }

                    if (isset($z_table_object)) {
                        try {
                            if ($z_parts[0] == 'install') {
                                $z_table_object->install();
                                $result = 'Table ' . $class . ' installed successfully';
                            } else if ($z_parts[0] == 'uninstall') {
                                $z_table_object->uninstall();
                                $result = 'Table ' . $class . ' uninstalled successfully';
                            } else if ($z_parts[0] == 'reinstall') {
                                $z_table_object->uninstall();
                                $z_table_object->install();
                                $result = 'Table ' . $class . ' reinstalled successfully';
                            }
                        } catch(\Exception $e) {
                            $result = $e->getMessage();
                        }
                    }
                }
            } else {
                $result = 'Unknown command';
            }
        }

        return $result;
    }

    protected function exec_sh($exec, $cd) {
        $exec_prefix = '';
        if (!empty($cd)) $exec_prefix = "cd '".$cd."' && ";
        if (strpos($exec, 'cd ')===0) {
            $dir = trim(substr($exec,3));
            $dir_suffix = '';
            $p=strpos($dir,'&&');
            if ($p!==false) {
                $dir_suffix = trim(substr($dir,$p+2));
                $dir = trim(substr($dir,0,$p));
            }
            if (strpos($dir,'"')===false && strpos($dir,"'")===false && $dir!='~') $dir = "'".$dir."'";
            if ($dir!='~') {
                $cmd = $exec_prefix.'cd '.$dir.' && pwd';
            } else {
                $cmd = 'pwd';
            }
            exec($cmd.' 2>&1', $result, $code);
            if ($code==0 && count($result)==1) {
                $cd = $result[0];
                $exec_prefix = "cd '".$cd."' && ";
                $result = '';
            } else {
                $result = $dir.': No such file or directory';
            }
            if (!empty($dir_suffix)) {
                $cmd = $exec_prefix.$dir_suffix;
                exec($cmd.' 2>&1', $result, $code);
                $result = implode("\r\n", $result);
            }
        } else {
            $cmd = $exec_prefix.$exec;
            exec($cmd.' 2>&1', $result, $code);
            $result = implode("\r\n", $result);
        }

        return array($result, $cd);
    }

    protected function exec_db($exec) {
        try {
            $exec=trim($exec);
            if (substr($exec,-1)!=';') $exec.=';';
            $is_select=true;
            if (strpos(strtolower($exec),'insert')===0 || strpos(strtolower($exec),'update')===0 || strpos(strtolower($exec),'delete')===0) $is_select = false;
            $stmt = Zira\Db\Db::query($exec);
            if ($is_select) {
                $result = array();
                $co=0;
                $breaked = false;
                $columns = array();
                while ($row=Zira\Db\Db::fetch($stmt)) {
                    $co++;
                    $value = '';
                    $vars = get_object_vars($row);
                    foreach($vars as $var=>$val) {
                        if ($co==1) $columns[]=$var;
                        if (!empty($value)) $value.=' | ';
                        if ($val===null) $val='NULL';
                        $value.=$val;
                    }
                    $result[]=$value;
                    if ($co>=100) {
                        $breaked = true;
                        break;
                    }
                }
                Zira\Db\Db::free($stmt);
                $columns = implode(' | ',$columns);
                $result = $columns."\r\n".str_repeat('-',strlen($columns))."\r\n".implode("\r\n", $result);
                if ($breaked) $result.="\r\n".'Too much rows. Use limit !';
            } else {
                $result = $stmt->rowCount().' row(s) affected';
            }
            $code = 0;
        } catch(\Exception $e) {
            $result = $e->getMessage();
            $code = 1;
        }

        return array($result, $code);
    }

    protected function exec_php($exec) {
        try {
            $exec=trim($exec);
            if (substr($exec,-1)!=';') $exec.=';';
            ob_start();
            $return = eval($exec);
            $result = ob_get_clean();
            if ($return!==false) {
                $code = 0;
            } else {
                $code=1;
                $result = 'An error occurred';
            }

        } catch(\Exception $e) {
            $result = $e->getMessage();
            $code = 1;
        }

        return array($result, $code);
    }
}