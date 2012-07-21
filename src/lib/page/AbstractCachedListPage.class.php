<?php
namespace wcf\page;
use wcf\system\cache\CacheHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\util\ClassUtil;

/**
 * Provides functionality for cached lists.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage page
 * @category Ultimate CMS
 */
abstract class AbstractCachedListPage extends SortablePage {
    
    /**
     * Contains the fully qualified name of the CacheBuilder.
     * @var string
     */
    public $cacheBuilderClassName = '';
    
    /**
     * Contains the name of the cache.
     * @var string
     */
    public $cacheName = '';
    
    /**
     * Contains the read cache objects.
     * @var array
     */
    public $objects = array();
    
    /**
     * @see \wcf\page\SortablePage::readData()
     */
    public function readData() {
        //calling SortablePage methods
        $this->validateSortOrder();
        $this->validateSortField();
        
        AbstractPage::readData();
        
        //calling MultipleLinkPage methods
        $this->initObjectList();
        $this->calculateNumberOfPages();
        
        //calling own methods
        $this->loadCache();
    }
    
    /**
     * Loads the cache for the list.
     * To use a custom path please overwrite this method <br />and replace WCF_DIR with the wanted application dir.
     *
     * @param string $path the application path; default WCF_DIR
     */
    public function loadCache($path = WCF_DIR) {
        //call loadCache event
        EventHandler::getInstance()->fireEvent($this, 'loadCache');
        
        if (!ClassUtil::isInstanceOf($this->cacheBuilderClassName, 'wcf\system\cache\builder\ICacheBuilder')) {
            throw new SystemException("Class '".$this->cacheBuilderClassName."' does not implement 'wcf\system\cache\builder\ICacheBuilder'");
        }
        
        $file = $path.'cache/cache.'.$this->cacheName.'.php';
        CacheHandler::getInstance()->addResource(
            $this->cache,
            $file,
            $cacheBuilderClassName
        );
        $this->objects = CacheHandler::getInstance()->get($cache);
    }
    
    /**
     * @see \wcf\page\MultipleLinkPage::countItems()
     */
    public function countItems() {
        // call countItems event
		EventHandler::getInstance()->fireAction($this, 'countItems');
		
        return count($this->objects);
    }
    
    /**
     * @see \wcf\page\SortablePage::assignVariables()
     */
    public function assignVariables() {
        AbstractPage::assignVariables();
        
        // assign sorting parameters
        // overwrite MultipleLinkPage objects assignment
		WCF::getTPL()->assign(array(
			'sortField' => $this->sortField,
			'sortOrder' => $this->sortOrder,
			'objects' => $this->objects
		));
    }
}
