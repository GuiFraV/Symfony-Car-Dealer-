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
        
       $faker = Factory::create('fr_FR');
       $faker->addProvider(new \Faker\Provider\Fakecar($faker));
       $categories = [];
       
       for ($i = 0; $i < 5; $i++) {
            $category = new CarCategory();
            $category->setName($faker->word);
            $manager->persist($category);
            $categories[] = $category;
            
        }

        for ($j = 0; $j < 50; $j++) {
            $car = new Car();
            $car->setName($faker->vehicle);
            $car->setNbSeats($faker->vehicleSeatCount);
            $car->setNbDoors($faker->vehicleDoorCount);
            $car->setCost($faker->randomFloat(2, 10000, 50000));
            $car->setCategory($categories[array_rand($categories)]);
            $manager->persist($car);
            
        }

        $manager->flush();
}
}
