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

namespace CCDNComponent\BBCode\Engine;

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
class Scanner
{
    /**
     *
     * @access public
     * @param  string $input
     * @return array
     */
    public function process($input)
    {
        $scanChunks = $this->scan($input);

        return $scanChunks;
    }

    /**
     *
     * @access protected
     * @param  string $input
     * @return array
     */
    protected function scan($input)
    {
        // Scan the input and break it down into possible tags and body text.
        $symbols = '\d\w _,.?!@#$%&*()^=:\+\-\'\/';

        $regex = '/(\[(?:\/|:)?[\d\w]{1,10}(?:\=(?:\"[' . $symbols . ']*\"|[' . $symbols . '])){0,500}?:?\])/';

        $scanChunks = preg_split($regex, $input, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

        return $scanChunks;
    }
}
