<?php

namespace App\DataFixtures;

use App\Service\SlugService;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends BaseFixture
{
    public const NUM_TAGS = 5;

    private $slugger;

    public function __construct(SlugService $slugger)
    {
        parent::__construct();
        $this->slugger = $slugger;
    }

    protected function doPerLocale($tag, string $locale, &$faker)
    {
        $tag->translate(self::localeToLanguage($locale))->setTitle($faker->name);
    }

    protected function entityFactory($tag, int $index)
    {
        $tag->setSlug($this->slugger->escapeText($tag->translate('en')->getTitle()));
    }

    protected function loadData(ObjectManager $om)
    {
        $this->createMany('Tag', self::NUM_TAGS);

        $om->flush();
    }


}