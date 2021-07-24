<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaksin extends Model
{
    protected $fillable = ['id_vaksin', 'tgl_vaksin', 'id_user', 'lokasi', 'foto', 'status', 'id_kategori','telp'];
    protected $table = "vaksin";
    protected $primaryKey = 'id_vaksin';

    public function kategori() {
        return $this->belongsTo('App\Models\Kategori','id_kategori','id_kategori');
    }

    public function tanggapan() {
        return $this->belongsTo('App\Models\Tanggapan','id_vaksin','id_vaksin');
    }
    public function user() {
        return $this->belongsTo('App\Models\User','id_user','id');
    }
}
