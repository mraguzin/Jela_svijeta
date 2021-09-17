<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use ReflectionClass;

trait SerializableTrait
{
    private function handleSubObjects(string $language, $timestamp, &$obj)
    {
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property) {
            $name = $property->getName();

            if (!$property->isStatic() && is_object($this->$name)) {
                if ($this->$name instanceof SerializableInterface) {
                    $obj[$name] = $this->$name->getFullObject($language, $timestamp); // Serialize recursively
                } elseif ($this->$name instanceof Collection) {
                    $obj[$name] = [];
                    foreach ($this->$name as $element) {
                        if (is_object($element)) {
                            if ($element instanceof SerializableInterface) {
                                $obj[$name][] = $element->getFullObject($language, $timestamp);
                            }
                        }
                    }
                }
            }
        }
    }

    private function getTranslatableId()
    {
        foreach ($this->translations as $translation) {
            return $translation->getTranslatable()->getId();
        }
    }

    public function getFullObject(string $language, $timestamp, array $ignoredFields = [], bool $json = false)
    {
        array_push(
            $ignoredFields,
            'newTranslations',
            'currentLocale',
            'defaultLocale',
            'deletedAt',
            'createdAt',
            'updatedAt',
            'translatableentityclass',
            'translations',
            'translatable',
            'locale',
            'name',
            '__initializer__',
            '__cloner__',
            '__isInitialized__'
        );

        $obj = [];
        $rc1 = new ReflectionClass($this);

        $properties = $rc1->getProperties();
        foreach ($properties as $property) {
            $propName = $property->getName();
            if (!$property->isStatic() && !is_object($this->$propName)) {
                if ($propName == 'id' && $this instanceof TranslatableInterface) {
                    $obj['id'] = $this->getTranslatableId();
                } else {
                    $obj[$propName] = $this->$propName;
                }
            }
        }

        if ($this instanceof TranslatableInterface) {
            $rc2 = new ReflectionClass('App\\Entity\\' . $rc1->getShortName() . 'Translation');
            $methods = $rc2->getMethods();

            foreach ($methods as $method) {
                $methodName = $method->getShortName();
                if (substr($methodName, 0, 3) == 'get' && $methodName != 'getId') {
                    $fieldName = strtolower(substr($methodName, 3));
                    $obj[$fieldName] = $this->translate($language)->$methodName();
                }
            }
        }


        if ($this instanceof TimestampableInterface && $this instanceof SoftDeletableInterface) {
            $obj['status'] = 'created';
            
            if ($timestamp > 0) {
                if ($this->getDeletedAt() !== null && $this->getDeletedAt()->getTimestamp() > $timestamp) {
                    $obj['status'] = 'deleted';
                } elseif ($this->getUpdatedAt() !== null && $this->getUpdatedAt()->getTimestamp() > $timestamp) {
                    $obj['status'] = 'modified';
                }
            }
        }

        $this->handleSubObjects($language, $timestamp, $obj);

        foreach ($ignoredFields as $ignored) {
            unset($obj[$ignored]);
        }

        if ($json) {
            return json_encode($obj);
        } else {
            return $obj;
        }
    }
}
