<?php

namespace App\Controller;

use App\Service\WeatherService;
use App\Repository\CarRepository;
use App\Repository\CarCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarController extends AbstractController
{
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }


    #[Route('/', name: 'app_car')]
    public function index(CarRepository $carRepository, CarCategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        $queryBuilder = $carRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder->where('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
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


        $weatherData = $this->weatherService->getWeatherData();
        $currentHour = date("Y-m-d\TH:00"); 
        $timeIndex = array_search($currentHour, $weatherData['hourly']['time']);

        if ($timeIndex === false) {
            $currentHour = date("Y-m-d\TH:00", strtotime("-1 hour"));
            $timeIndex = array_search($currentHour, $weatherData['hourly']['time']);
        }

        // if ($timeIndex === false) {
        //     // If $currentHour is still not found in the array, handle the error
        //     echo "The current hour is not found in the array.";
        // } else {
        //     echo "The index of the current hour in the array is " . $timeIndex;
        // }

        $currentTemperature = $weatherData['hourly']['temperature_2m'][$timeIndex];

        // dd($timeIndex);

        return $this->render('car/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categoryRepository->findAll(),
            'currentTemperature' => $currentTemperature,
        ]);
    }
}
