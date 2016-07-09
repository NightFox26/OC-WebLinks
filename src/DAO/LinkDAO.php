<?php

namespace WebLinks\DAO;

use WebLinks\Domain\Link;
use WebLinks\DAO\UserDAO;

class LinkDAO extends DAO 
{
    
    public function find($id)
    {
        $sql = "SELECT *
                FROM t_link
                WHERE link_id = ?"; 
        $row = $this->getDb()->fetchAssoc($sql, array($id));
        
        if($row){
            return $this->buildDomainObject($row);  
        }else{
            throw new \Exception ('No link matched the id : '.$id);
        }
    }    
    
    /**
     * Returns a list of all links, sorted by id.
     *
     * @return array A list of all links.
     */
    public function findAll() {
        $sql = "select * from t_link order by link_id desc";
        $result = $this->getDb()->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['link_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    /**
     * Creates an Link object based on a DB row.
     *
     * @param array $row The DB row containing Link data.
     * @return \WebLinks\Domain\Link
     */
    protected function buildDomainObject($row) {
        $userDao = new UserDao($this->getDb());
        $link = new Link();
        $link->setId($row['link_id']);
        $link->setUrl($row['link_url']);
        $link->setTitle($row['link_title']);
        $link->setUser($userDao->find($row['user_id']));
        
        return $link;
    }
    
    public function delete($id)
    {
        $this->getDb()->delete('t_link',array('link_id'=>$id));
    }
    
    public function deleteAllByUser($id)
    {
        $this->getDb()->delete('t_link', array('user_id'=>$id));
    }
    
    public function save(Link $link)
    {
        $linkData = array(
            'link_title'=>$link->getTitle(),
            'link_url'=>$link->getUrl(),
            'user_id'=>$link->getUser()->getId(),
        );
        if($link->getId()){
            $this->getDb()->update('t_link',$linkData,array('link_id'=>$link->getId()));
        }else{
            $this->getDb()->insert('t_link',$linkData);
            $id = $this->getDb()->lastInsertId();
            $link->setId($id);
        }
    }
}
