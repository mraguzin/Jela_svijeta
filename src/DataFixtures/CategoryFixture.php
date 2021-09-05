<?php

namespace App\DataFixtures;

use App\Service\SlugService;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends BaseFixture
{
    public const NUM_CATEGORIES = 5;

    private $slugger;

    public function __construct(SlugService $slugger) //TODO: autowire the slugger so we don't have to copy this same function in every fixture!
    {
        parent::__construct();
        $this->slugger = $slugger;
    }

    protected function doPerLocale($category, string $locale, &$faker)
    {
        $category->translate(self::localeToLanguage($locale))->setTitle($faker->text(20));
    }

    protected function entityFactory($category, int $index)
    {
        $category->setSlug($this->slugger->escapeText($category->translate('en')->getTitle()));
    }

    protected function loadData(ObjectManager $om)
    {
        $this->createMany('Category', self::NUM_CATEGORIES);

        $om->flush();
    }
}