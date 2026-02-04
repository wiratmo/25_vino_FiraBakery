<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Produk_model');
    }

    public function index()
    {
        $data['produk']   = $this->Produk_model->get_all_for_admin();  // join ke kategori biar tampil nama_kategori
        $data['kategori'] = $this->Kategori_model->get_all(); // buat isi dropdown
            // bikin array gambar per produk
    $gambar = $this->Foto_model->get_all();
    $grouped = [];
    foreach ($gambar as $g) {
        $grouped[$g->id_produk][] = $g;
    }
    $data['gambar'] = $grouped;
        $this->Template->load('dashboard','produk', $data);
    }
    
    public function simpan()
    {
        $data_produk = [
            'nama_produk' => $this->input->post('nama_produk'),
            'id_kategori' => $this->input->post('id_kategori'),
            'harga'       => $this->input->post('harga'),
            'deskripsi'   => $this->input->post('deskripsi')
        ];
    
        $this->Produk_model->insert_produk($data_produk);
    
        $this->session->set_flashdata('success', 'Produk berhasil disimpan');
        redirect('produk');
    }
    
    public function update($id)
    {
        $produk = $this->Produk_model->get_by_id($id);
    
        if (!$produk) {
            show_404();
        }
    
        if ($this->input->post()) {
            $data = [
                'nama_produk' => $this->input->post('nama_produk'),
                'id_kategori' => $this->input->post('id_kategori'),
                'harga'       => $this->input->post('harga'),
                'deskripsi'   => $this->input->post('deskripsi')
            ];
    
            $this->Produk_model->update($id, $data);
            $this->session->set_flashdata('success', 'Produk berhasil diperbarui');
            redirect('produk');
        } else {
            $data['produk']   = $produk;
            $data['kategori'] = $this->Kategori_model->get_all();
            $this->load->view('produk/form_edit', $data);
        }
    }
    
    public function delete($id) {
        $this->Produk_model->delete($id);
        //$this->session->set_flashdata('alert', '<div class="alert alert-success">Produk berhasil dihapus.</div>');
        redirect('produk');
    }

    public function detail($id_produk)
    {
        $data['produk'] = $this->Produk_model->getWithKategori($id_produk);
        $data['gambar'] = $this->Produk_model->getImages($id_produk);
        $data['related'] = $this->Produk_model->getRelated($data['produk']->id_kategori, $id_produk);
    
        if (!$data['produk']) {
            show_404();
        }
    
        $this->Template->load('user/template', 'user/produk_detail', $data);
    } 

    public function kategori($id_kategori)
    {
        // ambil kategori untuk slider
        $data['kategori'] = $this->Kategori_model->get_all();
    
        // ambil produk berdasarkan kategori
        $data['produk'] = $this->Produk_model->get_by_kategori($id_kategori);
    
        // ambil random images (BIAR TIDAK ERROR)
        $data['random_images'] = $this->Produk_model->get_random_images(6);
    
        $this->Template->load('user/template','user/main', $data);
    }
    


    public function search()
{
    $search = $this->input->get('search'); 
    $kategori_id = $this->input->get('kategori');

    $data['produk'] = $this->Produk_model->getProdukFiltered($search, $id_kategori);

    // untuk menampilkan kategori di dropdown filter
    $data['kategori'] = $this->db->get('kategori')->result();

    $this->Template->load('user/template','user/main', $data);
}

    public function shop()
    {
        $this->load->library('pagination');
        $this->load->model('Produk_model');
        $this->load->model('Kategori_model');
    
        // Ambil input dari form
        $search   = $this->input->get('search');
        $kategori = $this->input->get('kategori');
        $sort     = $this->input->get('sort');
    
        // Pagination config
        $config['base_url'] = site_url('produk/shop');
        $config['total_rows'] = $this->Produk_model->countAllProduk($search, $kategori);
        $config['per_page'] = 9;
        $config['page_query_string'] = TRUE; // biar pakai ?per_page=9 bukan segment URI
    
        // Styling pagination
        $config['full_tag_open'] = '<div class="shop__pagination">';
        $config['full_tag_close'] = '</div>';
        $config['cur_tag_open'] = '<a class="active">';
        $config['cur_tag_close'] = '</a>';
        $config['num_tag_open'] = '<a>';
        $config['num_tag_close'] = '</a>';
        $config['next_link'] = '<span class="arrow_carrot-right"></span>';
        $config['prev_link'] = '<span class="arrow_carrot-left"></span>';
    
        $this->pagination->initialize($config);
    
        $page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
    
        // Ambil produk sesuai filter
        $data['produk'] = $this->Produk_model->getProduk($config['per_page'], $page, $search, $kategori, $sort);
        $data['kategori'] = $this->Kategori_model->get_all();
        $data['pagination'] = $this->pagination->create_links();
    
        $this->Template->load('user/template', 'user/shop', $data);
    }
  
    public function add_to_cart($id_produk)
{
    if (!$this->session->userdata('logged_in')) {
        // Simpan URL produk detail yang dipilih supaya bisa balik ke sana setelah login
        $this->session->set_userdata('redirect_url', site_url('produk/detail/'.$id_produk));
        redirect('auth/login');
    }

    // Kalau sudah login, langsung tambahkan produk ke cart
    $this->Produk_model->addToCart($id_produk, $this->session->userdata('id_user'));
    redirect('cart');
}


}
