<?php
use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\User;
use WebLinks\Domain\Link;
use WebLinks\Form\Type\LinkType;
use WebLinks\Form\Type\UserType;

// Home page
$app->get('/', function () use ($app) {
    $links = $app['dao.link']->findAll();
    return $app['twig']->render('index.html.twig', array('links' => $links));
})->bind('home');

// login form
$app->get('/login', function (Request $request) use ($app) {    
    return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
})->bind('login');

// Admin Page
$app->get('/admin', function (Request $request) use ($app) { 
    $links = $app['dao.link']->findAll();
    $users = $app['dao.user']->findAll();  
    return $app['twig']->render('admin.html.twig',array(
        'users' => $users,
        'links' => $links
    ));
})->bind('admin');

//link edit 
$app->match('/admin/link/{id}/edit', function ($id, Request $request) use ($app) {     
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
})->bind('admin_link_edit');

//link delete 
$app->get('/admin/link/{id}/delete/', function ($id,Request $request) use ($app) {
    $app['dao.link']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The link was succesfully deleted.');
    return  $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_link_delete');

//user edit 
$app->match('/admin/user/{id}/edit', function ($id, Request $request) use ($app) { 
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
})->bind('admin_user_edit');

//user delete 
$app->get('/admin/user/{id}/delete', function ($id, Request $request) use ($app) { 
    $user = $app['dao.user']->find($id);
    $app['dao.link']->deleteAllByUser($id);
    $app['dao.user']->deleteUser($id);
    $app['session']->getFlashBag()->add('success', 'The User "'.$user->getUserName() .'" was succesfully deleted.');
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');

// create link form
$app->match('/link/submit', function (Request $request) use ($app) {     
    $link = new Link();     
    $linkForm = $app['form.factory']->create(new LinkType(),$link);
    $linkForm->handleRequest($request);
    
    if($linkForm->isSubmitted() && $linkForm->isValid()){
        $link->setUser($app['user']);
        $app['dao.link']->save($link);
        $app['session']->getFlashBag()->add('success', 'The link was succesfully added.');
    }
    
    return $app['twig']->render('link_form.html.twig',array(
        'title' =>'New link',
        'linkForm'  => $linkForm->createView()
    ));
    
})->bind('createLink');

// Api links list
$app->get('/api/links', function() use($app){
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
})->bind('api_links');

// Api link infos
$app->get('/api/link/{id}', function($id) use($app){ 
   $link = $app['dao.link']->find($id);
   
    $responseData =array(
           'id'        => $link->getId(),
           'title'     => $link->getTitle(),
           'url'       => $link->getUrl(),
           'user_id'   => $link->getUser()->getId()
    );   
    
    return $app->json($responseData); 
})->bind('api_link_info');
