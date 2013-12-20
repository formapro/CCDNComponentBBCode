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
class TableLexemes
{
    /**
     *
     * @var array $lexemeClasses
     */
    protected $lexemeClasses = array();

    /**
     *
     * Fully-qualified-namespace for NodeTree type.
     *
     * @var string $classNodeTree
     */
    protected $classNodeTree = '\CCDNComponent\BBCode\Node\Tree\NodeTree';

    /**
     *
     * Fully-qualified-namespace for PlainText Lexeme type.
     *
     * @var string $classPlainText
     */
    protected $classPlainText = '\CCDNComponent\BBCode\Node\Lexeme\Tag\PlainText';

    /**
     *
     * @access public
     */
    public function __construct(array $lexemeClasses = null)
    {
        if (null == $lexemeClasses) {
            $lexemeClasses = array();
        }

        $this->setLexemes($lexemeClasses);
    }

    public function setLexemes($lexemeClasses)
    {
        foreach ($lexemeClasses as $lexemeClass) {
            $this->addLexeme($lexemeClass);
        }
    }

    public function addLexeme($lexemeClass)
    {
        if (in_array($lexemeClass, $this->lexemeClasses)) {
            return;
        }

        $this->lexemeClasses[$lexemeClass::getCanonicalTokenName()] = $lexemeClass;
    }

    public function hasCanonicalTokenName($canonicalLexemeTokenName)
    {
        if (array_key_exists($canonicalLexemeTokenName, $this->lexemeClasses)) {
            return true;
        }

        return false;
    }

    /**
     *
     * @access public
     * @param  string          $lookupStr
     * @return LexemeInterface
     */
    public function lookup($lookupStr)
    {
        $lookupStrCanonical = strtoupper($lookupStr);

        foreach ($this->lexemeClasses as $lexeme) {
            if ($lexeme::isPatternMatch($lookupStr)) {
                return $lexeme;
            }
        }

        return null;
    }

    public function getClassNodeTree()
    {
        return $this->classNodeTree;
    }

    public function getClassPlainText()
    {
        return $this->classPlainText;
    }

    /**
     *
     * @access public
     * @return array
     */
    public function getClasses()
    {
        return $this->lexemeClasses;
    }
}
