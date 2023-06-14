<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CarCrudController extends AbstractCrudController
{
    // Méthode qui permet de retourner le CRUD EasyAdmin de l'entité Car
    public static function getEntityFqcn(): string
    {
        return Car::class;
    }
}