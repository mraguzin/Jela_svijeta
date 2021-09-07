<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ReflectionClass;
use stdClass;

trait SerializableTrait
{
    private function handleSubObjects(string $language, $timestamp, &$obj)
    {
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property)
        {
            $name = $property->getName();

            if (!$property->isStatic() && is_object($this->$name))
            {
                $rc2 = new ReflectionClass($this->$name);

                if ($rc2->implementsInterface('App\Entity\SerializableInterface'))
                {
                    $obj->$name = $this->$name->getFullObject($language, $timestamp); // Serialize recursively
                }

                elseif ($rc2->isSubclassOf('Doctrine\Common\Collections\Collection'))
                {
                    $obj->name = [];
                    foreach ($this->$name as $element)
                    {
                        if (is_object($element))
                        {
                            $rc2 = new ReflectionClass($element);
                            if ($rc2->implementsInterface('App\Entity\SerializableInterface'))
                            {
                                $obj->$name[] = $element->getFullObject($language, $timestamp);
                            }
                        }
                    }
                }
            }
            
        }
    }

    private function getTranslatableId()
    {
        foreach ($this->translations as $translation)
        {
            return $translation->getTranslatable()->getId();
        }
    }

    public function getFullObject(string $language, $timestamp, array $ignoredFields = [], bool $json = false)
    {
        array_push($ignoredFields, 'newTranslations', 'currentLocale', 'defaultLocale', 'deletedAt', 'createdAt', 'updatedAt', 'translatableentityclass',
        'translatable', 'locale', 'name', '__initializer__', '__cloner__', '__isInitialized__');
        $obj = new class {};
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property)
        {
            $propName = $property->getName();
            if (!$property->isStatic() && !is_object($this->$propName))
            {
                if ($propName == 'id' && $rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface'))
                {
                    $obj->id = $this->getTranslatableId();
                }

                else
                {
                    $obj->$propName = $this->$propName;
                }
                
            }
        }

        if ($rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface'))
        {
            $rc2 = new ReflectionClass('App\\Entity\\' . $rc1->getShortName() . 'Translation');
            $methods = $rc2->getMethods();

            foreach ($methods as $method)
            {
                $methodName = $method->getShortName();
                if (substr($methodName, 0, 3) == 'get' && $methodName != 'getId')
                {
                    $fieldName = strtolower(substr($methodName, 3));
                    $obj->$fieldName = $this->translate($language)->$methodName();
                }
            }
        }

        $obj->status = 'created';

        if ($rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface') && 
            $rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface') && $timestamp > 0)
        {
            if ($this->getDeletedAt() !== null && $this->getDeletedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'deleted';
            }

            elseif ($this->getUpdatedAt() !== null && $this->getUpdatedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'modified';
            }
        }

        $this->handleSubObjects($language, $timestamp, $obj);

        foreach ($ignoredFields as $ignore)
        {
            unset($obj->$ignore);
        }

        if ($json)
        {
            return json_encode($obj);
        }

        else
        {
            return $obj;
        }
    }
}