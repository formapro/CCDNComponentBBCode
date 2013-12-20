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

namespace CCDNComponent\BBCode;

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
class Bootstrap
{
    /**
     *
     * @access private
     */
    protected $tableContainer;

    /**
     *
     * @access protected
     */
    protected $scanner;

    /**
     *
     * @access protected
     */
    protected $lexer;

    /**
     *
     * @access protected
     */
    protected $parser;

    /**
     *
     * @access private
     * @param LexemeTable $lexemeTable
     * @param Scanner     $scanner
     * @param Lexer       $lexer
     * @param Parser      $parser
     */
    public function __construct($tableContainer = null, $scanner = null, $lexer = null, $parser = null)
    {
        if (null == $tableContainer) {
            $tableContainer = new \CCDNComponent\BBCode\Engine\Table\TableContainer();
        }

        if (null == $scanner) {
            $scanner = new \CCDNComponent\BBCode\Engine\Scanner();
        }

        if (null == $lexer) {
            $lexer = new \CCDNComponent\BBCode\Engine\Lexer();
        }

        if (null == $parser) {
            $parser = new \CCDNComponent\BBCode\Engine\Parser();
        }

        $this->tableContainer = $tableContainer;

        $this->scanner = $scanner;

        $this->lexer = $lexer;

        $this->parser = $parser;
    }

    /**
     *
     * @access public
     * @return string $html
     */
    public function process($input, $tableName = null)
    {
        if ($tableName == null) {
            $tableName = 'default';
        }

        $table = $this->getTableACL($tableName);

        if ($table->isParserEnabled()) {
            // Split input string by likely tag format.
            $scanChunks = $this->scanner->process($input, $table);

            // Create a symbol tree via the lexer.
            $symbolTree = $this->lexer->process($scanChunks, $table);

            // Parse the lexed symbol tree to get an HTML output.
            $html = $this->parser->process($symbolTree, $table);
        } else {
            $html = '<pre>' . htmlentities($input, ENT_QUOTES) . '</pre>';
        }

        return $html;
    }

    public function getTableACL($tableName)
    {
        return $this->tableContainer->getTableACL($tableName);
    }
}
