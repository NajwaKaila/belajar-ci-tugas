<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriProductModel extends Model
{
    protected $table = 'product_categories'; 
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama',];
    public $useTimestamps = false;
}
