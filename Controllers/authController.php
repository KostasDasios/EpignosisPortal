<?php
class authController extends Controller
{
    function login()
    {
        $login = FALSE;

        $auth = new Auth();

        /* check if already logged in */
        try
        {
            $login = $auth->sessionLogin();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            die();
        }

        /* check after login form submit */
        if (isset($_POST["email"]))
        {    
            try
            {
                $login = $auth->login($_POST["email"], $_POST["password"]);
                $auth->closeOtherSessions();
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
                die();
            }                      
        }

        /* check if logged in either way */
        if($login){
                
            $user = new User();

            $current_user = $user->showUser($auth->getId());
            if($current_user['type'] == 1){
                header("Location: " . WEBROOT . "users/index");
            }else{
                header("Location: " . WEBROOT . "applications/index");
            }
        }

        $this->render("login");
    }

    function logout()
    {
        $auth = new Auth();

        try
        {
            $login = $auth->sessionLogin();
            if($login){
                $auth->logout();
                $login = $auth->sessionLogin();
            }
            
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            die();
        }

        header("Location: " . WEBROOT . "auth/login");
    }
}
?>