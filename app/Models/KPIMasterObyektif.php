<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterObyektif extends Model
{

    protected $table = "pen_sasaran_obyektif";

    protected $primaryKey = "idSasaran";

    public const CREATED_AT = "createTime";
    public const UPDATED_AT = "updateTime";

    protected $guarded = [];

    public function rating()
    {
        return $this->hasOne(KPIMasterNilaiTipe::class, "id", "tipe");
    }

}
