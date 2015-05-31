<?php namespace Fetch404\Core\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelPermission extends Model
{
    //
    protected $table = 'channel_permission';
    protected $fillable = ['permission_id', 'channel_id', 'role_id'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
