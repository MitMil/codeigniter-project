<?php
class Mdl_role extends CI_Model
{
    private $tbl_roles = 'roles';

    //get all roles information
    public function get_roles($year = '', $month = '')
    {

        $this->load->model('Mdl_user', 'mdl_user');
        $role_restrinction = $this->mdl_user->user_restrictions_roles('role_id');

        $this->db->select($this->tbl_roles.'.*, count(user_roles.id) as count_assign');
        $this->db->from($this->tbl_roles);
        $this->db->join('user_roles', 'user_roles.role_id = roles.role_id', 'left');
        //$this->db->where('roles.name','Manager');
        if ($role_restrinction) {
            $this->db->where_not_in($this->tbl_roles.'.role_id', $role_restrinction);
        }

        if ($year || $month) {
            $this->db->join('users', 'users.user_id = user_roles.user_id', 'left');
        }
        if ($year) {
            $this->db->where('YEAR(users.insert_datetime)', $year);
        }
        if ($month) {
            $this->db->where('MONTH(users.insert_datetime)', $month);
        }

        $this->db->group_by('roles.role_id');
        $datas = $this->db->get();
        return $datas->result_array();
    }

    //get all users information
    public function get_role_users($role_id)
    {

        $this->db->select('user_roles.*, users.*');
        $this->db->from('user_roles');
        $this->db->join('users', 'users.user_id = user_roles.user_id', 'left');
        $this->db->where('user_roles.role_id', $role_id);
        $datas = $this->db->get();
        return $datas->result_array();
    }

    public function role_get_by_id($role_id)
    {

        $this->load->model('Mdl_user', 'mdl_user');
        $role_restrinction = $this->mdl_user->user_restrictions_roles('role_id');

        $this->db->select($this->tbl_roles.'.*, count(user_roles.id) as count_assign');
        $this->db->from($this->tbl_roles);
        $this->db->join('user_roles', 'user_roles.role_id = roles.role_id', 'left');
        $this->db->where('roles.role_id', $role_id);
        if ($role_restrinction) {
            $this->db->where_not_in($this->tbl_roles.'.role_id', $role_restrinction);
        }
        $this->db->order_by('roles.update_datetime', 'DESC');
        $this->db->group_by('roles.role_id');
        $data = $this->db->get();
        return $data->row_array();
    }

    public function role_insert($db_data)
    {
        $db_data = insert_extra($db_data);
        $this->db->insert($this->tbl_roles, $db_data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function role_update($role_id, $db_data)
    {
        $db_data = update_extra($db_data);
        $this->db->where('role_id', $role_id);
        $upd = $this->db->update($this->tbl_roles, $db_data);
        if (!$upd) {
           // there was an error
            return false;
        } else if (!$this->db->affected_rows()) {
           // no error, but nothing changed
            return true;
        } else {
           // record changed
            return true;
        }

        return false;
    }

    public function role_delete($role_id)
    {

        $data = $this->role_get_by_id($role_id);

        if ($data) {
            $roles = $this->get_role_users($role_id);

            if ($roles) {
                // Delete role_id entry from user_roles table
                $this->db->delete('user_roles', array('role_id' => $role_id));
                // Delete role_id entry from module_assign table
                $this->db->delete('module_assign', array('role_id' => $role_id));
            }

            $this->db->where('role_id', $role_id);
            $upd =  $this->db->delete($this->tbl_roles);

            if (!$upd) {
               // there was an error
                return false;
            } else {
               // record changed
                return true;
            }
        }

        return false;
    }

    public function name_check($str)
    {

        $role_id = (isset($_GET['id'])) ? $_GET['id'] : '';

        if ($role_id) {
            $role_data = $this->role_get_by_id($role_id);
            if ($str == $role_data['name']) {
                return false;
            } else {
                $this->db->where_not_in('name', $role_data['name']);
                $this->db->where('name', trim($str));
                $this->db->from($this->tbl_roles);
                $count = $this->db->count_all_results();
                if ($count > 0) {
                    return $count;
                } else {
                    return false;
                }
            }
        } else {
            $this->db->where('name', trim($str));
            $this->db->from($this->tbl_roles);
            $count = $this->db->count_all_results();
            if ($count > 0) {
                return $count;
            } else {
                return false;
            }
        }
    }
}
