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
     * @static
     * @var bool $isLexable
     */
    protected static $isLexable = true;

    /**
     *
     * @static
     * @var bool $isStandalone
     */
    protected static $isStandalone = true;

    /**
     *
     * @static
     * @var int $tokenCount
     */
    protected static $tokenCount = 0;

    /**
     *
     * @static
     * @var object $nestingACL
     */
	protected static $nestingACL;
	
	/**
	 * 
	 * Question for BBCode Editor to prompt user for tag parameter.
	 */
	protected static $buttonParameterQuestion = "";
	
    /**
     *
     * Sets up to initial operations that only need
     * to be run once and stored in a static context.
     *
     * @static
     * @access public
     */
    public static function warmup()
    {
        static::$tokenCount = count(static::$lexingPattern);
    }
	
    /**
     *
     * @static
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
     * @static
     * @access public
     * @return string
     */
    public static function getCanonicalLexemeName()
    {
        return static::$canonicalLexemeName;
    }

    /**
     *
     * @static
     * @access public
     * @return string
     */
    public static function getCanonicalGroupName()
    {
        return static::$canonicalGroupName;
    }

    /**
     *
     * @static
     * @access public
     * @return string
     */
    public static function getCanonicalTokenName()
    {
        return static::$canonicalTokenName;
    }

    /**
     *
     * @static
     * @access public
     * @return array
     */
    public static function getScanPattern()
    {
        return static::$scanPattern;
    }

    /**
     *
     * @static
     * @access public
     * @return array
     */
    public static function isLexable()
    {
        return static::$isLexable;
    }

    /**
     *
     * @static
     * @access public
     * @return array
     */
    public static function getLexingPattern()
    {
        return static::$lexingPattern;
    }

    /**
     *
     * @static
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
     * @static
     * @access public
     * @return bool
     */
    public static function isTree()
    {
        return false;
    }

    /**
     *
     * @static
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
     * @static
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
     * @static
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
     * @static
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
     * @static
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
     * @static
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
     * @static
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
	
	/**
	 * 
	 * @static
	 * @access public
	 * @param mixed $lexemeClass
	 * @return array
	 */
	public static function cascadeACL($lexemeClass)
	{
		if (! static::isStandalone()) {
			if (static::$nestingACL == null) {
				static::$nestingACL = new TableACL(true, true, static::subNodeGroupWhiteList(), static::subNodeGroupBlackList(), static::subNodeWhiteList(), static::subNodeBlackList());
			}
		
			static::$nestingACL->validate($lexemeClass);
		}
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return array
	 */
	public static function getNestableClasses()
	{
		if (! static::isStandalone()) {
			if (static::$nestingACL != null) {
				return static::$nestingACL->getClasses();
			}
		}
		
		return null;
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return string
	 */
	public static function getButtonLabel()
	{
		return static::$buttonLabel;
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return string
	 */
	public static function getButtonIcon()
	{
		return static::$buttonIcon;
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return array
	 */
	public static function getButtonGroup()
	{
		return static::$buttonGroup;
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return string
	 */
	public static function getButtonParameterQuestion()
	{
		return static::$buttonParameterQuestion;
	}
}
