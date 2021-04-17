<?php

use Illuminate\Database\Eloquent\Model;

class KPIPegawaiEvaluasiCatatan extends Model
{

    protected $table = "pen_eva_catatan";

    protected $primaryKey = "id_catatan";

    public const CREATED_AT = "create_date";
    public const UPDATED_AT = "update_date";

    protected $guarded = [];

}
