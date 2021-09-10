<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Service\SlugService;
use Doctrine\Persistence\ObjectManager;

class IngredientFixture extends BaseFixture
{
    public const NUM_INGREDIENTS = 20;

    private $slugger;

    public function __construct(SlugService $slugger)
    {
        parent::__construct();
        $this->slugger = $slugger;
    }

    protected function doPerLocale($ingredient, string $locale, &$faker)
    {
        $ingredient->translate(self::localeToLanguage($locale))->setTitle($faker->text(50));
    }

    protected function entityFactory($ingredient, int $index)
    {
        $ingredient->setSlug($this->slugger->escapeText($ingredient->translate('en')->getTitle()));
    }

    protected function loadData(ObjectManager $om)
    {
        $this->createMany('Ingredient', self::NUM_INGREDIENTS);

        $om->flush();
    }
}
