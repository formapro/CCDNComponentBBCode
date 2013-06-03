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

namespace CCDNComponent\BBCode\Node\Lexeme;

use CCDNComponent\BBCode\Engine\Table\TableACL;

use CCDNComponent\BBCode\Node\Lexeme\LexemeInterface;
use CCDNComponent\BBCode\Node\NodeBase;

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
 * @abstract
 *
 */
abstract class LexemeBaseStatic extends NodeBase
{
    /**
     *
     * @var bool $isLexable
     */
    protected static $isLexable = true;

    /**
     *
     * @var bool $isStandalone
     */
    protected static $isStandalone = true;

    /**
     *
     * @var int $tokenCount
     */
    protected static $tokenCount = 0;

    /**
     *
     * @var object $nestingACL
     */
	protected static $nestingACL;
	
    /**
     *
     * Sets up to initial operations that only need
     * to be run once and stored in a static context.
     *
     * @access public
     */
    public static function warmup(/*$tableLexemes*/)
    {
        static::$tokenCount = count(static::$lexingPattern);
    }
	
    /**
     *
     * @access public
     * @param  string          $lexingMatch
     * @return LexemeInterface
     */
    public static function createInstance($groupACL, $lexingMatch)
    {
        return new static($groupACL, $lexingMatch);
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalLexemeName()
    {
        return static::$canonicalLexemeName;
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalGroupName()
    {
        return static::$canonicalGroupName;
    }

    /**
     *
     * @access public
     * @return string
     */
    public static function getCanonicalTokenName()
    {
        return static::$canonicalTokenName;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function getScanPattern()
    {
        return static::$scanPattern;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function isLexable()
    {
        return static::$isLexable;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function getLexingPattern()
    {
        return static::$lexingPattern;
    }

    /**
     *
     * @access public
     * @return int
     */
    public static function getTokenCount()
    {
        return static::$tokenCount;
    }

    /**
     *
     * As we extend the NodeBase, we must state if we are
     * a tree node or a lexeme node, which is important
     * during both validation and rendering cascading.
     *
     * @access public
     * @return bool
     */
    public static function isTree()
    {
        return false;
    }

    /**
     *
     * @access public
     * @return int
     */
    public static function isStandalone()
    {
        return static::$isStandalone;
    }

    /**
     *
     * Will check the input string against the array of lexing
     * patterns in the form of regex strings to find a match.
     * Returns true immediately when match is found.
     *
     * @access public
     * @param  string $lookupStr
     * @return bool
     */
    public static function isPatternMatch($lookupStr)
    {
        $canonicalLookupStr = strtoupper($lookupStr);

        foreach (static::$lexingPattern as $pattern) {
            if (preg_match($pattern, $canonicalLookupStr)) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function subNodeGroupWhiteList()
    {
        return array(
            '*',
        );
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function subNodeGroupBlackList()
    {
        return array(

        );
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function subNodeWhiteList()
    {
        return array(
            '*',
        );
    }

    /**
     *
     * @access public
     * @return array
     */
    public static function subNodeBlackList()
    {
        return array(

        );
    }

    /**
     *
     * @access public
     * @param \CCDNComponent\BBCodeBundle\Component\Lexemes\LexemeInterface
     * @return bool
     */
    public static function childAllowed(LexemeInterface $lexeme)
    {
		if (! static::isStandalone()) {
			return static::$nestingACL->hasCanonicalTokenName($lexeme::getCanonicalTokenName());
		} else {
			return true;
		}
    }
	
	public static function cascadeACL($lexemeClass)
	{
		if (! static::isStandalone()) {
			if (static::$nestingACL == null) {
				static::$nestingACL = new TableACL(true, true, static::subNodeGroupWhiteList(), static::subNodeGroupBlackList(), static::subNodeWhiteList(), static::subNodeBlackList());
			}
		
			static::$nestingACL->validate($lexemeClass);
		}
	}
	
	public static function getNestableClasses()
	{
		if (! static::isStandalone()) {
			if (static::$nestingACL != null) {
				return static::$nestingACL->getClasses();
			}
		}
		
		return null;
	}
}
