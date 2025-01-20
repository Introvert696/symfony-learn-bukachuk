<?php

namespace App\Controller;


use App\Entity\Blog;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    #[Route('/blog/{id}',name:'blog_view')]
public function index(Blog $blog): Response
    {
        return $this->render('default/blog.html.twig',['blog' => $blog]);
    }
}
