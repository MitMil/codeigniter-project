<?php

class Mdl_module extends CI_Model
{
    private $tbl_modules = 'modules';

    //get all modules information
    public function get_modules()
    {
        $sql = 'SELECT *, CASE WHEN parent = 0 THEN module_id ELSE parent END AS Sort  FROM '.$this->tbl_modules.' ORDER BY `Sort`, `module_id`';
        $datas = $this->db->query($sql);

        return $datas->result_array();
    }

    public function module_insert($db_data)
    {
        $db_data = insert_extra($db_data);
        $this->db->insert($this->tbl_modules, $db_data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function module_get_by_id($module_id)
    {
        $this->db->from($this->tbl_modules);
        $this->db->where('module_id', $module_id);
        $module = $this->db->get();
        $data = $module->row_array();

        return $data;
    }

    public function module_update($module_id, $db_data)
    {
        $db_data = update_extra($db_data);
        $this->db->where('module_id', $module_id);
        $upd = $this->db->update($this->tbl_modules, $db_data);
        if (!$upd) {
            // there was an error
            return false;
        } elseif (!$this->db->affected_rows()) {
            // no error, but nothing changed
            return true;
        } else {
            // record changed
            return true;
        }

        return false;
    }

    public function module_delete($module_id)
    {
        //delete module entry from module assign table
        $this->db->where('module_id', $module_id);
        $this->db->delete('module_assign');

        $this->db->where('module_id', $module_id);
        $upd = $this->db->delete($this->tbl_modules);
        if (!$upd) {
            // there was an error
            return false;
        } else {
            // record changed
            return true;
        }

        return false;
    }

    public function name_check($str)
    {
        $module_id = (isset($_GET['id'])) ? $_GET['id'] : '';

        if ($module_id) {
            $module_data = $this->module_get_by_id($module_id);
            if ($str == $module_data['module_name']) {
                return false;
            } else {
                $this->db->where_not_in('module_name', $module_data['module_name']);
                $this->db->where('module_name', trim($str));
                $this->db->from($this->tbl_modules);
                $count = $this->db->count_all_results();
                if ($count > 0) {
                    return $count;
                } else {
                    return false;
                }
            }
        } else {
            $this->db->where('module_name', trim($str));
            $this->db->from($this->tbl_modules);
            $count = $this->db->count_all_results();
            if ($count > 0) {
                return $count;
            } else {
                return false;
            }
        }
    }

    public function get_modules_assign($role_id = '')
    {
        if ($role_id) {
            $this->db->where('role_id', $role_id);
        }
        $datas = $this->db->get('module_assign');

        return $datas->result_array();
    }

    public function module_assign($data, $old_records)
    {
        if ($old_records) {
            foreach ($old_records as $key => $value) {
                $this->db->delete('module_assign', $value);
            }
        }
        $insert_batch = array();
        if ($data) {
            foreach ($data as $key => $value) {
                $insert_arr = array();

                $insert_arr = $value;
                $insert_arr = insert_extra($insert_arr);
                array_push($insert_batch, $insert_arr);
            }
            $this->db->insert_batch('module_assign', $insert_batch);
        }

        return true;
    }
    public function count_modules_role()
    {
        $this->db->select($this->tbl_modules.'.*, count(module_assign.role_id) as count_assign');
        $this->db->from($this->tbl_modules);
        $this->db->join('module_assign', 'module_assign.module_id = modules.module_id', 'left');

        $this->db->group_by('module_assign.module_id');
        $datas = $this->db->get();

        return $datas->result_array();
    }

    public function get_user_restrict_modules($user_id)
    {

        //SELECT m.* FROM `module_assign` ms left join modules as m on m.module_id = ms.module_id WHERE user_id = 3 OR role_id IN (select role_id from user_roles where user_id = 3) group by module_id
        $user_modules = $this->get_individual_module($user_id);
        if (!$user_modules) {
            $this->db->select('modules.*');
            $this->db->where('user_id = '.$user_id.' OR role_id IN (select role_id from user_roles where user_id = '.$user_id.')');
            $this->db->from('module_assign');
            $this->db->join('modules', 'modules.module_id = module_assign.module_id', 'left');
            $this->db->group_by('module_assign.module_id');
            $datas = $this->db->get();
            $user_modules = $datas->result_array();
        }

        $user_module_ids = array();
        if ($user_modules) {
            $user_module_ids = array_map(function ($user_modules) {
                 return $user_modules['module_id'];
            }, $user_modules);
        }

        $all_modules = $this->get_modules();

        $restrict_modules = array();
        if ($all_modules) {
            foreach ($all_modules as $value) {
                if (!in_array($value['module_id'], $user_module_ids)) {
                    $restrict_modules[] = $value['module_url'];
                }
            }
        }

        return $restrict_modules;

    }

    /**
     * get_individual_module will fetch if perticuler user has invidual permission for access module
     * @param  $user_id
     * @return Array
     */
    public function get_individual_module($user_id)
    {
        $this->db->select('modules.*');
        $this->db->where('user_id', $user_id);
        $this->db->from('module_assign');
        $this->db->join('modules', 'modules.module_id = module_assign.module_id', 'left');
        $this->db->group_by('module_assign.module_id');
        $datas = $this->db->get();
        $user_modules = $datas->result_array();
        return $user_modules;
    }

    /**
     * get_modules_user_id will return default role modules list
     * @param  $user_id
     * @return Array
     */
    public function get_modules_user_id($user_id)
    {
        $sql = 'SELECT modules.*, CASE WHEN parent = 0 THEN `modules`.`module_id` ELSE parent END AS Sort from modules LEFT JOIN `module_assign` ON `module_assign`.`module_id` = `modules`.`module_id` WHERE `module_assign`.`role_id`=(SELECT `roles`.`role_id` FROM `user_roles` LEFT JOIN `roles` ON `roles`.`role_id` = `user_roles`.`role_id` WHERE `user_roles`.`user_id` = '.$user_id.') ORDER BY `Sort`, `modules`.`module_id`';
        $datas = $this->db->query($sql);

        return $datas->result_array();
    }
}
