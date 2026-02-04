<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

public function __construct()
{
    parent::__construct();
    $this->load->model('Produk_model');
}

public function index()
{
    $id_user = $this->session->userdata('id_user');
    if (!$id_user) {
        redirect('auth/login');
    }

    $data['cart'] = $this->Produk_model->getCartByUser($id_user);
    $id_user = $this->session->userdata('id_user');
    $data['cart_header'] = $this->Produk_model->getCartHeader($id_user);

    $this->Template->load('user/template','user/cart', $data);
}

public function update()
{
    $id_user = $this->session->userdata('id_user');
    $qtys = $this->input->post('qty'); // array: id_cart => qty

    if ($qtys) {
        foreach ($qtys as $id_cart => $qty) {
            $this->Produk_model->updateQty($id_cart, $qty, $id_user);
        }
    }

    redirect('cart');
}

public function delete($id_cart)
{
    $id_user = $this->session->userdata('id_user');
    $this->Produk_model->deleteItem($id_cart, $id_user);
    redirect('cart');
}

public function transaksi()
{
    $id_user = $this->session->userdata('id_user');
    $user = $this->User_model->getById($id_user); // ambil data user
    $cart = $this->Produk_model->getByUser($id_user);

    if (!$cart) {
        redirect('cart');
    }

    // hitung total
    $total = 0;
    $pesan = "Halo, saya *{$user->nama}* mau pesan:%0A"; // tambahin nama di awal
    foreach ($cart as $item) {
        $subtotal = $item->harga * $item->qty;
        $total += $subtotal;
        $pesan .= "- {$item->nama_produk} ({$item->qty} x Rp{$item->harga}) = Rp{$subtotal}%0A";
    }
    $pesan .= "Total: Rp{$total}%0A";

    // simpan ke tabel transaksi
    $id_transaksi = $this->Produk_model->createTransaksi($id_user, $cart, $total);

    // kosongkan cart
    $this->Produk_model->clearCart($id_user);

    // redirect ke WhatsApp
    $no_wa = "0281326502929"; // nomor WA pemilik toko
    redirect("https://wa.me/{$no_wa}?text=" . $pesan);
}



}

