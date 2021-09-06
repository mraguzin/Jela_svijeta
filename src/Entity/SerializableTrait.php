<?php

namespace App\Entity;

use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\PersistentCollection;
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

                else if ($rc2->isSubclassOf('Doctrine\Common\Collections\Collection'))
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

    public function getFullObject(string $language, $timestamp, array $ignoredFields = [], bool $json = false)
    {
        array_push($ignoredFields, 'newTranslations', 'currentLocale', 'defaultLocale', 'deletedAt', 'createdAt', 'updatedAt', 'translatableentityclass',
        'translatable', 'locale', 'name', '__initializer__', '__cloner__', '__isInitialized__');
        $obj = new stdClass();
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property)
        {
            $propName = $property->getName();
            if (!$property->isStatic() && !is_object($this->$propName))
            {
                $obj->$propName = $this->$propName;
            }
        }

        if ($rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface'))
        {
            $rc2 = new ReflectionClass('App\\Entity\\' . $rc1->getShortName() . 'Translation');
            $methods = $rc2->getMethods();

            foreach ($methods as $method)
            {
                $methodName = $method->getShortName();
                if (substr($methodName, 0, 3) == 'get')
                {
                    $fieldName = strtolower(substr($methodName, 3));
                    $obj->$fieldName = $this->translate($language)->$methodName();
                }
            }
        }

        if ($rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface') && 
            $rc1->implementsInterface('Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface') && $timestamp > 0)
        {
            if ($this->getDeletedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'deleted';
            }

            else if ($this->getUpdatedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'modified';
            }

            else if ($this->getCreatedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'created';
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