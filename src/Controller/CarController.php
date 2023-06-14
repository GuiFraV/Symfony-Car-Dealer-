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
    // Déclaration d'une variable private pour stocker les data de WeatherService
    private $weatherService;

    // Méthode pour pour injecter les données de l'API WeatherService dans la variable privée weatherService
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    // Route de base du controller qui est la première page du site
    #[Route('/', name: 'app_car')]
    public function index(CarRepository $carRepository, CarCategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Sotck les paramètres de recherche de la requête 'search' et 'cateogry'
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        // Requête en BDD
        $queryBuilder = $carRepository->createQueryBuilder('c');

        // S'il y a une entrée utilisateur dans le champ search, ajoute une condition WHERE à la requête
        if ($search) {
            $queryBuilder->where('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // S'il y a une catégorie de sélectionné, ajoute une nouvelle condition WHERE à la requête
        if ($category) {
            $queryBuilder->andWhere('c.category = :category')
                ->setParameter('category', $category);
        }

        // Stock dans la variable la query
        $query = $queryBuilder->getQuery();

        // Utilisation du bundle KnpPaginatorBundle pour paginer les résultats 
        $pagination = $paginator->paginate(
            // La requête à paginer:
            $query,
            // Définit le numéro de la page actuelle
            $request->query->getInt('page', 1),
            // Définit le nom d'élément par page
            20
        );

        // Variable qui stock l'appel API du service weatherData sous forme d'un array
        $weatherData = $this->weatherService->getWeatherData();
        // Heure actuelle sous forme suivant le format utilisé par l'API 
        $currentHour = date("Y-m-d\TH:00"); 
        // Permet de trouver l'index de l'heure actuelle avec les données de l'API
        $timeIndex = array_search($currentHour, $weatherData['hourly']['time']);

        // Vérification si l'index existe dans l'API, si ce n'est pas le cas:
        // C'est le seul moyen que j'ai trouver sur stakoverflow pour gérer l'erreur dans le cas ou timeIndex = false
        if ($timeIndex === false) {
            // Permet d'avoir l'heure précédente sous le même format que l'API
            $currentHour = date("Y-m-d\TH:00", strtotime("-1 hour"));
            // Recherche l'index dans l'api avec la variable currentHour 
            $timeIndex = array_search($currentHour, $weatherData['hourly']['time']);
        }

        // Débugage pour l'erreur false avec l'API
        // if ($timeIndex === false) {
        //     // Dans le cas ou $currentHour n'est pas trouvé dans l'array de l'API
        //     echo "Pas de données valide";
        // } else {
        //     echo "L'index de l'heure actuelle est " . $timeIndex;
        // }

        // Permet de trouver la température en fonction du $timeIndex
        // L'index est le seul moyen d'avoir la température dans l'API
        $currentTemperature = $weatherData['hourly']['temperature_2m'][$timeIndex];

        // dd($timeIndex);

        return $this->render('car/index.html.twig', [
            'pagination' => $pagination, // Variable  qui contient les données de pagination à faire passer dans la vue TWIG
            'categories' => $categoryRepository->findAll(), // Variable qui contient toutes les catégories générés en BDD
            'currentTemperature' => $currentTemperature, // Variable qui contient la température actuelle obtenu à partir du service WeatherService
        ]);
    }
}
