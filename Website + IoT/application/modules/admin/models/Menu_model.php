<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function getSubMenu()
    {
        $query = "SELECT user_sub_menu.*, user_menu.menu FROM user_sub_menu JOIN user_menu ON user_sub_menu.menu_id = user_menu.id";
        return $this->db->query($query)->result_array();
    }


    public function showMenu($role_id)
    {
        $queryMenu = "SELECT user_menu.id, menu FROM user_menu JOIN user_access_menu ON user_menu.id = user_access_menu.menu_id WHERE user_access_menu.role_id =  $role_id ORDER BY user_access_menu.menu_id ASC";
        return $this->db->query($queryMenu)->result_array();
    }

    public function showSubMenu($menuId)
    {
        $querySubMenu = "SELECT * FROM user_sub_menu  WHERE menu_id = $menuId AND is_active = 1";
        return $this->db->query($querySubMenu)->result_array();
    }
    // User Menu
    public function getUserMenuAll()
    {
        return $this->db->get_where('user_menu', ['id !=' => 1])->result_array();
    }
}
