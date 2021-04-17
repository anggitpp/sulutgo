<?php

use Illuminate\Database\Eloquent\Model;

class KPIMasterAspek extends Model
{

    protected $table = "pen_setting_aspek";

    protected $primaryKey = "idAspek";

    public const CREATED_AT = "createDate";
    public const UPDATED_AT = "updateDate";

    protected $guarded = [];

}
