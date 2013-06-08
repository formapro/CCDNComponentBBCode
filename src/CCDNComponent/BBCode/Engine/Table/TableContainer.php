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

use \CCDNComponent\BBCode\Engine\Table\TableACL;
use \CCDNComponent\BBCode\Engine\Table\TableLexemes;

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
class TableContainer
{
    /**
     *
     * @access protected
     */
	protected $lexemeClassesDefault = array(
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Image',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Vimeo',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Youtube',

        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\Code',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\CodeGroup',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\Quote',

        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Bold',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading1',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading2',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading3',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Italic',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Link',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListItem',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListOrdered',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListUnordered',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Strike',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\SubScript',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\SuperScript',
        '\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Underline',
    );
	
    /**
     *
     * @var array $tableLexemes
     */
	protected $lexemeClasses = array();
	
	/**
     *
     * @var string $plainText
     */
	protected $tableACL = array();
	
    /**
     *
     * @access public
     */
    public function __construct($acl = null, $lexemeClasses = null)
    {
		if ($lexemeClasses == null) {
			$lexemeClasses = $this->lexemeClassesDefault;
		}
		
		$this->setTableLexemes($lexemeClasses);
		
		if ($acl == null) {
			$acl = array(
				'default' => array(
					'enable_editor' => true,
					'enable_parser' => true,
					'group' => array(
						'white_list' => array('*'),
						'black_list' => array()
					),
					'tag' => array(
						'white_list' => array('*'),
						'black_list' => array()
					)
				)
			);
		}
		
		$this->setTableACL($acl);
    }
	
	public function setTableLexemes($lexemeClasses)
	{
		$this->lexemeClasses = array_merge($this->lexemeClasses, $lexemeClasses);
		
		foreach ($lexemeClasses as $lexeme) {
			$lexeme::warmup();
			
			foreach ($this->lexemeClasses as $nestable) {
				$lexeme::cascadeACL($nestable);
			}
		}
	}
	
	public function setTableACL($acls)
	{
		foreach ($acls as $name => $acl) {
			$this->addTableACL($name, $acl);
		}
	}
	
	public function addTableACL($name, $acl)
	{
		$this->tableACL[$name] = new TableACL(
			$acl['enable_editor'],
			$acl['enable_parser'],
			$acl['group']['white_list'],
			$acl['group']['black_list'],
			$acl['tag']['white_list'],
			$acl['tag']['black_list'],
			$this->lexemeClasses
		);
	}
	
	public function getTableACL($name)
	{
		if (! array_key_exists($name, $this->tableACL)) {
			throw new \Exception('ACL Table "' . $name . '" not found!');
		}
		
		return $this->tableACL[$name];
	}
}
