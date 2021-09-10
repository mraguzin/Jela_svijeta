<?php

namespace App\DataFixtures;

use App\Service\RandomizerService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MealFixture extends BaseFixture implements DependentFixtureInterface
{
    public const NUM_MEALS = 10;
    private const MAX_FAKE_TEXT_LEN = 2000;

    private $randomizer;

    public function __construct(RandomizerService $urs)
    {
        parent::__construct();
        $this->randomizer = $urs;
    }

    protected function doPerLocale($dish, string $locale, &$faker)
    {
        $dish->translate(BaseFixture::localeToLanguage($locale))->setDescription($faker->text(self::MAX_FAKE_TEXT_LEN));
        $dish->translate(BaseFixture::localeToLanguage($locale))->setTitle($faker->name);
    }

    protected function entityFactory($dish, int $index)
    {
        static $seeded = false;

        if (!$seeded) {
            srand(self::SEED);
            $seeded = true;
        }

        $index = rand(0, CategoryFixture::NUM_CATEGORIES);
        if ($index === CategoryFixture::NUM_CATEGORIES) {
            $dish->setCategory(null);
        } else {
            $dish->setCategory($this->getReference('Category_' . $index));
        }

        $indices = $this->randomizer->getRandomArray(IngredientFixture::NUM_INGREDIENTS);
        foreach ($indices as $index) {
            $dish->addIngredient($this->getReference('Ingredient_' . $index));
        }

        $indices = $this->randomizer->getRandomArray(TagFixture::NUM_TAGS);
        foreach ($indices as $index) {
            $dish->addTag($this->getReference('Tag_' . $index));
        }
    }

    protected function loadData(ObjectManager $om)
    {
        $this->createMany('Meal', self::NUM_MEALS);

        $om->flush();
    }

    public function getDependencies()
    {
        return [
            IngredientFixture::class,
            CategoryFixture::class,
            TagFixture::class
        ];
    }
}
