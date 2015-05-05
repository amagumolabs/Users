<?php namespace Amagumolabs\Modules\Users\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password','status','role_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    protected $attributes = array(
        'status' => 0
    );
    public function role(){
        return $this->hasOne('App\Role',"id","role_id");
    }
    public function toArray()
    {
        $rolePermissionAction=$this->role;
        return parent::toArray();
    }
    public function isRole($code)
    {
        if($this->role)
        {
            return $this->role->code==$code;
        }
        return false;
    }
    public function isLocked()
    {
        return !$this->status;
    }
    public function checkPermission($key)
    {
        if(!$this->role_id)
            return;
        $result=false;
        $pieces=explode(".",$key);
        $permissionName=$pieces[0];
        $actionName=$pieces[1];
        $rolePermissionAction=$this->role->rolePermissionAction;
        for($i=0;$i<count($rolePermissionAction);$i++)
        {
            if($rolePermissionAction[$i]['permission']['name']==$permissionName&&$rolePermissionAction[$i]['action']['name']==$actionName && $rolePermissionAction[$i]['value']==1)
            {
                $result=true;
                break;
            }
        }
        return $result;
    }

}
