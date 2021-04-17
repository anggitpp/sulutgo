<?php

use Illuminate\Database\Eloquent\Model;

class KPISetingIndividuObyektif extends Model
{

    protected $table = "pen_sasaran_individu";

    protected $primaryKey = "idIndividu";

    public const CREATED_AT = "createTime";
    public const UPDATED_AT = "updateTime";

    protected $guarded = [];

}
