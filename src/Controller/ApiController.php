<?php
namespace WebLinks\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    /**
     * API links controller.
     *
     * @param Application $app Silex application
     *
     * @return All links in JSON format
     */
    public function linksAction(Application $app)
    {
        $links = $app['dao.link']->findAll();
        $responseData = array();
    
        foreach($links as $link){
            $responseData[] = [
                'id'        => $link->getId(),
                'title'     => $link->getTitle(),
                'url'       => $link->getUrl(),
                'user_id'   => $link->getUser()->getId()
            ];
        }
       return $app->json($responseData);    
    }
   
    /**
     * API link info controller.
     *
     * @param Integer $id Link Id
     * @param Application $app Silex application
     *
     * @return all infos for a link in JSON format
     */
    public function linkInfosAction($id, Application $app)
    {
        $link = $app['dao.link']->find($id);   
        $responseData =array(
               'id'        => $link->getId(),
               'title'     => $link->getTitle(),
               'url'       => $link->getUrl(),
               'user_id'   => $link->getUser()->getId()
        ); 
        return $app->json($responseData); 
    }
    
}
