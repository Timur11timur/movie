<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    private MovieRepository $movieRepository;
    private EntityManagerInterface $em;

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    #[Route('/movies', name: 'app_movies', methods: ['GET'])]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();

        return $this->render('movies/index.html.twig', compact('movies'));
    }

    #[Route('/movies/create', name: 'app_movies_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/edit/{id}', name: 'app_movies_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    //Delete file
                }

                if (file_exists($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath())) {
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $movie->setImagePath('/uploads/' . $newFileName);

                    $this->em->flush();

                    return $this->redirectToRoute('app_movies');
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());

                $this->em->flush();

                return $this->redirectToRoute('app_movies');
            }
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movies/delete/{id}', name: 'app_movies_delete', methods: ['GET', 'DELETE'])]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirectToRoute('app_movies');
    }

    #[Route('/movies/{id}', name: 'app_movies_show', methods: ['GET'])]
    public function show($id): Response
    {
        $movie =  $this->movieRepository->find($id);

        return $this->render('movies/show.html.twig', compact('movie'));
    }

    #[Route('/movies1', name: 'app_movies1')]
    public function index1(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Movie::class);
        $movies = $repository->findAll();
        $movie = $repository->find(5);
        $movies = $repository->findBy([], ['id' => 'DESC']);
        $movie = $repository->findOneBy(['id' => 5, 'title' => 'The Dark Knight'], ['id' => 'DESC']);
        $count = $repository->count([]);
        $name = $repository->getClassName();

        dd($movies);

        return $this->render('index.html.twig', compact('movies'));
    }

    #[Route('/movies2', name: 'app_movies2')]
    public function index2(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        dd($movies);

        return $this->render('index.html.twig');
    }
}
