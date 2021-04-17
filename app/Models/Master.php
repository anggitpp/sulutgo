<?php

use Illuminate\Database\Eloquent\Model;

class Master extends Model
{

    protected $table = "mst_data";

    protected $primaryKey = "kodeData";

    public const CREATED_AT = "createTime";
    public const UPDATED_AT = "updateTime";

    protected $guarded = [];

}
