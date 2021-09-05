<?php

namespace App\DataFixtures;

use App\Entity\Dish;
use App\Entity\Tag;
use App\Service\RandomizerService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DishFixture extends BaseFixture implements DependentFixtureInterface
{
    public const NUM_DISHES = 10;
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
    }

    protected function entityFactory($dish, int $index)
    {
        static $seeded = false;

        if (!$seeded)
        {
            srand(self::SEED);
            $seeded = true;
        }

        $index = rand(0, CategoryFixture::NUM_CATEGORIES);
        if ($index === CategoryFixture::NUM_CATEGORIES)
        {
            $dish->setCategory(null);
        }

        else
        {
            $dish->setCategory($this->getReference('Category_' . $index));
        }

        $indices = $this->randomizer->getRandomArray(IngredientFixture::NUM_INGREDIENTS);
        foreach ($indices as $index)
        {
            $dish->addIngredient($this->getReference('Ingredient_' . $index));
        }

        $indices = $this->randomizer->getRandomArray(TagFixture::NUM_TAGS);
        foreach ($indices as $index)
        {
            $dish->addTag($this->getReference('Tag_' . $index));
        }

        // BaseFixture::foreachLocale(function(string $locale, &$faker) use ($dish, &$ii) {
        //     $dish->translate(BaseFixture::localeToLanguage($locale))->setDescription($faker->text(self::MAX_FAKE_TEXT_LEN)); //TODO: fix repeating fakes
        //     //$dish->translate(BaseFixture::localeToLanguage($locale))->setDescription((string)($ii++));
        // });
        
    }

    protected function loadData(ObjectManager $om)
    {
        $this->createMany('Dish', self::NUM_DISHES);

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