<?php

use Illuminate\Database\Eloquent\Model;

class KPIPegawaiEvaluasiKesepakatan extends Model
{

    protected $table = "pen_eva_tindakan";

    protected $primaryKey = "id_tindakan";

    public const CREATED_AT = "create_date";
    public const UPDATED_AT = "update_date";

    protected $guarded = [];

}
