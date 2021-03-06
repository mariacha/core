<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 *
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Decorator
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

namespace Includes\Decorator\DataStructure\Graph;

/**
 * Classes
 *
 * @package XLite
 * @see     ____class_see____
 * @since   1.0.0
 */
class Classes extends \Includes\DataStructure\Graph
{
    /**
     * Reflection object
     *
     * @var    \ReflectionClass
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $reflection;

    /**
     * File path
     * 
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $file;

    /**
     * Flag for so called "low-level" nodes
     *
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $isLowLevelNode;

    /**
     * Flag for so called "top-level" stub nodes
     *
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $isTopLevelNode;

    /**
     * Flag to determine if node was changed (e.g. its key was modified)
     *
     * @var    boolean
     * @access protected
     * @see    ____var_see____
     * @since  1.0.0
     */
    protected $isChanged;

    // ------------------------------ Constructor and common getters -

    /**
     * Constructor
     *
     * @param string $key  Node unique key OPTIONAL
     * @param string $file File name OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct($key = self::ROOT_NODE_KEY, $file = null)
    {
        parent::__construct($key);

        $this->file = $file;
    }

    /**
     * Add child node
     *
     * @param self $node Node to add
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function addChild(self $node)
    {
        parent::addChild($node);

        $node->setParentClass($this->getClass());
    }

    /**
     * Alias
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getClass()
    {
        return $this->prepareClassName($this->getKey());
    }

    /**
     * Getter for the flag
     *
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isLowLevelNode()
    {
        return $this->isLowLevelNode;
    }

    /**
     * Getter for the flag
     *
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isTopLevelNode()
    {
        return $this->isTopLevelNode;
    }

    /**
     * Return name of parent class
     *
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getParentClass()
    {
        return $this->getReflection()->parentClass;
    }

    /**
     * Set name of parent class
     *
     * @param strng $class Class name to set
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.17
     */
    public function setParentClass($class)
    {
        $this->getReflection()->parentClass = $class;
    }

    /**
     * Return list of class implementing interfaces
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getInterfaces()
    {
        return $this->getReflection()->interfaces;
    }

    /**
     * Check if class implements interface
     *
     * @param string $interface Name of interface to check
     *
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isImplements($interface)
    {
        return in_array($this->prepareClassName($interface), $this->getInterfaces());
    }

    /**
     * Check if class decorates another one
     *
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isDecorator()
    {
        return $this->isImplements('XLite\Base\IDecorator');
    }

    /**
     * Return name of the module where the class defined
     *
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getModuleName()
    {
        return \Includes\Utils\ModulesManager::getModuleNameByClassName($this->getClass());
    }

    /**
     * Return top-level child
     *
     * @return self
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getTopLevelNode()
    {
        return $this->isDecorator() && ($children = $this->getChildren()) ? reset($children)->{__FUNCTION__}() : $this;
    }

    // ------------------------------ Methods to modify graph -

    /**
     * Set node key
     *
     * @param string $key Key to set
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setKey($key)
    {
        foreach ($this->getChildren() as $node) {
            $node->setParentClass($key);
        }

        parent::setKey($key);

        // Set flag
        $this->isChanged = true;
    }

    /**
     * Mark node as "low-level"
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setLowLevelNodeFlag()
    {
        $this->isLowLevelNode = true;
    }

    /**
     * Mark node as "top-level"
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setTopLevelNodeFlag()
    {
        $this->isTopLevelNode = true;
    }

    // ------------------------------ Methods to get paths and source code -

    /**
     * Name of the origin class file 
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Transform class name into the relative path
     *
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getPath()
    {
        return \Includes\Utils\Converter::getClassFile($this->getClass());
    }

    /**
     * Prepare source code of the class
     *
     * @param self $parent Parent node OPTIONAL
     *
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getSource(self $parent = null)
    {
        return $this->isChanged || $this->isDecorator()
            ? $this->getActualSource($parent)
            : ($this->isTopLevelNode() ? $this->getEmptySource($parent) : $this->getRegularSource());
    }

    /**
     * Actualize and return source code for node
     *
     * @param self $parent Parent node OPTIONAL
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getActualSource(self $parent = null)
    {
        $this->getReflection()->docComment = $this->isLowLevelNode() 
            ? '/**' . PHP_EOL . ' * MOVED' . PHP_EOL . ' */' 
            : null;

