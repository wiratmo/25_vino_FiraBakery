<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_model');
    }

    public function index()
    {
        $data['kategori'] = $this->Kategori_model->get_all();
        $data['icons'] = [
            'flaticon-029-cupcake-3',
            'flaticon-002-cake',
            'flaticon-034-chocolate-roll',
            'flaticon-004-pizza',
            'flaticon-005-pancake',
            'flaticon-006-macarons'
        ];
        $this->Template->load('dashboard','kategori', $data);
    }

    public function save()
    {
        if ($this->input->post()) {
            $data = [
                'nama_kategori' => $this->input->post('nama_kategori'),
                'icon'          => $this->input->post('icon')
            ];
            $this->Kategori_model->insert($data);
            redirect('kategori');
        } else {
            // daftar icon (bisa juga simpan di config/helper biar rapih)
            $data['icons'] = [
                'flaticon-001-cupcake',
                'flaticon-002-cake',
                'flaticon-003-donut',
                'flaticon-004-pizza',
                'flaticon-005-bread',
                'flaticon-006-ice-cream'
            ];
            $this->Template->load('dashboard','kategori', $data);
        }
    }
    
// update menerima POST (tidak pakai parameter $id)
public function update()
{
    if ($this->input->post()) {
        $id = $this->input->post('id_kategori'); // dari hidden input dalam modal
        if (!$id) {
            show_error('ID kategori tidak ditemukan');
        }

        $data = [
            'nama_kategori' => $this->input->post('nama_kategori'),
            'icon'          => $this->input->post('icon')
        ];

        $this->Kategori_model->update($id, $data);
        $this->session->set_flashdata('success', 'Kategori berhasil diperbarui');
        redirect('kategori');
    } else {
        show_error('Request tidak valid');
    }
}

public function delete($id)
    {
        $this->Kategori_model->delete($id);
        redirect('kategori');
    }

    public function kontak(){
        $this->Template->load('user/template','user/kontak');
    }
}
