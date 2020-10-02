<?php
require(ROOT . 'Models/User.php');

class Auth extends Model
{
	/* The ID of the logged in account (or NULL if there is no logged in account) */
    private $id;
    
    /* The email of the logged in account (or NULL if there is no logged in account) */
    private $email;

    /* TRUE if the user is authenticated, FALSE otherwise */
    private $authenticated;

    public function __construct()
    {
        $this->id = NULL;
        $this->email = NULL;
        $this->authenticated = FALSE;
    }
    
    public function __destruct()
    {
        
    }

    public function deleteUserSessions($id)
    {
        $sql = 'DELETE FROM user_sessions WHERE user_id = ?';
        $req = Database::getConnection()->prepare($sql);
        return $req->execute([$id]);
    }

    public function login(string $email, string $passwd): bool
	{
		$email = trim($email);
		$passwd = trim($passwd);
		
		if (!User::isEmailValid($email))
		{
			return FALSE;
		}
		
		if (!User::isPasswdValid($passwd))
		{
			return FALSE;
		}
		
		$query = 'SELECT * FROM users WHERE (email = :email)';
		
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
		
		/* If there is a result, we must check if the password matches using password_verify() */
		if (is_array($row))
		{
			if (password_verify($passwd, $row['password']))
			{
				/* Authentication succeeded. Set the class properties (id and email) */
				$this->id = intval($row['id'], 10);
				$this->email = $email;
				$this->authenticated = TRUE;
				
				/* Register the current Sessions on the database */
				$this->registerLoginSession();
				
				return TRUE;
			}
		}
		
		/* If we are here, it means the authentication failed: return FALSE */
		return FALSE;
	}

	/* Saves the current Session ID with the account ID */
	private function registerLoginSession()
	{
		/* Check that a Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* 	Use a REPLACE statement to:
				- insert a new row with the session id, if it doesn't exist, or...
				- update the row having the session id, if it does exist.
			*/
			$query = 'REPLACE INTO user_sessions (session_id, user_id, login_time) VALUES (:sid, :accountId, NOW())';
			$values = array(':sid' => session_id(), ':accountId' => $this->id);
			
			try
			{
				$res = Database::getConnection()->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   throw new Exception('Database query error');
			}
		}
	}

	public function sessionLogin(): bool
	{
		/* Check that the Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* 
				Query template to look for the current session ID on the user_sessions table.
				The query also make sure the Session is not older than 7 days
			*/
			$query = 
			
			'SELECT * FROM user_sessions, users WHERE (user_sessions.session_id = :sid) ' . 
			'AND (user_sessions.login_time >= (NOW() - INTERVAL 7 DAY)) AND (user_sessions.user_id = users.id) ';
			
			$values = array(':sid' => session_id());
			
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
				/* Authentication succeeded. Set the class properties (id and email) and return TRUE*/
				$this->id = intval($row['id'], 10);
				$this->name = $row['email'];
				$this->authenticated = TRUE;
				
				return TRUE;
			}
		}
		
		/* If we are here, the authentication failed */
		return FALSE;
	}

	public function logout()
	{
		/* If there is no logged in user, do nothing */
		if (is_null($this->id))
		{
			return;
		}
		
		/* Reset the account-related properties */
		$this->id = NULL;
		$this->email = NULL;
		$this->authenticated = FALSE;
		
		/* If there is an open Session, remove it from the user_sessions table */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			$query = 'DELETE FROM user_sessions WHERE (session_id = :sid)';
			
			$values = array(':sid' => session_id());
			
			try
			{
				$res = Database::getConnection()->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   throw new Exception('Database query error');
			}
		}
	}

	public function isAuthenticated(): bool
	{
		return $this->authenticated;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	/* Close all account Sessions except for the current one (aka: "logout from other devices") */
	public function closeOtherSessions()
	{
		/* If there is no logged in user, do nothing */
		if (is_null($this->id))
		{
			return;
		}
		
		/* Check that a Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* Delete all account Sessions with session_id different from the current one */
			$query = 'DELETE FROM user_sessions WHERE (session_id != :sid) AND (user_id = :user_id)';
			
			/* Values array for PDO */
			$values = array(':sid' => session_id(), ':user_id' => $this->id);
			
			try
			{
				$res = Database::getConnection()->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   throw new Exception('Database query error');
			}
		}
	}

	public function aclCheck($filename, $class){
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

        if($login){
                
            $user = new User();

            $current_user = $user->showUser($auth->getId());

            if($current_user['type'] == 1){
                if($class !== 'users'){
                    header("Location: " . WEBROOT . "users/index");
                    return false;
                }
            }else{
                if($class !== 'applications'){
                    header("Location: " . WEBROOT . "applications/index");
                    return false;
                }
            }
        }else{
            if($filename !== 'login'){
                header("Location: " . WEBROOT . "auth/login");
                return false;
            }
        }

        return true;
    }
}
?>