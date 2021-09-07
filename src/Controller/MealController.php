<?php

namespace App\Controller;

use App\Entity\Language;
use App\Repository\MealRepository;
use App\Repository\LanguageRepository;
use App\Service\ArrayUrlService;
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

class MealController extends AbstractController
{
    private $aus;
    private $dishRepo;
    private $languageRepo;
    private $validator;

    private const DEFAULT_PER_PAGE = 5;

    public function __construct(ArrayUrlService $aus, MealRepository $dishRepo, LanguageRepository $languageRepo, ValidatorService $validator)
    {
        $this->aus          = $aus;
        $this->dishRepo     = $dishRepo;
        $this->languageRepo = $languageRepo;
        $this->validator    = $validator;
    }

    /**
     * @Route("/meals", name="meals")
     */
    public function meals(Request $request): JsonResponse
    {
        $fields = $this->validator->validateFields($request, ['per_page', 'page', 'category', 'tags', 'with', 'lang', 'diff_time'],
                                                       ['integer', 'integer', 'string', 'array', 'array', 'string', 'integer'],
                                                       [false, false, false, false, false, true, false],
                                                       [1, 1, 1, null, ['ingredients', 'category', 'tags'], null, 1]);


        if (!$this->languageRepo->languageExists($fields['lang']))
        {
            throw new BadRequestHttpException("The language '" . $fields['lang'] . "' does not exist in the database!");
        }

        $fields['per_page'] = $fields['per_page'] ? $fields['per_page'] : self::DEFAULT_PER_PAGE;
        $fields['page']     = $fields['page'] ? $fields['page'] : 1;

        $dishes = $this->dishRepo->findAllFromRequest($fields);
        $ignored = array_diff(['ingredients', 'category', 'tags'], $fields['with']);
        $obj = new class {};

        $obj->meta = new class {};
        $obj->meta->currentPage = $fields['page'];
        $obj->meta->totalItems = count($dishes);
        $obj->meta->itemsPerPage = $fields['per_page'];
        $obj->meta->totalPages = ceil($obj->meta->itemsPerPage ? $obj->meta->totalItems / $obj->meta->itemsPerPage : 1);

        $obj->data = [];

        foreach ($dishes as $dish)
        {
            $obj->data[] = $dish->getFullObject($fields['lang'], $fields['diff_time'], $ignored, false);
        }

        $obj->links = new class {};
        $escapedFields = $this->aus->escapeArray($fields);

        if ($obj->meta->currentPage > 1)
        {
            $escapedFields['page'] = $obj->meta->currentPage - 1;
            $obj->links->prev = $this->generateUrl('meals', $escapedFields);
        }

        else
        {
            $obj->links->prev = null;
        }

        if ($obj->meta->currentPage < $obj->meta->totalPages)
        {
            $escapedFields['page'] = $obj->meta->currentPage + 1;
            $obj->links->next = $this->generateUrl('meals', $escapedFields);

            $escapedFields['page'] = $obj->meta->currentPage;
        }

        else
        {
            $obj->links->next = null;
            $obj->links->prev = null;
        }

        $obj->links->self = $this->generateUrl('meals', $escapedFields);

        $json = json_encode($obj, JSON_PRETTY_PRINT);
        return new JsonResponse($json, 200, [], true);
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
