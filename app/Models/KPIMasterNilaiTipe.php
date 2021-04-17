<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterNilaiTipe extends Model
{

    protected $table = "kpi_seting_nilai_tipe";

    protected $guarded = [];

    public function detil()
    {
        return $this->hasMany(KPIMasterNilai::class, "tipe_id");
    }

}
