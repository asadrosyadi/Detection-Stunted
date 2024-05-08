<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataModel extends CI_Model
{
    public function reset_data()
    {
        // Hapus semua data dari tabel 'datasensor'
        $this->db->empty_table('historysesnor');
    }
}
