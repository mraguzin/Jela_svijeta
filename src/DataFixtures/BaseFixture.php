<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use ReflectionClass;

abstract class BaseFixture extends Fixture
{
    private ObjectManager $om;

    public const LOCALES   = ['en_US', 'it_IT', 'de_DE'];
    public const SEED      = 5621265;

    public static function localeToLanguage(string $locale)
    {
        return substr($locale, 0, 2);
    }

    public static function foreachLocale(callable $f)
    {
        foreach (self::LOCALES as $locale) {
            call_user_func_array($f, [$locale, &self::$fakers[$locale]]);
        }
    }

    protected static $fakers = [];
    private static $didInit = false;

    public function __construct()
    {
        if (!self::$didInit) {
            self::$didInit = true;
            foreach (self::LOCALES as $locale) {
                self::$fakers[$locale] = \Faker\Factory::create($locale);
                self::$fakers[$locale]->seed(self::SEED);
            }
        }
    }

    abstract protected function loadData(ObjectManager $om);
    abstract protected function doPerLocale($entity, string $locale, &$faker);
    abstract protected function entityFactory($entity, int $index); // Post-translation fixtures; can use the translations

    public function load(ObjectManager $om)
    {
        $this->om = $om;

        $this->loadData($om);
    }

    protected function createMany(string $className, int $count)
    {
        $className = 'App\\Entity\\' . $className;
        $rc = new ReflectionClass($className);
        $shortName = $rc->getShortName();

        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();

            if ($entity instanceof TranslatableInterface) {
                foreach (self::LOCALES as $locale) {
                    $this->doPerLocale($entity, $locale, self::$fakers[$locale]);
                }
            }

            $this->entityFactory($entity, $i);

            $this->addReference($shortName . '_' . $i, $entity);
            $this->om->persist($entity);

            if ($entity instanceof TranslatableInterface) {
                $entity->mergeNewTranslations();
            }
        }
    }
}
