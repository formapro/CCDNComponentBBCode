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

namespace CCDNComponent\BBCode\Node\Lexeme\Tag\Asset;

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
class Youtube extends LexemeBase implements LexemeInterface
{
    /**
     *
     * @var string $canonicalLexemeName
     */
    protected static $canonicalLexemeName = 'YouTube';

    /**
     *
     * @var string $canonicalTokenName
     */
    protected static $canonicalTokenName = 'YOUTUBE';

    /**
     *
     * @var string $canonicalGroupName
     */
    protected static $canonicalGroupName = 'Asset';

    /**
     *
     * @var string $buttonLabel
     */
    protected static $buttonLabel = 'Youtube';

    /**
     *
     * @var string $buttonIcon
     */
    protected static $buttonIcon = '';

    /**
     *
     * @var string $buttonGroup
     */
    protected static $buttonGroup = array(
        'group' => 'webvideo',
        'order' => 0
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
    protected static $parametersAcceptedOnToken = array(0 => array(0 => 'video_id'));

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
    protected static $parametersRequiredOnToken = array(0 => array(0 => 'video_id'));

    /**
     *
     * Specify wether this tag is paired with another for
     * a successful lexing/validation match to take place.
     *
     * @var bool $isStandalone
     */
    protected static $isStandalone = true;

    /**
     *
     * Regular expressions to match against the
     * scan chunk during lexing process. The order
     * must match the $lexingHtml variable.
     *
     * @var array $lexingPattern
     */
    protected static $lexingPattern = array('/^\[YOUTUBE?(\=(.*?)*)\]$/');

    /**
     *
     * HTML to output at the index of the matching regular
     * expression found in the $lexingPattern variable.
     *
     * Indexes between $lexingPattern and $lexingHtml must match.
     *
     * @var array $lexingHtml
     */
    protected static $lexingHtml = array('</pre><center><iframe width="560" height="315" src="http://www.youtube.com/embed/{{ param[0] }}" frameborder="0" allowfullscreen></iframe></center><pre>');

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
    protected static $buttonParameterQuestion = "Enter Youtube Video ID";

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
            // 3. Store Parameter.
            $this->parameters[0] = $param[2];

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
        if (array_key_exists(0, $this->parameters)) {
            if ($this->parameters[0] !== null) {
                return true;
            }
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
            return str_replace('{{ param[0] }}', htmlentities($this->parameters[0], ENT_QUOTES, 'UTF-8'), static::$lexingHtml[$this->tokenIndex]);
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
