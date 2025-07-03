<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionDetailModel extends Model
{
    protected $table = 'transaction_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'transaction_id', 'product_id', 'jumlah', 'diskon', 'subtotal_harga', 'created_at', 'updated_at'
    ];

    // ✅ Fungsi custom query berada di dalam class
    public function getDetailWithProduct($transaction_id)
    {
        return $this->select('transaction_detail.jumlah, product.harga, product.nama')
                    ->join('product', 'transaction_detail.product_id = product.id')
                    ->where('transaction_id', $transaction_id)
                    ->findAll();
    }
}
