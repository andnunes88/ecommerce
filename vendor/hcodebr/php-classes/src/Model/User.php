<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model{

    const SESSION = "User";

    public static function checkLogin(){
        
        if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;
		} else {
			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {
				return true;
			} else if ($inadmin === false) {
				return true;
			} else {
				return false;
			}
        }
        
    }

    public static function login($login, $password){

        
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin =:LOGIN", array(
            ":LOGIN"=>$login
        ));

        if(count($results) === 0){
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        }

        $data = $results[0];

        if (password_verify($password, $data["despassword"]) === true){
            
            $user = new User();

            $user->setData($data);
            
            $_SESSION[User::SESSION] = $user->getValues();

			return $user;

        }else{
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        }

    }

    public static function verifyLogin($inadmin = true){

        if(!User::checkLogin($inadmin)){

            if($inadmin){
                header("Location: /admin/login");
            }else{
                header("Location: /login");
            }

            exit;

        }        
       

    }


    public static function logout(){

        $_SESSION[User::SESSION] = NULL;

    }

}

?>