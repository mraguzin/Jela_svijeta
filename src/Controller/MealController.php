<?php

namespace App\Controller;

use App\Repository\MealRepository;
use App\Repository\LanguageRepository;
use App\Service\ArrayUrlService;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $fields = $this->validator->validateFields(
            $request,
            ['per_page', 'page', 'category', 'tags', 'with', 'lang', 'diff_time'],
            ['integer', 'integer', 'string', 'array', 'array', 'string', 'integer'],
            [false, false, false, false, false, true, false],
            [1, 1, 1, null, ['ingredients', 'category', 'tags'], null, 1]
        );


        if (!$this->languageRepo->languageExists($fields['lang'])) {
            throw new BadRequestHttpException("The language '" . $fields['lang'] . "' does not exist in the database!");
        }

        $fields['per_page'] = $fields['per_page'] ?: self::DEFAULT_PER_PAGE;
        $fields['page']     = $fields['page'] ?: 1;

        $dishes = $this->dishRepo->findAllFromRequest($fields);
        $ignored = array_diff(['ingredients', 'category', 'tags'], $fields['with']);
        $obj = [
            'meta' => [
                'currentPage'  => $fields['page'],
                'totalItems'   => count($dishes),
                'itemsPerPage' => $fields['per_page']
            ],
            'data' => []
        ];

        $obj['meta']['totalPages'] = ceil($obj['meta']['itemsPerPage'] ? $obj['meta']['totalItems'] / $obj['meta']['itemsPerPage'] : 1);

        foreach ($dishes as $dish) {
            $obj['data'][] = $dish->getFullObject($fields['lang'], $fields['diff_time'], $ignored, false);
        }

        $escapedFields = $this->aus->escapeArray($fields);
        if ($obj['meta']['currentPage'] > 1) {
            $escapedFields['page'] = $obj['meta']['currentPage'] - 1;
            $obj['links']['prev'] = $this->generateUrl('meals', $escapedFields, UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $obj['links']['prev'] = null;
        }

        if ($obj['meta']['currentPage'] < $obj['meta']['totalPages']) {
            $escapedFields['page'] = $obj['meta']['currentPage'] + 1;
            $obj['links']['next'] = $this->generateUrl('meals', $escapedFields, UrlGeneratorInterface::ABSOLUTE_URL);

            $escapedFields['page'] = $obj['meta']['currentPage'];
        } else {
            $obj['links']['next'] = null;
            $obj['links']['prev'] = null;
        }

        $obj['links']['self'] = $this->generateUrl('meals', $escapedFields, UrlGeneratorInterface::ABSOLUTE_URL);

        $json = json_encode($obj, JSON_PRETTY_PRINT);
        return new JsonResponse($json, 200, [], true);
    }
}
