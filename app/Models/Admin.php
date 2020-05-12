<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    public $incrementing = true;

    protected $table = 'admin';

    /**
     * 自動更新created_at, updated_at
     */
    public $timestamps = true;

    /**
     * 黑名單
     */
    protected $guarded = [];
}
