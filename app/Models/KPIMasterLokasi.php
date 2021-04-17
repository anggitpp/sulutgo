<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterLokasi extends Model
{

    protected $table = "pen_tipe";

    protected $primaryKey = "kodeTipe";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

    public function cabang()
    {
        return $this->hasMany(KPIMasterLokasiCabang::class, "kodeTipe");
    }

}
