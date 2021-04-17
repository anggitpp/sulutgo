<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterNilaiHasil extends Model
{

    protected $table = "pen_setting_konversi";

    protected $primaryKey = "idKonversi";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

}
