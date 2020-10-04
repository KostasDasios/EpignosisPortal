<?php
class Application extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    public function create($date_from, $date_to, $reason, $user_id)
    {
        $sql = "INSERT INTO applications (date_from, date_to, reason, status, created_at, updated_at) VALUES (:date_from, :date_to, :reason, :status, :created_at, :updated_at)";

        try
        {
            $req = Database::getConnection()->prepare($sql);
            $req->execute([
                'date_from' => date("Y-m-d H:i:s", strtotime($date_from)),
                'date_to' => date("Y-m-d H:i:s", strtotime($date_to)),
                'reason' => $reason,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        catch (PDOException $e)
        {
           throw new Exception($e->getMessage());
        }

        $applications_id = Database::getConnection()->lastInsertId();
        $this->userApplicationReference($applications_id, $user_id);
        return $applications_id;
    }

    public function userApplicationReference($application_id, $user_id)
    {
        $sql = "INSERT INTO user_applications (user_id, application_id) VALUES (:user_id, :application_id)";

        try
        {
            $req = Database::getConnection()->prepare($sql);
            return $req->execute([
                'user_id' => $user_id,
                'application_id' => $application_id,
            ]);
        }
        catch (PDOException $e)
        {
           throw new Exception($e->getMessage());
        }
    }

    public function showApplication($id)
    {
        $sql = "SELECT * FROM applications WHERE id =" . $id;
        $req = Database::getConnection()->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    public function showAllApplications($id = null)
    {
        if($id){
            $sql  = "SELECT date_from, date_to, application_status.status, created_at ";
            $sql .= "FROM applications ";
            $sql .= "INNER JOIN user_applications ON applications.id = user_applications.application_id ";
            $sql .= "LEFT JOIN application_status ON applications.status = application_status.id ";
            $sql .= "WHERE user_applications.user_id =" . $id;
            $sql .= " ORDER BY created_at DESC";
            $req = Database::getConnection()->prepare($sql);
            $req->execute();
        }else{
            $sql = "SELECT * FROM applications";
        }
        $req = Database::getConnection()->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

    public function edit($id, $date_from, $date_to, $reason)
    {
        $sql = "UPDATE applications SET date_from = :date_from, date_to = :date_to, reason = :reason , updated_at = :updated_at WHERE id = :id";

        $req = Database::getConnection()->prepare($sql);

        return $req->execute([
            'id' => $id,
            'date_from' => date("Y-m-d H:i:s", strtotime($date_from)),
            'date_to' => date("Y-m-d H:i:s", strtotime($date_to)),
            'reason' => $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getStatusLabel($status_id)
    {
        $sql = "SELECT status FROM application_status WHERE id =" . $status_id;
        $req = Database::getConnection()->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    public function editStatus(int $id, int $status): bool
    {
        $sql = "UPDATE applications SET status = :status, updated_at = :updated_at WHERE id = :id";

        $req = Database::getConnection()->prepare($sql);

        return $req->execute([
            'id' => $id,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM applications WHERE id = ?';
        $req = Database::getConnection()->prepare($sql);
        return $req->execute([$id]);
    }
}
?>