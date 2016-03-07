<?php

namespace laxertu\DataTree;
use laxertu\DataTree\Processor\ProcessableInterface;

/**
 * Composite implementation
 *
 * Class DataTree
 * @package DataTree
 */

abstract class DataTree implements ProcessableInterface
{


    /** @var  DataTree */
    private $parent;

    /**
     * "Special" values are:
     *
     * NULL - means that class name will be used as node name
     * ''   - means that DataTree has no node name
     *
     * @var null
     */
    private $name = null;


    /**
     * DataTree raw content as array or raw text, null for composites.
     *
     * @var null | array | String
     */
    private $value = null;

    /** @var DataTree[] */
    private $elements = [];


    /**
     * Returns DataTree name
     *
     * @return string
     */
    final public function getName()
    {
        if (is_null($this->name)) {
            $this->name = end(explode('\\', get_class($this)));
        }

        return $this->name;
    }

    /**
     * Sets DataTree name. See attribute documentation
     * @param $name
     */
    final public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Sets a DataTree raw value
     *
     * @param $value String | array | null
     * @throws \Exception
     */
    final public function setValue($value = '')
    {
        if ($this->getChildren()) {
            throw new \Exception('Cannot set value of a composite DataTree');
        }


        $this->value = $value;
    }

    final public function getPathWithSeparator($separator = '/')
    {

        if ($this->parent) {
            return $this->parent->getPathWithSeparator($separator).$separator.$this->getName();
        } else {
            return $separator.$this->getName();
        }
    }



    /**
     * Sets $element as $pos child, overwrites existent if any
     *
     * This method is declared as protected as often we want to give control about how a tree is structured to
     * tree itself. If you want more flexibility you have to use / extend OpenDataTree.
     *
     * @param ProcessableInterface $element
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function setChild(ProcessableInterface $element, $pos)
    {
        if (!is_int($pos) || ($pos < 0)) {

            throw new \InvalidArgumentException('Pos have to be a positive integer');

        } elseif ($this->getValue()) {

            throw new \Exception('Cannot set a child if tree is a leaf one');
        } else {
            $element->setParent($this);
            $this->elements[$pos] = $element;
        }
    }

    final public function setParent(ProcessableInterface $parent)
    {
        $this->parent = $parent;
    }

    final public function getParent()
    {
        return $this->parent;
    }

    /**
     * This method is declared as protected as often we want to give control about how a tree is structured to
     * tree itself. If you want more flexibility you have to use / extend OpenDataTree.
     *
     * @param Integer $pos
     */
    protected function removeChild($pos)
    {
        unset($this->elements[$pos]);
    }

    /**
     * @return null|String|array
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * @return ProcessableInterface[]
     */
    final public function getChildren()
    {
        return $this->elements;
    }

}
