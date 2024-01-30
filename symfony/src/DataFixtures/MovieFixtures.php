<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\MediaObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;


class MovieFixtures extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager): void
    {

        $actors = [];
        $categories = []; 

        // Create a new MediaObject
        $mediaObjectActor = new MediaObject();
        $mediaObjectActor->setFilePath('actor.png');

        $manager->persist($mediaObjectActor);

        // Create 100 actors
        for ($i = 0; $i < 100; $i++) {
            $actor = new Actor();
            $actor->setName($this->faker->name());
            $actor->setBirth($this->faker->dateTimeBetween('-80 years', '-15 years')->format('Y-m-d'));
            $actor->setBiography($this->faker->paragraphs(3, true));
            $actor->setImage($mediaObjectActor);
            
            $manager->persist($actor);
            $actors[] = $actor; 
        }

        // Create some categories
        $categoriesData = [
            ['title' => 'Action', 'description' => 'Films d\'action'],
            ['title' => 'Comedy', 'description' => 'Comedy films'],
            ['title' => 'Drama', 'description' => 'Drama films'],
            ['title' => 'Horror', 'description' => 'Horror films'],
            ['title' => 'Thriller', 'description' => 'Thriller films'],
            ['title' => 'Western', 'description' => 'Western films'],
        ];

        foreach ($categoriesData as $categoryData) {
            $category = new Category();
            $category->setTitle($categoryData['title']);
            $category->setDescription($categoryData['description']);
        
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create a new MediaObject
        $mediaObjectMovie = new MediaObject();
        $mediaObjectMovie->setFilePath('black-cat.jpg');

        $manager->persist($mediaObjectMovie);

        // Create movies
        for ($i = 0; $i < 50; $i++) {
            $movie = new Movie();
            $movie->setTitle($this->faker->catchPhrase());
            $movie->setReleased($this->faker->dateTimeBetween('-30 years', 'now')->format('Y-m-d'));
            $movie->setResume($this->faker->paragraphs(3, true));
            $movie->setScore($this->faker->numberBetween(0, 10));
            $movie->setDirector($this->faker->name());
            $movie->setImage($mediaObjectMovie);

            // Add multiple random actors to the movie
            $randomActors = $this->faker->randomElements($actors, $this->faker->numberBetween(2, 5));
            foreach ($randomActors as $actor) {
                $movie->addFkActor($actor);
            }

            // // Add a random category to the movie
            $randomCategory = $this->faker->randomElement($categories);
            $movie->setFkCategory($randomCategory);

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
