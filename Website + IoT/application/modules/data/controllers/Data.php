<?php

class Data extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataModel');
        is_logged_in();
    }

    function index()
    {
        $datas['data'] = $this->db->select('*')->from('data_pengukuran')->get()->result();
        $datas['data2'] = $this->db->select('*')->from('historysesnor')->limit(1)->order_by('id', 'DESC')->get()->result(); //Untuk mengambil data dari database webinar
        $datas['title'] = 'Measurement';
        $datas['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->load->view('templates/header', $datas);
        $this->load->view('templates/sidebar', $datas);
        $this->load->view('templates/topbar', $datas);
        $this->template->load('template1', 'data/index', $datas);
    }

    function download_excel()
    {
        $datas['data'] = $this->db->select('*')->from('data_pengukuran')->get()->result();
        $datas['data2'] = $this->db->select('*')->from('historysesnor')->limit(1)->order_by('id', 'DESC')->get()->result(); //Untuk mengambil data dari database webinar
        $datas['title'] = 'Measurement';
        $datas['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->load->view('data/download_excel', $datas);
    }

    function historysensor()
    {
        $datas['title'] = 'History Sensor';
        $datas['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $datas['data'] = $this->db->select('*')->from('historysesnor')->order_by('id', 'DESC')->get()->result(); //Untuk mengambil data dari database webinar
        $datas['data2'] = $this->db->select('*')->from('historysesnor')->limit(1)->order_by('id', 'DESC')->get()->result(); //Untuk mengambil data dari database webinar

        $this->load->view('templates/header', $datas);
        $this->load->view('templates/sidebar', $datas);
        $this->load->view('templates/topbar', $datas);
        $this->template->load('template1', 'data/historysensor', $datas);
        //$this->load->view('templates/footer'); // gak usah di pakai
    }

    function add()
    {
        $this->load->helper('string');
        $isi = array(
            'name'     => $this->input->post('name'),
            'gender'    => $this->input->post('gender'),
            'date_brith'    => $this->input->post('date_brith'),
            'parents'    => $this->input->post('parents'),
            'address'    => $this->input->post('address'),
            'date_create'    => $this->input->post('date_create'),
            'mass'    => $this->input->post('mass'),
            'height'    => $this->input->post('height'),
            'head'    => $this->input->post('head')
        );
        $this->db->insert('data_pengukuran', $isi);
        redirect('data');
    }

    function edit()
    {
        if (isset($_POST['submit'])) {
            $data = array(
                'name'     => $this->input->post('name'),
                'gender'    => $this->input->post('gender'),
                'date_brith'     => $this->input->post('date_brith'),
                'parents'     => $this->input->post('parents'),
                'address'     => $this->input->post('address'),
                'date_create'     => $this->input->post('date_create'),
                'mass'     => $this->input->post('mass'),
                'height'     => $this->input->post('height'),
                'head'     => $this->input->post('head'),
            );
            $id   = $this->input->post('id');
            $this->db->where('id', $id);
            $this->db->update('data_pengukuran', $data);
            redirect('data');
        } else {
            $id           = $this->uri->segment(3);
            $datas['data'] = $this->db->get_where('data_pengukuran', array('id' => $id))->row_array();
            $datas['data2'] = $this->db->select('*')->from('data_pengukuran')->where('id', $this->uri->segment(3))->limit(1)->order_by('id', 'DESC')->get()->result();
            $datas['title'] = 'Measurement';
            $datas['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            $this->load->view('templates/header', $datas);
            $this->load->view('templates/sidebar', $datas);
            $this->load->view('templates/topbar', $datas);
            $this->template->load('template1', 'edit', $datas);
        }
    }

    function hapus()
    {
        $id = $this->uri->segment(3);
        if (!empty($id)) {
            $this->db->where('id', $id);
            $this->db->delete('data_pengukuran');
        }
        redirect('data');
    }

    public function reset_data()
    {
        // Panggil fungsi untuk menghapus semua data
        $this->DataModel->reset_data();

        // Redirect kembali ke halaman index
        redirect('data/historysensor');
    }

    function bacajason()
    {
        $data = $this->db->select('*')->from('status')->limit(1)->order_by('id', 'DESC')->get()->result();
        $response = array("Data" => array());
        foreach ($data as $r) {
            $temp = array(
                "status" => $r->status
            );

            array_push($response["Data"], $temp);
        }
        $data = json_encode($response);
        echo "$data";
    }

    function changeStatus()
    {
        // Ubah status menjadi ON
        $this->db->update('status', array('status' => 'ON'));

        // Tunggu selama 1 detik
        sleep(1);

        // Ubah status kembali menjadi OFF setelah 1 detik
        $this->db->update('status', array('status' => 'OFF'));

        // Redirect kembali ke halaman yang sesuai
        redirect('data');
    }

    public function kirimdatasensor()
    {
        $isi = array(
            'mass'     => $_GET['mass'],
            'height'     => $_GET['height'],
            'head'     => $_GET['head']
        );
        $this->db->insert('historysesnor', $isi);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "gagal";
        } else {
            echo "sukses";
        }
    }
}
