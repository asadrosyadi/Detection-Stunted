<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('Menu_model', 'menu');
    }

    public function index()
    {
        $data['title'] = 'Menu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->form_validation->set_rules('menu', 'Menu', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New menu added!</div>');
            redirect('menu');
        }
    }


    public function submenu()
    {
        $data['title'] = 'Submenu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->load->model('Menu_model', 'menu');

        $data['subMenu'] = $this->menu->getSubMenu();
        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'icon', 'required');

        if ($this->form_validation->run() ==  false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'title' => $this->input->post('title'),
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active')
            ];
            $this->db->insert('user_sub_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New sub menu added!</div>');
            redirect('menu/submenu');
        }
    }

    public function editmenu($menu_id)
    {
        $data['title'] = 'Edit Menu';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['menu'] = $this->db->get_where('user_menu', ['id' => $menu_id])->row_array();

        $this->form_validation->set_rules('menu', 'Menu Name', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/edit-menu', $data);
            $this->load->view('templates/footer');
        } else {
            $menu_name = $this->input->post('menu');
            $getMenu = $this->db->get_where('user_menu', ['menu' => $menu_name]);

            if ($getMenu->num_rows() < 1) {
                $this->db->set('menu', $menu_name);
                $this->db->where('id', $menu_id);
                $this->db->update('user_menu');
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Edit Menu Success!</div>');
                redirect('menu');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This Menu name is exist or same!</div>');
                redirect('menu/editmenu/' . $menu_id);
            }
        }
    }

    public function deletemenu($menu_id)
    {
        $menu = $this->db->get_where('user_menu', ['id' => $menu_id])->row_array();

        $this->db->delete('user_menu', ['id' => $menu_id]);
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">' . $menu['menu'] . ' menu is deleted!</div>');
        redirect('menu');
    }

    public function editsub($submenu_id)
    {
        $data['title'] = 'Edit Submenu';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['menu'] = $this->db->get('user_menu')->result_array();
        $data['submenu'] = $this->db->get_where('user_sub_menu', ['id' => $submenu_id])->row_array();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'Icon', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/edit-submenu', $data);
            $this->load->view('templates/footer', $data);
        } else {
            $submenu_name = $this->input->post('title');
            $data_sub = [
                'title' => $submenu_name,
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active')
            ];
            $this->db->set($data_sub);
            $this->db->where('id', $submenu_id);
            $this->db->update('user_sub_menu');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Edit Submenu Success!</div>');
            redirect('menu/submenu');
        }
    }
    public function deletesub($submenu_id)
    {
        $submenu = $this->db->get_where('user_sub_menu', ['id' => $submenu_id])->row_array();

        $this->db->delete('user_sub_menu', ['id' => $submenu_id]);
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">' . $submenu['title'] . ' Submenu is deleted!</div>');
        redirect('menu/submenu');
    }
}
