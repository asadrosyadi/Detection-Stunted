<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function getUserRoleById($role_id)
    {
        return $this->db->get_where('user_role', ['id' => $role_id])->row_array();
    }
    public function getUserRoleAll()
    {
        return $this->db->get('user_role')->result_array();
    }
}
