<?php
namespace WebLinks\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\Link;
use WebLinks\Domain\User;
use WebLinks\Form\Type\LinkType;
use WebLinks\Form\Type\UserType;

class AdminController
{
    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app)
    {
        $links = $app['dao.link']->findAll();
        $users = $app['dao.user']->findAll();  
        return $app['twig']->render('admin.html.twig',array(
            'users' => $users,
            'links' => $links
        ));
    }
    
    /**
     * Edit link controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function linkEditAction($id,Request $request, Application $app)
    {
        $link = $app['dao.link']->find($id);
        $linkForm = $app['form.factory']->create(new linkType(), $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'The link was succesfully updated.');
        }    
         return $app['twig']->render('link_form.html.twig', array(
            'title' => 'Edit link',
            'linkForm' => $linkForm->createView()));
    }
    
    /**
     * Delete link controller.
     *
     * @param integer $id Link id
     * @param Application $app Silex application
     */
    public function linkDeleteAction($id, Application $app)
    {
        $app['dao.link']->delete($id);
        $app['session']->getFlashBag()->add('success', 'The link was succesfully deleted.');
        return  $app->redirect($app['url_generator']->generate('admin'));
    }
    
    /**
     * Edit User controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function userEditAction($id, Request $request, Application $app)
    {
        $user = $app['dao.user']->find($id);
        $userForm = $app['form.factory']->create(new UserType(), $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user "' .$user->getUserName(). '" was succesfully updated.');
            }  

             return $app['twig']->render('user_form.html.twig', array(
                'title' => 'Edit User',
                'userForm' => $userForm->createView()));        
    }
    
    /**
     * Delete user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */
    public function userDeleteAction($id, Application $app)
    {
        $user = $app['dao.user']->find($id);
        $app['dao.link']->deleteAllByUser($id);
        $app['dao.user']->deleteUser($id);
        $app['session']->getFlashBag()->add('success', 'The User "'.$user->getUserName() .'" was succesfully deleted.');
        return $app->redirect($app['url_generator']->generate('admin'));
    }
    
}
