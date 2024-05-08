<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    // Users Data
    public function getUserData()
    {
        $query = $this->db->get_where('user', ['email' => $this->session->userdata('email')]);
        return $query->row_array();
    }
    public function getUserDataAll()
    {
        $query = $this->db->get('user');
        return $query->result_array();
    }

    // Login
    public function userCheckLogin($email)
    {
        $this->db->where("email =  '$email'");
        $query = $this->db->get('user');
        return $query->row_array();
    }
}
