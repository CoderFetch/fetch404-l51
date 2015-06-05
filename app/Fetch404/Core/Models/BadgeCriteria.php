<?php namespace Fetch404\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeCriteria extends Model
{
    //
    protected $table = 'badge_criteria';
    protected $fillable = ['user_type', 'trigger_type', 'trigger_value'];

    public function badges()
    {
        return $this->belongsToMany('Fetch404\Core\Models\Badge', 'badge_badge_criteria', 'criteria_id');
    }
}
