<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Building;
use App\Entity\User;
use App\Form\BuildingType;
use App\Form\SearchBuildingType;
use App\Repository\ArticleRepository;
use App\Repository\BuildingRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param UserRepository $userRepository
     * @param Request $request
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
    public function index(UserRepository $userRepository, Request $request, BuildingRepository $buildingRepository): Response
    {
        $searchBuildingForm = $this->createForm(SearchBuildingType::class);
        $searchBuildingForm->handleRequest($request);

        if ($searchBuildingForm->isSubmitted() && $searchBuildingForm->isValid()) {
            $street = $searchBuildingForm->getData()->getStreet();
            $building = $buildingRepository->searchBuilding($street);
            if ($building == null) {
                return $this->redirectToRoute('building_new');
            }
            $id = $building[0]->getId();
            return $this->redirectToRoute('building_found', [
                'id' => $id,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'users' => $userRepository->findAll(),
            'form' => $searchBuildingForm->createView(),
        ]);
    }

    /**
     * @Route("/fonctionnement", name="about")
     * @return Response
     */
    public function about()
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/ajouter_immeuble", name="building_new", methods={"GET","POST"})
     */
    public function newBuilding(Request $request): Response
    {
        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $building->setPassword(random_int($min = 0001, $max = 99999));
            /** TO DO : OPTIMISER GENERATION PASSWORD  */
            $entityManager->persist($building);
            $entityManager->flush();

            return $this->redirectToRoute('building_new_success', [
                'id' => $building->getId(),
            ]);
        }

        return $this->render('building/new.html.twig', [
            'building' => $building,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/immeuble/{id}", name="building_found", methods={"GET","POST"})
     * @param Building $building
     */
    public function foundBuilding(Building $building): Response
    {
        return $this->render('building/search_success.html.twig', [
            'building' => $building,
        ]);
    }

    /**
     * @Route("/félicitations/{id}", name="building_new_success", methods={"GET"})
     * @param Building $building
     * @return Response
     */
    public
    function buildingNewSuccess(Building $building): Response
    {
        return $this->render('building/new_success.html.twig', [
            'building' => $building,
        ]);
    }


    /**
     * @Route("/articles", name="news", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function articles(ArticleRepository $articleRepository): Response
    {
        return $this->render('home/news.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }


}
