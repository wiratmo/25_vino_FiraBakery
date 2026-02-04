<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Foto extends CI_Controller {

public function __construct() {
    parent::__construct();
    $this->load->model('Foto_model');
}

// tampilkan semua foto milik produk
public function kelola($id_produk)
{
    $this->load->model('Foto_model');
    $data['fotos'] = $this->Foto_model->get_by_produk($id_produk);
    $data['id_produk'] = $id_produk;

    $this->Template->load('dashboard', 'gambar/kelola', $data);
}

// form tambah foto
public function tambah($id_produk) {
    if ($_FILES) {
        $config['upload_path']   = './assets/uploads/produk/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2048;
        $config['file_name']     = 'produk_' . time();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('gambar')) {
            $upload_data = $this->upload->data();
            $file_path = 'assets/uploads/produk/' . $upload_data['file_name'];

            $data = [
                'id_produk' => $id_produk,
                'gambar'    => $file_path
            ];
            $this->Foto_model->insert($data);
        }
        redirect('produk');
    } else {
        $data['id_produk'] = $id_produk;
        $this->load->view('foto/form_tambah', $data);
    }
}

public function update($id_gambar)
{
    if (!empty($_FILES['gambar']['name'])) {
        $config['upload_path']   = './assets/uploads/produk/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2048;
        $config['file_name']     = 'produk_' . time();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('gambar')) {
            $upload_data = $this->upload->data();
            $file_name   = $upload_data['file_name'];

            // ambil data lama
            $gambar_lama = $this->Foto_model->get_by_id($id_gambar);

            if ($gambar_lama && file_exists(FCPATH . $gambar_lama->gambar)) {
                unlink(FCPATH . $gambar_lama->gambar); // hapus file lama
            }

            // update DB dengan path lengkap
            $data = [
                'gambar' => 'assets/uploads/produk/' . $file_name
            ];
            $this->Foto_model->update($id_gambar, $data);

            $this->session->set_flashdata('success', 'Gambar berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
    }

    redirect('produk'); 
}

public function delete($id_gambar)
{
    // ambil data gambar berdasarkan id
    $gambar = $this->Foto_model->get_id_hapus($id_gambar);

    if ($gambar) {
        // hapus file fisik kalau ada
        $file_path = FCPATH . 'uploads/' . $gambar->nama_file;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // hapus record dari database
        $this->Foto_model->delete($id_gambar);

        $this->session->set_flashdata('alert', '<div class="alert alert-success">Gambar berhasil dihapus.</div>');
    } else {
        $this->session->set_flashdata('alert', '<div class="alert alert-danger">Gambar tidak ditemukan.</div>');
    }

    // kembali ke halaman produk
    redirect('produk');
}

}
