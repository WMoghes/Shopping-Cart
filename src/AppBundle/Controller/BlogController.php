<?php
/**
 * Created by PhpStorm.
 * User: Family
 * Date: 5/3/2017
 * Time: 11:09 AM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function listAction()
    {
        return $this->render('blog/blog.html.twig', []);
    }

    /**
     * @Route("show-details/{slug}", name="show_details")
     */
    public function showAction($slug) {
        return $this->render('blog/blog.html.twig', [
            'slug'      => $slug
        ]);
    }
}