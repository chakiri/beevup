<?php

namespace App\Controller;


use App\Repository\EmailContentRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class EmailContentController extends AbstractController
{
    /**
     * @Route("/emailContent/{id}", name="get_email_content")
     */
    public function getContent(Request $request, EntityManagerInterface $manager, $id, EmailContentRepository $emailContentRepository)
    {
        if ($request->isXmlHttpRequest()) {
            $content = $emailContentRepository->findOneBy(['id' => $id])->getContent();
            return new Response(
                $content,
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }


    }
}

