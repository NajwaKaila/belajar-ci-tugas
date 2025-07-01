<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $client;
    protected $apiKey;
    protected $transaction;
    protected $transaction_detail;
   
    function __construct()
    {
        helper('number');
        helper('form');
        $this->cart = \Config\Services::cart();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        return view('v_keranjang', $data);
    }

    public function profile()
{
    $username = session()->get('username');
    $data['username'] = $username;

    $buy = $this->transaction->where('username', $username)->findAll();
    $data['buy'] = $buy;

    $product = [];

    if (!empty($buy)) {
        foreach ($buy as $item) {
            $detail = $this->transaction_detail->select('transaction_detail.*, product.nama, product.harga, product.foto')->join('product', 'transaction_detail.product_id=product.id')->where('transaction_id', $item['id'])->findAll();

            if (!empty($detail)) {
                $product[$item['id']] = $detail;
            }
        }
    }

    $data['product'] = $product;

    return view('v_profile', $data);
}

    public function cart_add()
    {
        $hargaAsli = $this->request->getPost('harga');
        $diskon = session()->get('diskon') ?? 0;
        $hargaDiskon = $hargaAsli - $diskon;

        $this->cart->insert([
        'id'      => $this->request->getPost('id'),
        'qty'     => 1,
        'price'   => $hargaDiskon,
        'name'    => $this->request->getPost('nama'),
        'options' => [
        'foto' => $this->request->getPost('foto'),
        'harga_asli' => $hargaAsli,
        'diskon' => $diskon
        ]
        ]);

        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() . 'keranjang">Lihat</a>)');
        return redirect()->to(base_url('/'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $value) {
            $this->cart->update(array(
                'rowid' => $value['rowid'],
                'qty'   => $this->request->getPost('qty' . $i++)
            ));
        }

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
{
    $data['items'] = $this->cart->contents();
    $data['total'] = $this->cart->total();

    return view('v_checkout', $data);
}
public function form_upload($id)
{
    $transaksi = $this->transaction->find($id);
    if (!$transaksi) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException("Transaksi tidak ditemukan");
    }

    return view('v_upload_bukti', ['transaksi' => $transaksi]);
}

public function upload_bukti($id)
{
    $file = $this->request->getFile('bukti');

    if ($file->isValid() && !$file->hasMoved()) {
        $newName = $file->getRandomName();
        $file->move('uploads/bukti', $newName);

        $this->transaction->update($id, [
            'bukti' => $newName
        ]);

        return redirect()->to('/')->with('success', 'Bukti pembayaran berhasil diupload');
    }

    return redirect()->back()->with('failed', 'Upload gagal');
}


public function getLocation()
{
		//keyword pencarian yang dikirimkan dari halaman checkout
    $search = $this->request->getGet('search');

    $response = $this->client->request(
        'GET', 
        'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search='.$search.'&limit=50', [
            'headers' => [
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ],
        ]
    );

    $body = json_decode($response->getBody(), true); 
    return $this->response->setJSON($body['data']);
}

public function getCost()
{ 
		//ID lokasi yang dikirimkan dari halaman checkout
    $destination = $this->request->getGet('destination');

		//parameter daerah asal pengiriman, berat produk, dan kurir dibuat statis
    //valuenya => 64999 : PEDURUNGAN TENGAH , 1000 gram, dan JNE
    $response = $this->client->request(
        'POST', 
        'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'multipart' => [
                [
                    'name' => 'origin',
                    'contents' => '64999'
                ],
                [
                    'name' => 'destination',
                    'contents' => $destination
                ],
                [
                    'name' => 'weight',
                    'contents' => '1000'
                ],
                [
                    'name' => 'courier',
                    'contents' => 'jne'
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ],
        ]
    );

    $body = json_decode($response->getBody(), true); 
    return $this->response->setJSON($body['data']);
}

public function buy()
{
    if ($this->request->getPost()) { 
        $dataForm = [
            'username' => $this->request->getPost('username'),
            'total_harga' => $this->request->getPost('total_harga'),
            'alamat' => $this->request->getPost('alamat'),
            'ongkir' => $this->request->getPost('ongkir'),
            'status' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->transaction->insert($dataForm);

        $last_insert_id = $this->transaction->getInsertID();

        foreach ($this->cart->contents() as $value) {
            $diskon = $value['options']['diskon'] ?? 0;
            $hargaAsli = $value['options']['harga_asli'] ?? $value['price'];

            $dataFormDetail = [
                'transaction_id' => $last_insert_id,
                'product_id' => $value['id'],
                'jumlah' => $value['qty'],
                'diskon' => $value['options']['diskon'] ?? 0,
                'subtotal_harga' => $value['qty'] * $value['price'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $this->transaction_detail->insert($dataFormDetail);
        }

        $this->cart->destroy();
 
        return redirect()->to(base_url());
    }
}

}
