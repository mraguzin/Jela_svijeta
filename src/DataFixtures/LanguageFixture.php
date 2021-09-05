<?php

namespace App\DataFixtures;

use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LanguageFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (BaseFixture::LOCALES as $locale)
        {
            $language = new Language();
            $language->setId(BaseFixture::localeToLanguage($locale));

            $manager->persist($language);
        }

        $manager->flush();
    }
}