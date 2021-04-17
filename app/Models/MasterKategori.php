<?php

use Illuminate\Database\Eloquent\Model;

class MasterKategori extends Model
{

    protected $table = "mst_category";

    protected $primaryKey = "kodeCategory";

    public const CREATED_AT = "createTime";
    public const UPDATED_AT = "updateTime";

    protected $guarded = [];

    public function master()
    {
        return $this->hasMany(Master::class, "kodeCategory", "kodeCategory");
    }

}
