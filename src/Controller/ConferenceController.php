<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class ConferenceController extends AbstractController
{
    /**
     * 
     * @var Environment
     */
    private $twig;

    /**
     * 
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * 
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * 
     * @author Martin Seon
     * @param Environment $twig 
     * @return void 
     */
    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }


    /**
     * 
     * @Route("/", name="homepage")
     * @author Martin Seon
     */
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return (new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ])))->setSharedMaxAge(3600);
    }

    /**
     * @Route("/conference_header", name="conference_header")
     */
    public function conferenceHeader(ConferenceRepository $conferenceRepository): Response
    {
        return ($this->render('conference/header.html.twig', [
            'conferences' => $conferenceRepository->findAll()
        ]))->setSharedMaxAge(3600);
    }

    /**
     * 
     * @Route("/conference/{slug}", name="conference")
     * @author Martin Seon
     */
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository,
        string $photoDir
    ) {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form->get('photo')->getData()) {
                $filename = bin2hex(random_bytes(6) . '.' . $photo->guessExtension());
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referrer'),
                'permalink' => $request->getUri(),
            ];
            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
        ]));
    }

    /**
     * @Route("/comment/test/{commentId}", name="commment_test")
     */
    public function test(
        int $commentId,
        CommentRepository $commentRepository,
        MailerInterface $mailer,
        string $adminEmail
    ): Response {
        /** @var Comment */
        $comment = $commentRepository->findOneBy(['id' => $commentId]);
        dump($comment);
        $mailer->send((new NotificationEmail())
            ->subject('New comment posted')
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->from($adminEmail)
            ->to($adminEmail)
            ->context(['comment' => $comment]));

        // return new Response('Test Completed');
        return $this->render('index.html.twig');
    }
}
