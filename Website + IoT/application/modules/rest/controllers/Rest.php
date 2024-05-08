<?php

class Rest extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        //is_logged_in();
    }

    function index()
    {
        $tolak = json_encode("access denied");
        echo $tolak;
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