        return \Includes\Decorator\Utils\Tokenizer::getSourceCode(
            $this->getFile(),
            $this->getActualNamespace(),
            $this->getClassBaseName(),
            $this->getActualParentClassName($parent),
            $this->getReflection()->docComment
        );
    }

    /**
     * Return source code for "top-level" decorator node
     *
     * @param self $parent Parent node OPTIONAL
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getEmptySource(self $parent = null)
    {
        return '<?php' . PHP_EOL . PHP_EOL
            . (($namespace = $this->getActualNamespace()) ? ('namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL) : '')
            . (($comment = $this->getReflection()->docComment) ? ($comment . PHP_EOL) : '')
            . ($this->getReflection()->isFinal ? 'final '    : '')
            . ($this->getReflection()->isAbstract ? 'abstract ' : '')
            . ($this->getReflection()->isInterface ? 'interface' : 'class') . ' ' . $this->getClassBaseName()
            . (($class = $this->getActualParentClassName($parent)) ? (' extends ' . $class) : '')
            . (($interfaces = $this->getInterfaces()) ? (' implements \\' . implode(', \\', $interfaces)) : '')
            . PHP_EOL . '{' . PHP_EOL . '}';
    }

    /**
     * Return source code for regular node
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getRegularSource()
    {
        return \Includes\Utils\FileManager::read($this->getFile());
    }

    /**
     * Return actual parent class name
     *
     * @param self $parent Node to get class name
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getActualParentClassName(self $parent = null)
    {
        return ($parent && ($class = $parent->getClass())) ? ('\\' . $this->prepareClassName($class)) : null;
    }

    /**
     * Return namespace by class name
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getActualNamespace()
    {
        list(, $namespace) = $this->getClassNameParts();

        return $namespace ? $this->prepareClassName(implode('\\', $namespace)) : null;
    }

    /**
     * Return base part of the class name
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getClassBaseName()
    {
        list($basename, ) = $this->getClassNameParts();

        return $this->prepareClassName($basename);
    }

    /**
     * Parse class name into parts
     *
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getClassNameParts()
    {
        $parts = explode('\\', $this->getClass());

        return array(array_pop($parts), $parts);
    }


    // ------------------------------ Tags -

    /**
     * Get tag info
     *
     * @param string $name Tag name
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getTag($name)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getTags(), strtolower($name), true);
    }

    /**
     * Parse and return all tags
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getTags()
    {
        if (!isset($this->tags)) {
            $this->tags = \Includes\Decorator\Utils\Operator::getTags($this->getReflection()->docComment);
        }

        return $this->tags;
    }

    // ------------------------------ Auxiliary methods -

    /**
     * Return the ReflectionClass object for the current node
     *
     * @return \ReflectionClass
     * @access public
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getReflection()
    {
        if (!isset($this->reflection)) {
            $this->reflection = new \StdClass();
            $util = '\Includes\Decorator\Utils\Tokenizer';

            if ($util::getDecoratorFlag()) {
                $util = '\Includes\Decorator\Utils\Tokenizer';

                $this->reflection->parentClass = $util::getParentClassName($this->getFile());
                $this->reflection->interfaces  = $util::getInterfaces($this->getFile());
                $this->reflection->docComment  = $util::getDockBlock($this->getFile());
                $this->reflection->isFinal     = $util::getFlag($this->getFile(), T_FINAL);
                $this->reflection->isAbstract  = $util::getFlag($this->getFile(), T_ABSTRACT);
                $this->reflection->isInterface = (bool) $util::getInterfaceName($this->getFile());

                // :KLUDGE: the "StaticRoutines" plugin support
                $this->reflection->hasStaticConstructor = $util::hasMethod(
                    $this->getFile(),
                    \Includes\Decorator\Plugin\StaticRoutines\Main::STATIC_CONSTRUCTOR_METHOD
                );

            } elseif (\Includes\Utils\Operator::checkIfClassExists($this->getClass())) {
                $reflection = new \ReflectionClass($this->getClass());

                $this->reflection->parentClass = ($class = $reflection->getParentClass()) ? $class->getName() : null;
                $this->reflection->interfaces  = (array) $reflection->getInterfaceNames();
                $this->reflection->docComment  = $reflection->getDocComment();
                $this->reflection->isFinal     = $reflection->isFinal();
                $this->reflection->isAbstract  = $reflection->isAbstract();
                $this->reflection->isInterface = $reflection->isInterface();

                // :KLUDGE: the "StaticRoutines" plugin support
                $this->reflection->hasStaticConstructor = $reflection->hasMethod(
                    \Includes\Decorator\Plugin\StaticRoutines\Main::STATIC_CONSTRUCTOR_METHOD
                );
            }

            $this->reflection->parentClass = $this->prepareClassName($this->reflection->parentClass);
            $this->reflection->interfaces  = array_map(array($this, 'prepareClassName'), $this->reflection->interfaces);
        }

        return $this->reflection;
    }

    /**
     * Prepare class name
     *
     * @param string $class Class name to prepare
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareClassName($class)
    {
        return \Includes\Utils\Converter::trimLeadingChars($class, '\\');
    }

    /**
     * For additional info
     *
     * @param self $node Current node
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.17
     */
    protected function drawAdditional(self $node)
    {
        $result = parent::drawAdditional($node);

        if ($node->getParentClass()) {
            $result .= ' <i>(' . $node->getParentClass() . ')</i>';
        }

        return $result;
    }
}
