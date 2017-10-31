<?php

namespace MbAnnotation\standard;

use Illuminate\Contracts\Support\Arrayable;
use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use Serializable;

/**
 * Class MbResource
 *
 * @package MbAnnotation\standard
 */
abstract class MbResource extends Annotation implements Arrayable, Serializable
{

    /**
     * Action name
     *
     * @var string
     */
    protected $name;

    /**
     * Check if action needs login
     *
     * @var bool
     */
    protected $login = true;

    /**
     * Check if action needs ajax
     *
     * @var bool
     */
    protected $ajax = false;

    /**
     * Requisition method required
     *
     * @var string
     */
    protected $method = null;

    /**
     * Check if action is a shortcode
     *
     * @var bool
     */
    protected $shortcode = false;

    /**
     * Stores the capability of the action
     *
     * @var string
     */
    protected $capability;

    /**
     * Page rules
     *
     * @var string[]
     */
    protected $rules;

    /**
     * Required parameters
     *
     * @var string[]
     */
    protected $params = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->login;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function isShortcode()
    {
        return $this->shortcode;
    }

    /**
     * @return string
     */
    public function getCapability()
    {
        return $this->capability;
    }

    /**
     * @return string[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return string[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Initialize the annotation.
     */
    public function initAnnotation(array $properties)
    {
        $this->map($properties, ['name']);

        parent::initAnnotation($properties);

        if (!isset($this->name)) {
            throw new AnnotationException('MbAction requires a name property');
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'ajax'       => $this->isAjax(),
            'capability' => $this->getCapability(),
            'login'      => $this->isLogin(),
            'method'     => $this->getMethod(),
            'name'       => $this->getName(),
            'params'     => $this->getParams(),
            'rules'      => $this->getRules(),
            'shortcode'  => $this->isShortcode(),
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