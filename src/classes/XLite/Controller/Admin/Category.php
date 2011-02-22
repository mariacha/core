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
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    GIT: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Controller\Admin;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Category extends \XLite\Controller\Admin\Catalog
{
    /**
     * Default tabber page
     * 
     * @var    string
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $page = 'category_modify';

    /**
     * Tabber pages titles
     * 
     * @var    array
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $pages = array(
        'category_modify' => 'Add/Modify category',
    );

    /**
     * Tabber pages templates
     * 
     * @var    array
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $pageTemplates = array(
        'category_modify' => 'categories/add_modify_body.tpl',
    );


    /**
     * Controller initialization
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function init()
    {
        parent::init();

        if ('add' != $this->mode && 'modify' == $this->mode) {
            $this->pages['category_modify'] = $this->t('Modify category');

        } else {
            $this->pages['category_modify'] = $this->t('Add new category');
        }
    }

    /**
     * Return current (or default) category object
     *
     * @return \XLite\Model\Category
     * @access public
     * @since  3.0.0 EE
     */
    public function getCategory()
    {
        return ('add_child' === \XLite\Core\Request::getInstance()->mode) 
            ? new \XLite\Model\Category()
            : parent::getCategory();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getTitle()
    {
        return ('add_child' === \XLite\Core\Request::getInstance()->mode) 
            ? $this->t('Add category')
            : parent::getCategory()->getName();
    }


    /**
     * Common method to determine current location 
     * 
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getLocation()
    {
        return ('add_child' === \XLite\Core\Request::getInstance()->mode) 
            ? $this->t('Add category')
            : $this->t('Details');
    }

    /**
     * Create/update image
     *
     * @param integer $categoryId Image category ID OPTIONAL
     *
     * @return \XLite\Model\Image\Category\Image
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function saveImage($categoryId = null)
    {
        if (empty($categoryId)) {
            $categoryId = $this->getCategoryId();
        }   

        $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId);

        $img = $category->getImage();

        if (!$img) {
            $img = new \XLite\Model\Image\Category\Image();
        }   

        if ($img->loadFromRequest('postedData', 'image')) {
            if (!$img->getCategory())  {
                $img->setCategory($category);
                $category->setImage($img);
                \XLite\Core\Database::getEM()->persist($img);
            }   
        }   

        return $img;
    }

    /**
     * doActionAddChild 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionAddChild()
    {
        if ($properties = $this->validateCategoryData(true)) {
            $category = \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->insert(array('parent_id' => $this->getCategoryId()) + $properties);

            $this->saveImage($category->getCategoryId());

            $this->setReturnUrl($this->buildURL('categories', '', array('category_id' => $category->getCategoryId())));
        }
    }

    /**
     * doActionModify 
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionModify()
    {
        if ($properties = $this->validateCategoryData()) {

            $this->saveImage();

            \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->updateById($properties['category_id'], $properties);

            $this->setReturnUrl($this->buildURL('categories', '', array('category_id' => $properties['category_id'])));
        }
    }

    /**
     * Validate values passed from the REQUEST for updating/creating category 
     * 
     * @param boolean $isNewObject Flag - is a data for a new category or for updaing existing category OPTIONAL
     *  
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function validateCategoryData($isNewObject = false)
    {
        $postedData = \XLite\Core\Request::getInstance()->getData();

        $data = array();
        $isValid = true;

        $fieldsSet = array(
            'name',
            'description',
            'meta_tags',
            'meta_desc',
            'meta_title',
            'enabled',
            'membership_id',
            'clean_url',
            'show_title',
        );

        if (!$isNewObject) {
            $data['category_id'] = intval($postedData['category_id']);
        }

        foreach ($fieldsSet as $field) {

            if (isset($postedData[$field])) {
                $data[$field] = $postedData[$field];
            }

            // 'Clean URL' is optional field and must be a unique
            if ('clean_url' === $field && isset($data['clean_url'])) {

                $data['clean_url'] = $this->sanitizeCleanURL($data['clean_url']);

                if (!empty($data['clean_url']) && !$this->isCleanURLUnique($data['clean_url'], (!$isNewObject ? $data['category_id'] : null))) {

                    \XLite\Core\TopMessage::getInstance()->add(
                        $this->t('The Clean URL you specified is already in use. Please specify another Clean URL'),
                        \XLite\Core\TopMessage::ERROR
                    );

                    $isValid = false;
                }

            // 'Name' is a mandatory field
            } elseif ('name' === $field) {

                if (!isset ($data['name']) || 0 == strlen(trim($data['name']))) {

                    \XLite\Core\TopMessage::getInstance()->add(
                        $this->t('Not empty category name must be specified'),
                        \XLite\Core\TopMessage::ERROR
                    );

                    $isValid = false;
                }

            // 'Enabled' field value must be either 0 or 1
            } elseif ('enabled' === $field) {
                $data['enabled'] = isset($data['enabled']) && $data['enabled'] == '1' ? 1 : 0;
            }
        }

        return $isValid ? $data : false;
    }

    /**
     * Check - specified clean URL unique or not
     * 
     * @param string $cleanURL Clean URL
     *  
     * @return boolean
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isCleanURLUnique($cleanURL, $categoryId = null)
    {
        $result = \XLite\Core\Database::getRepo('XLite\Model\Category')->findOneByCleanUrl($cleanURL);

        return !isset($result)
            || (!is_null($categoryId) && intval($categoryId ) == intval($result->getCategoryId()));
    }
}
