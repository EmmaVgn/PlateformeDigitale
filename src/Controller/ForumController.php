<?php

namespace App\Controller;

use App\Entity\ForumTopic;
use App\Entity\ForumMessage;
use App\Repository\ForumTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserFormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

final class ForumController extends AbstractController{
    #[Route('/forum', name: 'app_forum')]
    public function index(
        Request $request,
        ForumTopicRepository $forumTopicRepository,
        UserFormationRepository $userFormationRepo,
        PaginatorInterface $paginator
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException("Veuillez vous connecter pour accéder au forum.");
        }
    
        $inscriptionsValidees = $userFormationRepo->findBy(['user' => $user, 'isValidated' => true]);
    
        if (count($inscriptionsValidees) === 0) {
            throw $this->createAccessDeniedException("Vous devez être inscrit à une formation pour accéder au forum.");
        }
    
        $searchTerm = $request->query->get('q');
    
        $queryBuilder = $forumTopicRepository->createQueryBuilder('t');
    
        if ($searchTerm) {
            $queryBuilder
                ->where('t.title LIKE :search OR t.content LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }
    
        $queryBuilder->orderBy('t.createdAt', 'DESC');
    
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6 // sujets par page
        );
    
        return $this->render('forum/index.html.twig', [
            'topics' => $pagination
        ]);
    }
    
    
    #[Route('/forum/new', name: 'app_forum_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Veuillez vous connecter.");
        }

        $topic = new ForumTopic();
        $form = $this->createFormBuilder($topic)
            ->add('title', TextType::class, ['label' => 'Titre du sujet'])
            ->add('content', TextareaType::class, ['label' => 'Message'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setAuthor($user);
            $topic->setCreatedAt(new \DateTimeImmutable());
            $topic->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($topic);
            $em->flush();

            return $this->redirectToRoute('app_forum');
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/forum/{id}', name: 'app_forum_show')]
    public function show(ForumTopic $topic, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Veuillez vous connecter.");
        }
    
        $message = new ForumMessage();
        $form = $this->createFormBuilder($message)
            ->add('content', TextareaType::class, ['label' => 'Votre réponse'])
          
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAuthor($user);
            $message->setTopic($topic);
            $message->setCreatedAt(new \DateTimeImmutable());
            $em->persist($message);
            $em->flush();
    
            return $this->redirectToRoute('app_forum_show', ['id' => $topic->getId()]);
        }
    
        return $this->render('forum/show.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }
    
    

    
}