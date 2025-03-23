<?php

namespace src\Models;

use Cassandra\Date;
use core\auth\GenerateToken;
use core\Orm\ORM;
use src\Models\Models;

/**
 *
 */
class userEntity implements Models
{
    public int $id;
    /**
     * @var string
     */
    public string $username;
    /**
     * @var string
     */
    public string $userEmail;
    /**
     * @var string
     */
    public string $userPassword;
    /**
     * @var int
     */
    public int $userIdRole;
    /**
     * @var string
     */
    public string $userStatus;

    private ORM $orm;

    /**
     * @param int $userId
     */
    public function __construct(int $userId=0,array $arrayUser=[] )
    {
        $this->orm = new ORM();
        
        if ($userId > 0) {
            $this->id = $userId;
            $result = self::recoveryById($userId,$this->orm);
            $this->username = $result[0]['username'];
            $this->userEmail = $result[0]['userEmail'];
            $this->userPassword = $result[0]['userPassword'];
            $this->userIdRole = $result[0]['userIdRole'];
            $this->userStatus = $result[0]['userStatus'];

        }else{
           $this->username = $arrayUser['username'];
           $this->userEmail = $arrayUser['userEmail'];
           $this->userPassword = $arrayUser['userPassword'];
            if(empty($arrayUser['userIdRole'])){
                $this->userIdRole = 1;
            }else{
                $this->userIdRole = $arrayUser['userIdRole'];
            }
            if(empty($arrayUser['userStatus'])){
                $this->userStatus = "en attente";
            }else{
                $this->userStatus = $arrayUser['userStatus'];
            }

        }

    }


    /**
     * @inheritDoc
     */
    public function save(): array
    {
          // Assuming ORM\Database is the library used
            if (empty($this->username) || empty($this->userEmail) || empty($this->userPassword)) {
                throw new Exception("Mandatory fields are missing.");
            }

           $this->useriD= $this->orm->insertAndGetId('userentity',[
                'username' => $this->username,
                'userEmail' => $this->userEmail,
                'userPassword' => password_hash($this->userPassword, PASSWORD_BCRYPT),
                'userIdRole' => $this->userIdRole,
                'userStatus' => $this->userStatus
            ]);

           return ['id'=>$this->useriD] ;
    }

    public static function authUser($email, $password){
        $ormAUTH=new ORM();
        $user = $ormAUTH->select('userentity', ['*'], ['userEmail' => $email]);
        if(password_verify($password,$user[0]['userPassword'])){
           $token = GenerateToken::generateToken();
           $tokenExpiration = GenerateToken::generateExpiry();
           $auth = new authEntity($token,$user[0]['id'],$tokenExpiration);
           if($auth->ifExist($user[0]['id'])){
               $auth->updateById($user[0]['id']);
           }else{
               $auth->save();
           }
        }
        return $auth;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        // TODO: Implement deleteById() method.
    }

    /**
     * @inheritDoc
     * on ne met pas a jour le token et le tokenExpirat
     */
    public function updateById($id)
    {
        // TODO: Implement updateById() method.
        $data=[
            'username' => $this->username,
            'userEmail' => $this->userEmail,
            'userPassword' => password_hash($this->userPassword, PASSWORD_BCRYPT),
            'userIdRole' => $this->userIdRole,
            'userStatus' => $this->userStatus,
        ];
        $this->orm->update('userentity', $data, ['id' => $id]);
        return $this;
    }

    public function updateToken(){
        try{
            $this->orm->update('userentity',
                [
                    'username' => $this->username,
                    'userEmail' => $this->userEmail,
                    'userPassword'=>password_hash($this->userPassword, PASSWORD_BCRYPT),
                    'userRole' => $this->userIdRole,
                    'userStatus' => $this->userStatus,
                ],
                ['id' => $this->id]
            );
            $message = 'Token updated';
        }catch (\Exception $e){
            $message = $e->getMessage();
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function ifExist($id)
    {
        $user = $this->orm->select('userentity', ['userEmail'], ['id' => $id]);
        if($user != null){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    static function recoveryById($id,$orm)
    {
       return $orm->select('userentity', ['*'], ['id' => $id]);
    }



}