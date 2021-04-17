<?php

use Illuminate\Database\Eloquent\Model;

class PegawaiPosisi extends Model
{

    protected $table = "emp_phist";

    public const CREATED_AT = "create_date";
    public const UPDATED_AT = "update_date";

    protected $guarded = [];

}
