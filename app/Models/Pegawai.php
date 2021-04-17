<?php

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{

    protected $table = "emp";

    public const CREATED_AT = "create_date";
    public const UPDATED_AT = "update_date";

    protected $guarded = [];

    public function posisi()
    {
        return $this->hasOne(PegawaiPosisi::class, "parent_id");
    }

    // KPI
    public function kpiSetingObyektif()
    {
        return $this->hasMany(KPISetingIndividuObyektif::class, "idPegawai");
    }

    public function kpiRealisasiHasil()
    {
        return $this->hasMany(KPIRealisasiIndividu::class, "id_pegawai");
    }

    public function kpiRealisasiHasilDetil()
    {
        return $this->hasMany(KPIRealisasiIndividuDetil::class, "id_pegawai");
    }

    // Evaluasi
    public function kpiEvaluasiKesepakatan()
    {
        return $this->hasMany(KPIPegawaiEvaluasiKesepakatan::class, "id_pegawai");
    }

    public function kpiEvaluasiCatatan()
    {
        return $this->hasMany(KPIPegawaiEvaluasiCatatan::class, "id_pegawai");
    }

}
