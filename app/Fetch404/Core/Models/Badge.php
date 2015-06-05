<?php namespace Fetch404\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    //
    protected $table = 'badges';
    protected $fillable = ['name', 'slug', 'description', 'enabled'];

    public function users()
    {
        return $this->belongsToMany('Fetch404\Core\Models\User', 'user_badge', 'badge_id');
    }

    public function criteria()
    {
        return $this->belongsToMany('Fetch404\Core\Models\BadgeCriteria', 'badge_badge_criteria', 'badge_id');
    }
}
