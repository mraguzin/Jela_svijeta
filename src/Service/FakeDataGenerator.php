<?php

namespace App\Service;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;

class FakeDataGenerator
{
    private const SEED = 5621265;
    private const MAX_FAKE_TEXT_LEN = 100;

    private const NUM_DISHES      = 10;
    private const NUM_INGREDIENTS = 20;
    private const NUM_CATEGORIES  = 5;
    private const NUM_TAGS        = 5;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    

    public function generate(array $entities, array $quantities, array $locales, array $translatableFields)
    {
        $generator = \Faker\Factory::create();
        $generator->seed(FakeDataGenerator::SEED);
        $populator = new \Faker\ORM\Doctrine\Populator($generator);

        for ($i = 0; $i < count($entities); $i++)
        {
            $populator->addEntity($entities[$i], $quantities[$i], [
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
                'visible'    => null,
            ]);//, [], true);
        }
        //TODO: special slug handling here?

        $populator->execute($this->em);

        foreach ($locales as $locale)
        {
            $language = substr($locale, 0, 2);
            $generator = \Faker\Factory::create($locale);
            $generator->seed(FakeDataGenerator::SEED);

            foreach ($translatableFields as $field)
            {
                foreach ($entities as $entity)
                {
                    $methodName = 'set' . ucfirst($field);
                    $rc = new ReflectionClass('App\\Entity\\' . $entity);
                    if (!$rc->hasMethod($methodName))
                    {
                        continue;
                    }

                    $instances = $this->em->getRepository($entity)->findAll();
                    foreach ($instances as $instance)
                    {
                        $instance->translate($language)->$methodName($generator->text(FakeDataGenerator::MAX_FAKE_TEXT_LEN));
                    }
                }
            }

            $instance->mergeNewTranslations();
            //TODO: persist?
        }

        $this->em->flush();
    }
}