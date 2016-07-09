<?php
namespace WebLinks\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use WebLinks\Domain\User;


class UserDAO extends DAO implements UserProviderInterface
{
     /**
     * Returns an user matching the supplied id.
     *
     * @param integer $id
     *
     * @return \WebLinks\Domain\User|throws an exception if no matching user is found
     */
    public function find($id)
    {
        $sql = "SELECT *
                FROM t_user
                WHERE user_id = ?";        
        $row = $this->getDb()->fetchAssoc($sql,array($id));
        
        if($row){
            return $this->buildDomainObject($row);
        }else{
            throw new \Exception("No user matched on id : ".$id);
        }
    }
    
    /**
     * Return a list of all users, sorted by role (admin first).
     *
     * @return array A list of all users.
     */
    public function findAll()
    {
        $sql = "SELECT *
               FROM t_user
               ORDER BY user_role";
        $response = $this->getDb()->fetchAll($sql);
        
        $dataUsers=array();        
        foreach($response as $row){
            $userId = $row['user_id'];
            $dataUsers[$userId]= $this->buildDomainObject($row);
        }
        return $dataUsers;
    }
    
    /**
     * Delete user by using an id
     *     
     */
    public function deleteUser($id)
    {
       $this->getDb()->delete('t_user',array('user_id'=>$id)); 
    }
    
    /**
     * Save user in Db
     *  
     * @param \WebLinks\Domain\User
     */
    public function save(User $user)
    {
        $dataUser = [
            'user_name'     =>$user->getUserName(),
            'user_password' =>$user->getPassword(),
            'user_salt'     =>$user->getSalt(),
            'user_role'     =>$user->getRole()
        ];
        
        if($user->getId()){
            $this->getDb()->update('t_user',$dataUser,array('user_id'=>$user->getId()));
        }else{
            $this->getDb()->insert('t_user', $dataUser);
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }
    
    /**
     * Creates an User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return \WebLinks\Domain\User
     */
    protected function buildDomainObject($row)
    {
        $user = new User();
        $user->setId($row['user_id']);
        $user->setUserName($row['user_name']);
        $user->setPassword($row['user_password']);
        $user->setSalt($row['user_salt']);
        $user->setRole($row['user_role']);
        return $user;
    }    
    
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "select * from t_user where user_name=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {            
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'WebLinks\Domain\User' === $class;
    }
}

