<?php

namespace App\Controller;

use App\Entity\Language;
use App\Repository\DishRepository;
use App\Repository\LanguageRepository;
use App\Service\FakeDataGenerator;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DishController extends AbstractController
{
    /**
     * @Route("/meals", name="meals")
     */
    public function meals(DishRepository $dishRepo, LanguageRepository $languageRepo, ValidatorService $validator, Request $request): JsonResponse
    {
        $fields = $validator->validateFields($request, ['per_page', 'page', 'category', 'tags', 'with', 'lang', 'diff_time'],
                                                       ['integer', 'integer', 'string', 'array', 'array', 'string', 'integer'],
                                                       [false, false, false, false, false, true, false],
                                                       [1, 1, 1, null, ['ingredients', 'category', 'tags'], null, 1]);

        $numDishes = $dishRepo->getNumberOfDishes();

        if (!$languageRepo->languageExists($fields['lang']))
        {
            throw new BadRequestHttpException("The language '" . $fields['lang'] . "' does not exist in the database!");
        }

        // if ($fields['page'] === null)
        // {
        //     $fields['page'] = 1;
        // }

        if ($fields['page'] * $fields['per_page'] > $numDishes)
        {
            $fields['page'] = $fields['page']-1;
        }

        $dishes = $dishRepo->findAllFromRequest($fields);
        $ignored = array_diff(['ingredients', 'category', 'tags'], $fields['with']);
        $obj = new stdClass();

        $obj->meta = new stdClass();
        $obj->meta->currentPage = $fields['page'] ? $fields['page'] : 1;
        $obj->meta->totalItems = $numDishes;
        $obj->meta->itemsPerPage = $fields['per_page'];
        $obj->meta->totalPages = $obj->meta->itemsPerPage ? $numDishes / $obj->meta->itemsPerPage : 1;

        $obj->data = [];

        foreach ($dishes as $dish)
        {
            $obj->data[] = $dish->getFullObject($fields['lang'], $fields['diff_time'], $ignored, false);
            //$obj->data[] = $dish;
        }

        $obj->links = new stdClass();
        if ($obj->meta->currentPage > 1)
        {
            $fields['page'] = $obj->meta->currentPage - 1;
            $obj->links->prev = $this->generateUrl('meals', $fields);
        }

        else
        {
            $obj->links->prev = null;
        }

        if ($obj->meta->currentPage < $obj->meta->totalPages)
        {
            $fields['page'] = $obj->meta->currentPage + 1;
            $obj->links->next = $this->generateUrl('meals', $fields);
        }

        else
        {
            $obj->links->next = null;
        }

        $fields['page'] = $obj->meta->currentPage;
        $obj->links->self = $this->generateUrl('meals', $fields);

        return new JsonResponse($obj);
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
