<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    public $cache_expire_in_minutes = 100;


    public function getAllCached()
    {
        return \Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function () {
            return $this->all();
        });
    }
}
