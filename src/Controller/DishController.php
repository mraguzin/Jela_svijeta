<?php

namespace App\Controller;

use App\Entity\Language;
use App\Service\FakeDataGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DishController extends AbstractController
{
    /**
     * @Route("/meals", name="meals")
     */
    public function meals(Request $request): JsonResponse
    {
        
    }

    /**
     * @Route("/fake_dish_data", name="faker")
     */
    public function index(EntityManagerInterface $em, FakeDataGenerator $faker, Request $request): Response
     // NOT USED due to the Faker library being incompatible with new Doctrine versions

    {
        // Three languages assumed for each translatable field by default: it, en, de
        $numDishes      = $request->query->get('numDishes', 10);
        $numIngredients = $request->query->get('numIngredients', 20);
        $numCategories  = $request->query->get('numCategories', 5);
        $numTags        = $request->query->get('numTags', 5);
        $locales        = ['it_IT', 'en_US', 'de_DE'];

        $faker->generate(['Category', 'Tag', 'Ingredient', 'Dish'],
                         [$numCategories, $numTags, $numIngredients, $numDishes],
                         $locales, ['title']);

        // Populate the languages table with the aforementioned locales (only the basic language code is needed for the translations extension)
        foreach ($locales as $locale)
        {
            $language = new Language;
            $language->setId(substr($locale, 0, 2));
            $em->persist($language);
        }

        $em->flush();

        return new Response('Done!');
    }
}
