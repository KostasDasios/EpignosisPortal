<?php
class applicationsController extends Controller
{
    function index()
    {
        require(ROOT . 'Models/Application.php');

        $applications = new Application();

        $d['applications'] = $applications->showAllApplications($this->getCurrentUser());
        $this->set($d);
        $this->render("index");
    }

    function create()
    {
        if (isset($_POST["date_from"]))
        {
            require(ROOT . 'Models/Application.php');

            $application= new Application();

            if ($application->create($_POST["date_from"], $_POST["date_to"], $_POST["reason"], $this->getCurrentUser()))
            {
                header("Location: " . WEBROOT . "applications/index");
            }
        }

        $this->render("create");
    }

    function getCurrentUser()
    {
        $auth = new Auth();
        $login = $auth->sessionLogin();
        return $auth->getId();
    }
}
?>