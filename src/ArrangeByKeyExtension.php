<?php

namespace Gautile\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Database\Eloquent\Collection;

class ArrangeByKeyExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter( 'arrangeByKey', array($this, 'collectionFilter') ),
        );
    }

    public function getName()
    {
        return 'arrangeByKey_extension';
    }

    public function collectionFilter($contents, $key)
    {
        $this->validateInputs($contents, $key);

        $newArray = [];
        foreach( $contents as $content ) {

            if (is_object($content)) {
                $newArray[$this->getValueFromObjectWithKey($content, $key)][] = $content;
            }
            else if (is_array($content)) {
                $newArray[$this->getValueFromArrayWithKey($content, $key)][] = $content;
            }
        }

        return $newArray;
    }

    private function validateInputs(&$contents, $key)
    {
        if ( !is_string($key) ) {
            throw new \InvalidArgumentException('Variable passed as key to the getArraysByKeyValue filter 
            is not a string');
        }

        //if DoctrineCollection or EloquentCollection, convert to array
        if ( is_a($contents, 'Doctrine\Common\Collections\Collection') ||
            is_a($contents, 'Illuminate\Database\Eloquent\Collection') ) {
            /**
             * @var $contents ArrayCollection|Collection
             */
            $contents = $contents->toArray();
        }

        if ( !is_array($contents) ) {
            throw new \InvalidArgumentException('Variable passed to the getArraysByKeyValue filter is not an array');
        }

        if ( empty($contents) ) {
            return [];
        }

        //if is not an array containing arrays or objects
        if ( !is_array($contents[0]) && !is_object($contents[0]) ) {
            throw new \InvalidArgumentException('Variable passed to the getArraysByKeyValue filter is not a valid 
            multidimensional array');
        }

        return true;
    }

    private function getValueFromObjectWithKey($content, $key)
    {
        $hasExplicitGetter = method_exists($content, 'get' . ucfirst($key));
        $currentValue = $hasExplicitGetter ? $content->{'get' . ucfirst($key)} : $content->{$key};
        if ( ( is_null($currentValue) || empty($currentValue) ) ) {
            throw new \InvalidArgumentException('Variable passed to the getArraysByKeyValue contains elements with 
            value of key '.$key.' with invalid value');
        }

        return $currentValue;
    }

    private function getValueFromArrayWithKey($content, $key)
    {
        if ( !array_key_exists($key, $content) ) {
            throw new \InvalidArgumentException('Variable passed to the getArraysByKeyValue contains elements
                 without provided key');
        }

        return $content[$key];
    }
}