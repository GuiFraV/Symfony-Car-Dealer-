<?php

namespace App\Controller\Admin;

use App\Entity\Car; 
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarAdminController extends AbstractDashboardController
{
    // Définition de l'URL pour accéder à la vue TWIG
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    // Méthode d'EasyAdmin pour configurer le DashBoard
    public function configureDashboard(): Dashboard
    {
        // Ajout du titre Car
        return Dashboard::new()
            ->setTitle('CarDealer');
    }

    // Méthode EasyAdmin pour créer des éléments dans le menu du DashBoard
    public function configureMenuItems(): iterable
    {
        // Ajout d'un lien et d'un boutton vers le DashBoard
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // Ajout de lien vers le CRUD pour les différents éléments Car de la BDD
        yield MenuItem::linkToCrud('Cars', 'fas fa-list', Car::class); 
    }
}
