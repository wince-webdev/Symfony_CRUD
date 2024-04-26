<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('main/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/create', name: 'create-post')]
    public function createPost(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form-> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('message','Insertion avec succès.');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit_post/{id}', name: 'edit-post')]
    public function editPost(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class,$post);
        $form-> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('message','Modifier avec succès.');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete_post/{id}', name: 'delete-post')]
    public function deletePost($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);

        $this->em->remove($post);
        $this->em->flush();
        $this->addFlash('message','Supprimer avec succès.');
        return $this->redirectToRoute('app_main');
    }
        
}

