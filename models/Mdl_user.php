<?php
class Mdl_user extends CI_Model
{
    private $tbl_users = 'users';

    public function get_next_insertid()
    {
        $table_name = $this->tbl_users;
        $query = $this->db->query("SHOW TABLE STATUS WHERE name='$table_name'");
        $row = $query->row_array();
        return $row["Auto_increment"];
    }

    //login user check
    public function login($username, $password)
    {
        $this->db->select('*');
        $this->db->from($this->tbl_users);
        $this->db->where('password', MD5($password));
        $this->db->where('username', $username);
        $this->db->or_where('email', $username);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function check_login()
    {
        if ($this->session->userdata('user')) {
            return true;
        } else {
            return false;
        }
    }
    //get all users information
    public function get_users($filter = array())
    {

        $user_restriction = $this->user_restrictions_roles('user_id');

        if ($filter) {

            foreach ($filter as $key => $value) {
                switch ($key) {
                    case 'role_id':
                        $this->db->select($this->tbl_users.'.*');
                        $this->db->from($this->tbl_users);
                        $this->db->join('user_roles', 'user_roles.user_id = '.$this->tbl_users.'.user_id', 'left');
                        $this->db->where('user_roles.role_id', $value);
                        if ($user_restriction) {
                            $this->db->where_not_in($this->tbl_users.'.user_id', $user_restriction);
                        }
                        $users = $this->db->get();
                        $data = $users->result_array();

                        $i=0;
                        foreach ($data as $key => $value) {
                            $data[$i]['roles'] = $this->get_user_roles($value['user_id']);
                            $i++;
                        }

                        return $data;
                    break;

                }
            }
        }

        if ($user_restriction) {
            $this->db->where_not_in('user_id', $user_restriction);
        }
        $this->db->from($this->tbl_users);
        $users = $this->db->get();
        $data = $users->result_array();

        $i=0;
        foreach ($data as $key => $value) {
            $data[$i]['roles'] = $this->get_user_roles($value['user_id']);
            $i++;
        }

        //echo $this->db->last_query();
        //p($data); die;
        return $data;
    }

    //get all users information
    public function get_user_roles($user_id)
    {

        $this->db->select('user_roles.*, roles.name as role_name');
        $this->db->from('user_roles');
        $this->db->join('roles', 'roles.role_id = user_roles.role_id', 'left');
        $this->db->where('user_roles.user_id', $user_id);
        $users = $this->db->get();
        return $users->result_array();
    }

    public function user_get_by_id($user_id, $login = false)
    {

        if (!$login) {
            $user_restriction = $this->user_restrictions_roles($get = 'user_id');
            if ($user_restriction) {
                $this->db->where_not_in('user_id', $user_restriction);
            }
        }

        $this->db->from($this->tbl_users);
        $this->db->join('languages', 'languages.idlang = users.language', 'left');
        $this->db->where('user_id', $user_id);
        $user = $this->db->get();
        $data = $user->row_array();
        if ($data) {
            $data['roles'] = $this->get_user_roles($user_id);
        }

        return $data;

    }

    public function user_get_by_username($user_name, $login = false)
    {
        $this->db->from($this->tbl_users);
        $this->db->where('username', $user_name);
        $user = $this->db->get();
        $data = $user->row_array();

        if ($data) {
            $data['roles'] = $this->get_user_roles($data['user_id']);

        }
        return $data;
    }

    public function user_insert($db_data)
    {
        $db_data = insert_extra($db_data);
        $this->db->insert($this->tbl_users, $db_data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }


    public function insert_user_role($db_data)
    {

        if (is_array($db_data['role_id'])) {
            $data = array();
            foreach ($db_data['role_id'] as $key => $role_id) {
                $insert_arr['role_id'] = $role_id;
                $insert_arr['user_id'] = $db_data['user_id'];
                $insert_arr = insert_extra($insert_arr);
                array_push($data, $insert_arr);
            }
            $this->db->insert_batch('user_roles', $data);
            return true;
        } else {
            $db_data = insert_extra($db_data);
            $this->db->insert('user_roles', $db_data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }

    public function user_update($user_id, $db_data)
    {

        $db_data = update_extra($db_data);
        $this->db->where('user_id', $user_id);
        $upd = $this->db->update($this->tbl_users, $db_data);
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

    public function update_user_role($user_id, $db_data)
    {

        $roles = $this->get_user_roles($user_id);

        if ($roles) {

            $this->db->where('user_id', $user_id);
            $upd = $this->db->delete('user_roles');
        }

        if (is_array($db_data['role_id'])) {
            $data = array();
            foreach ($db_data['role_id'] as $key => $role_id) {
                $insert_arr['role_id'] = $role_id;
                $insert_arr['user_id'] = $user_id;
                $insert_arr = insert_extra($insert_arr);
                array_push($data, $insert_arr);
            }
            $this->db->insert_batch('user_roles', $data);
            return true;
        } else {

            $db_data['user_id'] = $user_id;
            $db_data = insert_extra($db_data);
            $this->db->insert('user_roles', $db_data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }

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

    public function user_delete($user_id)
    {
        $user_details = $this->session->userdata('user');
        // Session user can not delete own profile
        if ($user_id==$user_details['id']) {
            return false;
        }
        $data = $this->user_get_by_id($user_id);
        if ($data) {
            $roles = $data['roles'];
            if ($roles) {
                // Delete user_id entry from user_roles table
                $this->db->delete('user_roles', array('user_id' => $user_id));
                // Delete user_id entry from user_roles table
                $this->db->delete('module_assign', array('user_id' => $user_id));
            }
            $this->db->where('user_id', $user_id);
            $upd =  $this->db->delete($this->tbl_users);

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

    public function username_check($str)
    {

        $user_id = (isset($_GET['id'])) ? $_GET['id'] : '';

        if ($user_id) {
            $user_data = $this->user_get_by_id($user_id);
            if ($str == $user_data['username']) {
                return false;
            } else {
                $this->db->where_not_in('username', $user_data['username']);
                $this->db->where('username', trim($str));
                $this->db->from($this->tbl_users);
                $count = $this->db->count_all_results();
                if ($count > 0) {
                    return $count;
                } else {
                    return false;
                }
            }
        } else {
            $this->db->where('username', trim($str));
            $this->db->from($this->tbl_users);
            $count = $this->db->count_all_results();
            if ($count > 0) {
                return $count;
            } else {
                return false;
            }
        }
    }

    public function email_check($str)
    {

        $user_id = (isset($_GET['id'])) ? $_GET['id'] : '';

        if ($user_id) {
            $user_data = $this->user_get_by_id($user_id);
            if ($str == $user_data['email']) {
                return false;
            } else {
                $this->db->where_not_in('email', $user_data['email']);
                $this->db->where('email', trim($str));
                $this->db->from($this->tbl_users);
                $count = $this->db->count_all_results();
                if ($count > 0) {
                    return $count;
                } else {
                    return false;
                }
            }
        } else {

            $this->db->where('email', trim($str));
            $this->db->from($this->tbl_users);
            $count = $this->db->count_all_results();

            if ($count > 0) {
                return $count;
            } else {
                return false;
            }

        }
    }

    public function get_user_by_roles($roles = 'all', $select = '*')
    {

        $this->db->select($this->tbl_users.'.'.$select);
        $this->db->from($this->tbl_users);
        $this->db->join('user_roles', 'user_roles.user_id = '.$this->tbl_users.'.user_id', 'left');
        if ($roles != 'all') {
            $this->db->where_in('user_roles.role_id', $roles);
        }
        $users = $this->db->get();
        $data = $users->result_array();

        if ($select != '*') {
            $new_data = array();
            foreach ($data as $key => $value) {
                $new_data[] = $value[$select];
            }

            return $new_data;
        } else {
            return $data;
        }
    }

    public function user_restrictions_roles($get = 'role_id')
    {

        $user_details = $this->session->userdata('user');
        $user_restriction_roles = array(1, 2); // Hide SUI and Service for other users
        $roles = explode(',', $user_details['roles']);
        foreach ($roles as $key => $role) {
            switch ($role) {
                case '1':
                    // Allow all users for admin.
                    $user_restriction_roles = array();
                    break;

                case '2':
                    // Allow Service, Manager, Farmer for Service user.
                    $user_restriction_roles = array(1);
                    break;

                case '3':
                    // Allow Manager, Farmer for Manager user.
                    $user_restriction_roles = array(1, 2);
                    break;

            }
        }

        if ($user_restriction_roles) {
            switch ($get) {
                case 'role_id':
                    return $user_restriction_roles;
                    break;

                case 'user_id':
                    return $this->get_user_by_roles($user_restriction_roles, $select = 'user_id');
                    break;

                default:
                    # code...
                    break;
            }
        } else {
            return $user_restriction_roles;
        }
    }
}
