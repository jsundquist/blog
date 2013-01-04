<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$nav = array(
    //array('title'=>'Blog','link'=>'/'),
    array('title'=>'Blog','link'=>'/'),
    array('title'=>'About','link'=>'/about'),
    array('title'=>'Contact','link'=>'/contact'),
//    array('title'=>'Resume','link'=>'/resume')
);

$app = new Silex\Application();

// debugging mode
//$app['debug'] = true;

require_once __DIR__ . '/database.php';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'         =>  __DIR__ . '/views',
));

/*$app->get('/', function(Silex\Application $app) use ($nav){

    $breadcrumbs = array('' => 'Blog');

    return $app['twig']->render('home.html.twig', array(
        'active'        => 'Blog',
        'nav'           => $nav,
        'breadcrumbs'   => $breadcrumbs,
        'previousPosts'      => previousPosts($app)
    ));
});*/

// Main blog page
$app->get('/', function(Silex\Application $app) use ($nav){
    $sql = "SELECT * FROM posts where post_status = 'publish' order by post_date desc limit 0, 5";
    $posts = $app['dbs']['mysql_read']->fetchAll($sql);

    $breadcrumbs = array('' => 'Blog');
 
    return $app['twig']->render('blog_list.html.twig',array(
        'posts' => $posts,
        'nav' => $nav,
        'active' => 'Blog',
        'previousPosts' => previousPosts($app),
        'breadcrumbs' => $breadcrumbs
    ));
});

// blog article
$app->get('/blog/view/{id}', function(Silex\Application $app, $id) use ($nav){
    $sql = 'SELECT * FROM posts where post_name = ?';

    $post = $app['db']->fetchAssoc($sql, array($id));

    $breadcrumbs = array('/' => 'Blog', '' => $post['post_title']);
    return $app['twig']->render('blog_view.html.twig', array(
        'nav' => $nav,
        'active' => 'Blog',
        'post' => $post,
        'previousPosts'=> previousPosts($app),
        'breadcrumbs' => $breadcrumbs
    ));
});

// about me
$app->get('/about', function(Silex\Application $app) use ($nav){
    $breadcrumbs = array('/' => 'Blog', '' => 'About');

    return $app['twig']->render('about.html.twig', array(
        'nav' => $nav,
        'breadcrumbs' => $breadcrumbs,
        'active' => 'About',
        'previousPosts' => previousPosts($app)
    ));
});

// my resume
$app->get('/resume', function(Silex\Application $app) use ($nav){
    return $app['twig']->render('resume.html.twig', array(
        'nav' => $nav,
        'active' => 'Resume',
        'previousPosts' => previousPosts($app)
    ));
});

// get in contact with me
$app->get('/contact', function(Silex\Application $app) use ($nav){
    $breadcrumbs = array('/' => 'Blog', '' => 'Contact');

    return $app['twig']->render('contact.html.twig', array(
        'breadcrumbs' => $breadcrumbs,
        'nav' => $nav,
        'active' => 'Contact',
        'previousPosts' => previousPosts($app)
    ));
});


$app->error(function (\Exception $e, $code) use ($app) {
    return new Response('And we will be right back after a moment from our sponsor');
    //return new Response($app['twig']->render('error.html.twig'));
});

$app->run();

function previousPosts (Silex\Application $app){
    $sql = "SELECT post_title, post_name FROM posts where post_status = 'publish' ORDER BY post_date DESC LIMIT 0, 5";
    return $app['db']->fetchAll($sql);
};
