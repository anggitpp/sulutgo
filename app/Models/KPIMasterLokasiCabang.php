<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterLokasiCabang extends Model
{

    protected $table = "pen_setting_kode";

    protected $primaryKey = "idKode";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

}
