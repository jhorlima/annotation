<?php

namespace MbAnnotation\standard;

use Illuminate\Contracts\Support\Arrayable;
use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use MocaBonita\MocaBonita;
use MocaBonita\tools\MbPage as Page;
use Serializable;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('class' => true, 'inherited' => false)
 */
class MbPage extends Annotation implements Arrayable, Serializable
{
    /**
     * Stores the capability of the action
     *
     * @var string
     */
    protected $capability = "manage_options";

    /**
     * Page slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Page dashicon
     *
     * @var string
     */
    protected $dashicon = "dashicons-editor-code";

    /**
     * Page menu position
     *
     * @var int
     */
    protected $position = 1;

    /**
     * Page parent
     *
     * @var string
     */
    protected $parent;

    /**
     * Remove page from submenu when available
     *
     * @var bool
     */
    protected $submenu = true;

    /**
     * Store if page is main menu
     *
     * @var bool
     */
    protected $menu = true;

    /**
     * Page rules
     *
     * @var string[]
     */
    protected $rules;

    /**
     * @return string
     */
    public function getCapability()
    {
        return $this->capability;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getDashicon()
    {
        return $this->dashicon;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function isSubmenu()
    {
        return $this->submenu;
    }

    /**
     * @return bool
     */
    public function isMenu()
    {
        return $this->menu;
    }

    /**
     * @return string[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Initialize the annotation.
     */
    public function initAnnotation(array $properties)
    {
        $this->map($properties, ['slug']);

        parent::initAnnotation($properties);

        if (!isset($this->slug)) {
            throw new AnnotationException('MbPage requires a slug property');
        }
    }

    /**
     * @param MocaBonita $mocaBonita
     * @param string     $controller
     *
     * @return Page
     */
    public function mocaBonita(MocaBonita $mocaBonita, $controller)
    {
        $mbPage = Page::create($this->getSlug());

        $mbPage->setController($controller)
            ->setCapability($this->getCapability())
            ->setDashicon($this->getDashicon())
            ->setHideMenu(!$this->isMenu())
            ->setRules($this->getRules())
            ->setRemovePageSubmenu(!$this->isSubmenu());

        if (!is_null($this->getParent())) {
            $mocaBonita->getMbPage($this->getParent())->setSubPage($mbPage);
            $mocaBonita->addSubMbPage($mbPage);
        } else {
            $mocaBonita->addMbPage($mbPage);
        }

        return $mbPage;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'capability' => $this->getCapability(),
            'dashicon'   => $this->getDashicon(),
            'menu'       => $this->isMenu(),
            'parent'     => $this->getParent(),
            'position'   => $this->getPosition(),
            'rules'      => $this->getRules(),
            'slug'       => $this->getSlug(),
            'submenu'    => $this->isSubmenu(),
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