<?php

use Illuminate\Database\Eloquent\Model;

class KPISetingIndividuPrespektifIndikator extends Model
{

    protected $table = "pen_setting_prespektif";

    protected $primaryKey = "idPrespektif";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

}
