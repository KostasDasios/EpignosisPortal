<?php
class usersController extends Controller
{
    function index()
    {
        $users = new User();

        $d['users'] = $users->showAllUsers();
        $this->set($d);
        $this->render("index");
    }

    function create()
    {
        if (isset($_POST["email"]))
        {
            $user = new User();

            try
            {
                $d['newId'] = $user->addUser($_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["password"], $_POST["type"]);
                $this->set($d);
                if ($d['newId'])
                {
                    header("Location: " . WEBROOT . "users/index");
                }
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
                die();
            }            
        }
        $this->render("create");
    }

    function edit($id)
    {
        $user = new User();

        $d["user"] = $user->showUser($id);

        if (isset($_POST["email"]))
        {            
            if ($user->editUser($id, $_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["password"], $_POST["type"]))
            {
                header("Location: " . WEBROOT . "users/index");
            }
        }
        $this->set($d);
        $this->render("edit");
    }

    function delete($id)
    {
        $user = new User();
        $auth = new Auth();

        if($auth->aclCheck('delete', 'users')){

            if ($user->delete($id))
            {                
                $auth->deleteUserSessions($id);

                header("Location: " . WEBROOT . "users/index");
            }
        }

    }
}
?>