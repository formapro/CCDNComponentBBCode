<?php

/*
 * This file is part of the CCDNComponent BBCode
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNComponent\BBCode\Engine\Table;

use CCDNComponent\BBCode\Engine\Table\TableLexemes;

/**
 *
 * @category CCDNComponent
 * @package  BBCode
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNComponentBBCode
 *
 */
class TableACL
{
	protected $enableEditor;
	protected $enableParser;
	protected $groupWhiteList;
	protected $groupBlackList;
	protected $whiteList;
	protected $blackList;
	protected $table;
	
	public function __construct($enableEditor, $enableParser, $groupWhiteList, $groupBlackList, $whiteList, $blackList, $tableLexemes = null)
	{
		$this->enableEditor   = $enableEditor;
		$this->enableParser   = $enableParser;
		$this->groupWhiteList = $groupWhiteList;
		$this->groupBlackList = $groupBlackList;
		$this->whiteList      = $whiteList;
		$this->blackList      = $blackList;
		
		$this->table = new TableLexemes();
		
		if (null != $tableLexemes) {
			foreach ($tableLexemes as $lexeme) {
				$this->validate($lexeme);
			}
		}
	}
	
	/**
	 * 
	 * @access public
	 * @param mixed $lexeme
	 */
	public function validate($lexeme)
	{
		if (! in_array('CCDNComponent\BBCode\Node\NodeInterface', class_implements($lexeme))) {
			throw new \Exception('Lexeme must implement the NodeInterface');
		}
		
        $groupWhiteList = $this->groupWhiteList;
        $groupBlackList = $this->groupBlackList;
		
        $tagWhiteList = $this->whiteList;
        $tagBlackList = $this->blackList;

        // By default all tags can have nested content.
        // If a black list is defined, everything on the black-list is prevented from being nested.
        // If a white list is defined, groups on the white-list will override the black-list except individual tags.
        // To override a blacklisted group for a single tag, white list the tag.
        if (in_array($lexeme::getCanonicalGroupName(), $groupBlackList) || in_array('*', $groupBlackList) || in_array('*', $tagBlackList)) {
            if (in_array($lexeme::getCanonicalGroupName(), $groupWhiteList) || in_array($lexeme::getCanonicalTokenName(), $tagWhiteList)) {
                if (! in_array($lexeme::getCanonicalTokenName(), $tagBlackList) || in_array($lexeme::getCanonicalTokenName(), $tagWhiteList)) {
					$this->table->addLexeme($lexeme);
                }
             }
        } else {
            if (in_array($lexeme::getCanonicalGroupName(), $groupWhiteList) || in_array('*', $groupWhiteList) || in_array('*', $tagWhiteList)) {
                 if (! in_array($lexeme::getCanonicalTokenName(), $tagBlackList)) {
					 $this->table->addLexeme($lexeme);
                 }
             } else {
                if (in_array($lexeme::getCanonicalTokenName(), $tagWhiteList)) {
					$this->table->addLexeme($lexeme);
                }
             }
        }
	}

	/**
	 * 
	 * @access public
	 * @param string $canonicalLexemeTokenName
	 * @return bool
	 */
	public function hasCanonicalTokenName($canonicalLexemeTokenName)
	{
		return $this->table->hasCanonicalTokenName($canonicalLexemeTokenName);
	}

	/**
	 * 
	 * @access public
	 * @param string $lookupStr
	 * @return NodeInterface
	 */
	public function lookup($lookupStr)
	{
		$lexeme = $this->table->lookup($lookupStr);
		
		if ($lexeme == null) {
			return $this->createPlainText($lookupStr);
		} else {
			return $lexeme::createInstance($this, $lookupStr);
		}
	}
	
    /**
     *
     * @access public
     * @param string $lookupStr
     * @return NodeInterface
     */
	public function createPlainText($lookupStr)
	{
		$class = $this->table->getClassPlainText();
		
		return new $class($this, $lookupStr);
	}
	
    /**
     *
     * @access public
     * @return NodeTreeInterface
     */
	public function createNodeTree()
	{
		$class = $this->table->getClassNodeTree();
		
		return new $class($this);
	}
	
	/**
	 * 
	 * @access public
	 * @return array
	 */
	public function getClasses()
	{
		return $this->table->getClasses();
	}
	
	public function isEditorEnabled()
	{
		return $this->enableEditor;
	}
	
	public function isParserEnabled()
	{
		return $this->enableParser;
	}
	
	public function getTags()
	{
		$tagClasses = $this->table->getClasses();
		
		$tags = array();
		
		foreach ($tagClasses as $key => $tagClass) {
			$groupName = $tagClass::getCanonicalGroupName();
			
			if (! array_key_exists($groupName, $tags)) {
				$tags[$groupName] = array();
			}

			$buttonGroup = $tagClass::getButtonGroup();

			if (! array_key_exists('group', $buttonGroup)) {
				$buttonGroup['group'] = 'none';
			}
			
			if ($buttonGroup['group'] == '' || $buttonGroup['group'] == null) {
				$buttonGroup['group'] = 'none';
			}
			
			if (! array_key_exists('order', $buttonGroup)) {
				$buttonGroup['order'] = $key;
			}

			if (! array_key_exists($buttonGroup['group'], $tags[$groupName])) {
				$tags[$groupName][$buttonGroup['group']] = array();
			}

			$tags[$groupName][$buttonGroup['group']][$buttonGroup['order']] = $tagClass;
		}
		
		return $tags;
	}
	
	public function getTagsByGroup($groupNames)
	{
		$tags = $this->getTags();
		
		return $tags[$groupNames];
	}
}
