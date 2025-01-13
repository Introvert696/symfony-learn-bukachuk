<?php

namespace App\Controller;

use App\Entity\Blog;

use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'blog_default')]
    public function index(EntityManagerInterface $em,BlogRepository $blogRepository): Response
    {
        $blog = $blogRepository->findOneBy(['id'=>2]);
        $em->refresh($blog);

        dd($blog);
        $blog = (new Blog())
            ->setTitle('title')
            ->setDescription('Desctr')
            ->setText('text')
        ;
        $em->persist($blog);
        $em->flush();

        return $this->render('index.html.twig', []);
    }
}
