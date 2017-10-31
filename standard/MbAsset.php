<?php

namespace MbAnnotation\standard;

use Illuminate\Contracts\Support\Arrayable;
use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use mindplay\annotations\IAnnotationParser;
use MocaBonita\tools\MbAsset as Asset;
use MocaBonita\tools\MbPath;
use Serializable;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('method' => true, 'class' => true, 'inherited' => true, 'multiple' => true)
 */
class MbAsset extends Annotation implements IAnnotationParser, Arrayable, Serializable
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $complement;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param string $value The raw string value of the Annotation.
     *
     * @return array An array of Annotation properties.
     */
    public static function parseAnnotation($value)
    {
        $parts = \explode(' ', \trim($value), 3);

        if (\count($parts) < 2) {
            return [];
        }

        return ['type' => $parts[0], 'path' => $parts[1], 'complement' => isset($parts[2]) ? $parts[2] : null];
    }

    /**
     * Initialize the annotation.
     */
    public function initAnnotation(array $properties)
    {
        $this->map($properties, ['type', 'path']);

        parent::initAnnotation($properties);

        if (!isset($this->type)) {
            throw new AnnotationException('MbAsset requires a type property');
        }

        if (!isset($this->path)) {
            throw new AnnotationException('MbAsset requires a path property');
        }
    }

    /**
     * @param Asset $mbAsset
     *
     * @return Asset
     *
     */
    public function mocaBonita(Asset $mbAsset)
    {
        switch ($this->getComplement()) {
            case 'plugin':
                $this->path = MbPath::pUrl($this->path);
                break;
            case 'js' :
                $this->path = MbPath::pJsDir($this->path);
                break;
            case 'css' :
                $this->path = MbPath::pCssDir($this->path);
                break;
            case 'bower' :
                $this->path = MbPath::pBwDir($this->path);
                break;
            default :
                $this->path = $this->getComplement() . $this->path;
                break;
        }

        switch ($this->getType()) {
            case 'js' :
                $mbAsset->setJs($this->getPath());
                break;
            case 'css' :
                $mbAsset->setCss($this->getPath());
                break;
        }

        return $mbAsset;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'complement' => $this->getComplement(),
            'path'       => $this->getPath(),
            'type'       => $this->getType(),
        ];
    }

    /**
     * String representation of object
     *
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Constructs the object
     *
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->initAnnotation(unserialize($serialized));
    }
}