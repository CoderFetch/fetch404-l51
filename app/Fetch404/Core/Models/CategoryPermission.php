<?php namespace Fetch404\Core\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPermission extends Model
{
    //
    protected $table = 'category_permission';
    protected $fillable = ['permission_id', 'category_id', 'role_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
