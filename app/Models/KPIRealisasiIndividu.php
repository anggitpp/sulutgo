<?php

use Illuminate\Database\Eloquent\Model;

class KPIRealisasiIndividu extends Model
{

    protected $table = "pen_hasil";

    protected $primaryKey = "id_realisasi";

    public const CREATED_AT = "create_date";
    public const UPDATED_AT = "update_date";

    protected $guarded = [];

}
