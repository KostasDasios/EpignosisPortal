<?php
class Mail extends Model
{
    public function sendMail(string $to_email, string $subject, string $body, string $headers): bool
    {
        try
        {
            mail($to_email, $subject, $body, $headers);            
        }
        catch (Exception $e)
        {
           throw new Exception('Email sending failed...');
           die;
        }

        return true;
    }

    public function mailtoAdmin(string $vacation_start, string $vacation_end, string $reason, int $user_id, int $application_id): bool
    {
        $users = new User();
        $admin_mail = $users->getAdminEmail();
        $user = $users->showUser($user_id);
        $vacation_start = date("d-m-Y", strtotime($vacation_start));
        $vacation_end = date("d-m-Y", strtotime($vacation_end));

        $to_email = $admin_mail;
        $subject = "Vacation Request from ". $user['first_name'].' '.$user['last_name'];
        $body = "Dear supervisor, employee ". $user['first_name'].' '.$user['last_name']." requested for some time off, starting on <br><br>";
        $body .= "$vacation_start and ending on $vacation_end, stating the reason: <br>";
        $body .= "$reason <br><br>";
        $body .= "Click on one of the below links to approve or reject the application: <br>";
        $body .= '<a href="http://127.0.0.1/EpignosisPortal/users/application_approve/'.$application_id.'">Approve</a> - <a href="http://127.0.0.1/EpignosisPortal/users/application_reject/'.$application_id.'">Reject</a>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Epignosis Portal" . "\r\n";

        return $this->sendMail($to_email, $subject, $body, $headers);
    }

    public function mailtoEmployee(string $status, int $application_id): bool
    {
        $users = new User();
        $application = new Application();
        $user = $users->showUser($users->getUserIdByApplicationId($application_id));
        $submitted_application = $application->showApplication($application_id);
        $submission_date = date("d-m-Y", strtotime($submitted_application['created_at']));

        $to_email = $user['email'];
        $subject = "Vacation Request from " . $user['first_name'].' '.$user['last_name'];
        $body = "Dear employee, your supervisor has $status your application <br>";
        $body .= "submitted on $submission_date.";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Epignosis Portal" . "\r\n";

        return $this->sendMail($to_email, $subject, $body, $headers);
    }
}
?>