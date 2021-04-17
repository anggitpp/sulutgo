<?php

use Illuminate\Database\Eloquent\Model;

class KPIPegawai extends Model
{

    protected $table = "pen_pegawai";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, "id", "idPegawai");
    }

}
