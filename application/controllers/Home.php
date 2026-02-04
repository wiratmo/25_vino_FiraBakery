<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Kategori_model');
        $this->load->model('Produk_model');
    }


    public function index($id_kategori = null) {
        $id_user = $this->session->userdata('id_user');
        $data['cart_header'] = $this->Produk_model->getCartHeader($id_user);
        $data['random_images'] = $this->Produk_model->get_random_images(6);

        $data['kategori'] = $this->Kategori_model->get_all();

        if ($id_kategori) {
            // produk per kategori
            $data['produk'] = $this->Produk_model->get_by_kategori($id_kategori, 8);
        } else {
            // kalau tidak pilih kategori → tampilkan semua produk
            $data['produk'] = $this->Produk_model->get_all(8);
        }

        $data['id_kategori_aktif'] = $id_kategori;

        $this->Template->load('user/template', 'user/main', $data);
    }

}

