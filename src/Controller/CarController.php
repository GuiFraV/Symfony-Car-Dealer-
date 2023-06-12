<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\CarCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{
    #[Route('/', name: 'app_car')]
    public function index(CarRepository $carRepository, CarCategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        $queryBuilder = $carRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder->where('c.name LIKE :search')
                         ->setParameter('search', '%'.$search.'%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.category = :category')
                         ->setParameter('category', $category);
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), 
            20 
        );

        return $this->render('car/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}