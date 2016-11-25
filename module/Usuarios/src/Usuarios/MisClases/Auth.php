<?php

namespace Usuarios\MisClases;
require "Meli.php";

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class Auth{
	
	private $moduleConfing;
	
	public function __construct($config){
		$this->config = $config;
	}
	
	public function setModuleConfing($param){
		$this->moduleConfing = $param;
	}
	
    public function autenticarMl($code = null){

        if(!isset($_GET['code'])){
            $_GET['code'] = "";
        }

		if(!$_GET['code']){
			if($code){
				$_GET['code'] = $code;
			}
		}

        if(!isset($_SESSION['access_token'])){
            $_SESSION['access_token'] = "";
        }

        if(!isset($_SESSION['refresh_token'])){
            $_SESSION['refresh_token'] = "";
        }

		$mlData = $this->config;
		
        $meli = new Meli($mlData["appId"], $mlData["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);

        if($_GET['code'] || $_SESSION['access_token']) {

            // If code exist and session is empty
            if($_GET['code'] && !($_SESSION['access_token'])) {
                // If the code was in get parameter we authorize
                $user = $meli->authorize($_GET['code'], $mlData["authUrl"]);
                // Now we create the sessions with the authenticated user
                $_SESSION['access_token'] = $user['body']->access_token;
                $_SESSION['expires_in'] = time() + $user['body']->expires_in;
                $_SESSION['refresh_token'] = $user['body']->refresh_token;
                //sendPost($_SESSION);
				
            } else {

                // We can check if the access token in invalid checking the time
                if(isset($_SESSION['expires_in'])){
                    if($_SESSION['expires_in'] < time() ) {
                        try {
                            // Make the refresh proccess
                            $refresh = $meli->refreshAccessToken();

                            // Now we create the sessions with the new parameters
                            $_SESSION['access_token'] = $refresh['body']->access_token;
                            $_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
                            $_SESSION['refresh_token'] = $refresh['body']->refresh_token;
                        } catch (Exception $e) {
                            echo "Exception: ",  $e->getMessage(), "\n";
                        }
                    }
					
                }else{

                    $refresh = $meli->refreshAccessToken();

                    // Now we create the sessions with the new parameters
                    $_SESSION['access_token'] = $refresh['body']->access_token;
                    $_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
                    $_SESSION['refresh_token'] = $refresh['body']->refresh_token;
					

                }
                //sendPost($_SESSION);
            }

            return array("success" => true);
        } else {
            $newURL = $meli->getAuthUrl($mlData["authUrl"], Meli::$AUTH_URL['MLU']);

            if(strpos($newURL,"https://auth.mercadolibre.com.uy/") !== false){
                $newURL = substr($newURL,strlen("https://auth.mercadolibre.com.uy/"),strlen($newURL));
            }

            if(strpos($newURL,"http://auth.mercadolibre.com.uy/") !== false){
                $newURL = substr($newURL,strlen("http://auth.mercadolibre.com.uy/"),strlen($newURL));
            }

            return array(
                "success" => false,
                "callback" => $newURL
            );
        }
    }
}
