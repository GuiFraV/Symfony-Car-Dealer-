<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Car;
use App\Entity\CarCategory;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       // Variable faker pour générer des données aléatoires en Français 
       $faker = Factory::create('fr_FR');
       // Permet de générer des données liée à des voitures grâce au bundle https://github.com/pelmered/fake-car
       $faker->addProvider(new \Faker\Provider\Fakecar($faker));
       // Permet de stocker les catégories dans un array
       $categories = [];
       
       // Génère 5 catégories de voitures
       for ($i = 0; $i < 5; $i++) {
            $category = new CarCategory();
            $category->setName($faker->word);
            $manager->persist($category);
            $categories[] = $category; // Ajoute la catégories générée à chaque itérations
            
        }

        // Génère 50 voitures avec les propriétés suivantes :
        // Marque de la voiture
        // Nombre de siège
        // Nombre de porte 
        // Prix de la voiture générée aléatoirement entre 10 000 et 50 000 sous forme d'un float
        // Permet d'ajouter une catégorie sélectionnée aléatoirement dans la variable $categories[]
        for ($j = 0; $j < 50; $j++) {
            $car = new Car();
            $car->setName($faker->vehicle);
            $car->setNbSeats($faker->vehicleSeatCount);
            $car->setNbDoors($faker->vehicleDoorCount);
            $car->setCost($faker->randomFloat(2, 10000, 50000));
            $car->setCategory($categories[array_rand($categories)]);
            $manager->persist($car);
        }

        // Envoi des données générés en BDD
        $manager->flush();
}
}
