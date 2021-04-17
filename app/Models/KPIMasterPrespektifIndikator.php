<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterPrespektifIndikator extends Model
{

    protected $table = "pen_setting_prespektif_indikator";

    // multipe key
//    protected $primaryKey = "idPrespektif";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

}
