<?php

namespace App\Entity;

use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\PersistentCollection;

trait SerializableTrait
{
    private function handleProperties(string $language, $timestamp, &$obj)
    {
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property)
        {
            $name = $property->getName();

            if (!$property->isStatic() && is_object($obj->$name))
            {
                $type = get_class($obj->$name);
                echo "name: $name | type: $type ";
                $rc2 = new ReflectionClass($obj->$name);

                if ($rc2->implementsInterface('App\\Entity\\SerializableInterface'))
                {
                    echo "SERIALIZABLE: $name ";
                    //$obj->$name = $obj->$name->getFullObject($language, $timestamp); // Serialize recursively
                    $obj->$name = $obj->$name->getFullObject($language, $timestamp); // Serialize recursively
                }

                else if ($rc2->getShortName() == 'PersistentCollection')
                {
                    echo "SERIALIZABLE: $name ";
                    foreach ($obj->$name as &$element)
                    {
                        if (is_object($element))
                        {
                            $type = get_class($element);
                            $rc2 = new ReflectionClass($element);
                            if ($rc2->implementsInterface('App\\Entity\\SerializableInterface'))
                            {
                                $element = $element->getFullObject($language, $timestamp);
                            }
                        }
                    }
                }
            }
            
        }
    }

    public function getFullObject(string $language, $timestamp, array $ignoredFields = [], bool $json = false)
    {
        $obj = $this;
        $rc1 = new ReflectionClass($this);
        echo "DISH CLASS NAME: " . $rc1->getName() . ' ';

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
            if ($obj->getCreatedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'created';
            }

            else if ($obj->getUpdatedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'modified';
            }

            else if ($obj->getDeletedAt()->getTimestamp() > $timestamp)
            {
                $obj->status = 'deleted';
            }
        }

        $this->handleProperties($language, $timestamp, $obj);

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