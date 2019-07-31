<?php
/**
 * Class User_account_m
 * @property $username
 * @property $password
 * @property $role
 * @property $reset_token
 */
class User_account_m extends MY_Model
{
    protected $table = "user_accounts";
    protected $primaryKey = "username";

    const ROLE_SUPERADMIN = 1;
    const ROLE_ADMIN = 2;

    public static $listRole = [
        self::ROLE_SUPERADMIN => 'Superadmin',
        self::ROLE_ADMIN => 'Admin',
    ];

    public static function verify($username,$password){
        $user = self::findOne(['username'=>$username]);
        if($user){
            return password_verify($password,$user->password);
        }
        return false;
    }

    public function findWithBiodata($username){
        return $this->find()->where('username',$username)->join('members m','m.username_account = username')->get()->row_array();
    }

}