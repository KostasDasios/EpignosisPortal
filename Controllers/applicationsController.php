<?php

require(ROOT . 'Models/Application.php');
require(ROOT . 'Models/Mail.php');

class applicationsController extends Controller
{
    function index()
    {
        $applications = new Application();

        $d['applications'] = $applications->showAllApplications($this->getCurrentUser());
        $this->set($d);
        $this->render("index");
    }

    function create()
    {
        if (isset($_POST["date_from"]))
        {
            $application= new Application();
            $mail = new Mail();

            if ($applications_id = $application->create($_POST["date_from"], $_POST["date_to"], $_POST["reason"], $this->getCurrentUser()))
            {
                $mail->mailtoAdmin($_POST["date_from"], $_POST["date_to"], $_POST["reason"], $this->getCurrentUser(), $applications_id);
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