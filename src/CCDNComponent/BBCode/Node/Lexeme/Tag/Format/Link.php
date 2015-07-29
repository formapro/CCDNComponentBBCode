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

namespace CCDNComponent\BBCode\Node\Lexeme\Tag\Format;

use CCDNComponent\BBCode\Node\Lexeme\LexemeBase;
use CCDNComponent\BBCode\Node\Lexeme\LexemeInterface;

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
class Link extends LexemeBase implements LexemeInterface
{
    /**
     *
     * @var string $canonicalLexemeName
     */
    protected static $canonicalLexemeName = 'Link';

    /**
     *
     * @var string $canonicalTokenName
     */
    protected static $canonicalTokenName = 'URL';

    /**
     *
     * @var string $canonicalGroupName
     */
    protected static $canonicalGroupName = 'Format';

    /**
     *
     * @var string $buttonLabel
     */
    protected static $buttonLabel = 'Add Link';

    /**
     *
     * @var string $buttonIcon
     */
    protected static $buttonIcon = 'glyphicon glyphicon-globe';

    /**
     *
     * @var string $buttonGroup
     */
    protected static $buttonGroup = array(
        'group' => null,
        'order' => null
    );

    /**
     *
     * 1) First level index should match the token
     *    index that the parameter will be found in.
     * 2) Second level index should specify the
     *    order of the parameter.
     *
     * @var array $parametersAcceptedOnToken
     */
    protected static $parametersAcceptedOnToken = array(0 => array(0 => 'url'));

    /**
     *
     * These parameters will be mandatory. All parameters
     * specified here must also be reflected in the above
     * $parametersAcceptedOnToken and the index must match
     * must match the same index for each parameter in
     * before mentioned $parametersAcceptedOnToken.
     *
     * 1) First level index should match the token
     *    index that the parameter will be found in.
     * 2) Second level index should specify the
     *    order of the parameter.
     *
     * @var array $parametersRequiredOnToken
     */
    protected static $parametersRequiredOnToken = array(0 => array(0 => 'url'));

    /**
     *
     * Specify wether this tag is paired with another for
     * a successful lexing/validation match to take place.
     *
     * @var bool $isStandalone
     */
    protected static $isStandalone = false;

    /**
     *
     * Regular expressions to match against the
     * scan chunk during lexing process. The order
     * must match the $lexingHtml variable.
     *
     * @var array $lexingPattern
     */
    protected static $lexingPattern = array('/^\[URL?(\=(.*?)*)\]$/', '/^\[\/URL\]$/');

    /**
     *
     * HTML to output at the index of the matching regular
     * expression found in the $lexingPattern variable.
     *
     * Indexes between $lexingPattern and $lexingHtml must match.
     *
     * @var array $lexingHtml
     */
    protected static $lexingHtml = array('<a href="{{ param[0] }}" target="_blank" rel="nofollow">', '</a>');

    /**
     *
     * Specifies the array of other lexemes that
     * are permitted to be valid and rendered between
     * a matching pair of this particular lexeme.
     *
     * @var object $nestingACL
     */
    protected static $nestingACL;

    /**
     *
     * Calculated in LexemeBaseStatic::warmup method,
     * by number of indices found in $lexingPattern.
     *
     * @var int $tokenCount
     */
    protected static $tokenCount;

    /**
     *
     * Question for BBCode Editor to prompt user for tag parameter.
     */
    protected static $buttonParameterQuestion = "Enter URL";

    /**
     *
     * @access public
     * @return bool
     */
    public function extractParameters()
    {
        // 1. Extract Parameter.
        $symbols = '\d\w _,.?!@#$%&*()^=:\+\-\'\/';
        $regex = '/(\=\"(['.$symbols.']*)\"{0,500})/';

        $param = preg_split($regex, $this->lexingMatch, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

        // 2. Check Parameter meets some criteria.
        if (is_array($param) && count($param) > 2) {
            $protocol = preg_split('/^(http|https|ftp)\:\/\//', $param[2], null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

            // 3. Store Parameter.
            if ($protocol[0] == 'http' || $protocol[0] == 'https' || $protocol[0] == 'ftp') {
                $url = $param[2];
            } else {
                $url = 'http://' . $param[2];
            }

            $this->parameters[0] = $url;

            return true;
        }

        return false;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function areAllParametersValid()
    {
        if ($this->tokenIndex == 0) {
            if (array_key_exists(0, $this->parameters)) {
                if ($this->parameters[0] !== null) {
                    return true;
                }
            }
        } else {
            return true;
        }

        return false;
    }

    /**
     *
     * Renders the html from the $lexingHtml index matching
     * this nodes index from the $lexingPatterns index.
     *
     * @access public
     * @return string
     */
    public function cascadeRender()
    {
        if ($this->isValid(true)) {
            if ($this->tokenIndex == 0) {
                return str_replace('{{ param[0] }}', $this->parameters[0], static::$lexingHtml[$this->tokenIndex]);
            } else {
                return static::$lexingHtml[$this->tokenIndex];
            }
        }

        return $this->renderErrors();
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
            'Asset',
            'Block',
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
}
