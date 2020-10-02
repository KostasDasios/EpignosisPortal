<?php
class User extends Model
{ 
    /* Add a new account to the system and return its ID (the user_id column of the users table) */
    public function addUser(string $first_name, string $last_name, string $email, string $passwd, int $type): int
    {
        /* Trim the strings to remove extra spaces */
        $first_name = trim($first_name);
        $last_name = trim($last_name);
        $email = trim($email);
        $passwd = trim($passwd);
        
        if (!$this->isEmailValid($email))
        {
            throw new Exception('Invalid user email');
        }
        
        if (!$this->isPasswdValid($passwd))
        {
            throw new Exception('Invalid password');
        }
        
        if (!is_null($this->getIdFromEmail($email)))
        {
            throw new Exception('User email already exists');
        }
        
        $query = "INSERT INTO users (first_name, last_name, email, password, type, created_at, updated_at) VALUES (:first_name, :last_name, :email, :passwd, :type, :created_at, :updated_at)";
        
        $hash = password_hash($passwd, PASSWORD_DEFAULT);
        
        $values = array(
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':passwd' => $hash,
            ':type' => $type,
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s')
        );
        
        try
        {
            $res = Database::getConnection()->prepare($query);
            $res->execute($values);
        }
        catch (PDOException $e)
        {
           throw new Exception($e->getMessage());
        }
        
        /* Return the new ID */
        return Database::getConnection()->lastInsertId();
    }

    public function isEmailValid(string $email): bool
    {
        $valid = TRUE;
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid = FALSE;
        }
        
        return $valid;
    }

    public function isPasswdValid(string $passwd): bool
    {
        $valid = TRUE;
        
        /* the length must be between 8 and 16 chars */
        $len = mb_strlen($passwd);
        
        if (($len < 8) || ($len > 16))
        {
            $valid = FALSE;
        }
        
        return $valid;
    }

    /* Returns the account id having $email as email, or NULL if it's not found */
    public function getIdFromEmail(string $email): ?int
    {
        if (!$this->isEmailValid($email))
        {
            throw new Exception('Invalid user email');
        }
        
        $id = NULL;
        
        $query = 'SELECT id FROM users WHERE (email = :email)';
        $values = array(':email' => $email);
        
        try
        {
            $res = Database::getConnection()->prepare($query);
            $res->execute($values);
        }
        catch (PDOException $e)
        {
           throw new Exception('Database query error');
        }
        
        $row = $res->fetch(PDO::FETCH_ASSOC);
        
        if (is_array($row))
        {
            $id = intval($row['id'], 10);
        }
        
        return $id;
    }

    public function showUser($id)
    {
        $sql = "SELECT * FROM users WHERE id =" . $id;
        $req = Database::getConnection()->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    public function showAllUsers()
    {
        $sql = "SELECT * FROM users";
        $req = Database::getConnection()->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM users WHERE id = ?';
        $req = Database::getConnection()->prepare($sql);
        return $req->execute([$id]);
    }

    public function editUser(int $id, string $first_name, string $last_name, string $email, string $passwd, int $type)
    {
        /* Trim the strings to remove extra spaces */
        $first_name = trim($first_name);
        $last_name = trim($last_name);
        $email = trim($email);
        $passwd = trim($passwd);
        
        if (!$this->isEmailValid($email))
        {
            throw new Exception('Invalid user email');
        }
        
        if (!$this->isPasswdValid($passwd))
        {
            throw new Exception('Invalid password');
        }
        
        /* Check if an account having the same name already exists (except for this one). */
        $idFromEmail = $this->getIdFromEmail($email);
        
        if (!is_null($idFromEmail) && ($idFromEmail != $id))
        {
            throw new Exception('User email already exists');
        }

        $query = 'UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, password = :passwd, type = :type, updated_at = :updated_at WHERE id = :id';
        
        $hash = password_hash($passwd, PASSWORD_DEFAULT);
        
        $values = array(
            ':id' => $id,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':passwd' => $hash,
            ':type' => $type,
            ':updated_at' => date('Y-m-d H:i:s')
        );
        
        try
        {
            $res = Database::getConnection()->prepare($query);
            $res->execute($values);            
        }
        catch (PDOException $e)
        {
           throw new Exception($e->getMessage());
        }

        return $res->rowCount();
    }
}
?>