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
class Lexer
{
    /**
     *
     * @var LexemeTable $lexemeTable
     */
    protected $lexemeTable;

    /**
     *
     * @var array $scanChunks
     */
    protected static $scanChunks;

    /**
     *
     * @var int $scanChunksSize
     */
    protected static $scanChunksSize;

    /**
     *
     * @var int $scanChunksIndex
     */
    protected static $scanChunksIndex;

    /**
     *
     * @access public
     * @param  array $scanChunks
     * @return array
     */
    public function process($scanChunks, $table)
    {
        $this->table = $table;

		$tokens = $this->tokenise($scanChunks);
        $tree = $this->lexify($tokens);
		
		ld($tokens);
		
		$dirtyBranches = 0;
		while ($dirtyBranches > 0) {
			$dirtyBranches = 0;
	        $tree = $this->lexify($tree);

			foreach ($tree as $branch) {
				if ($branch->isOpeningTag() && !$token::isStandalone()) {
					$dirtyBranches++;
				}
			}
		}

		ldd($tree);

        $tree->cascadeValidate();

        return $tree;
    }

	protected function tokenise($scanChunks)
	{
		$tokens = array();

        foreach ($scanChunks as $scanStr) {
            $tokens[] = $this->table->lookup($scanStr);
		}
		
		return $tokens;
	}

    /**
     *
     * Iterates over the scan chunks, and recursively calls itself for each opening
     * lexeme it matches. A return from the last recursive call takes place when
     * both a matching lexeme (closing half) is found and that the current tree
     * has a parent. The iterator is used in a static context to maintain iterations
     * through recursion. Each recursive call creates a new tree that once returned
     * is appended to a node of the last tree.
     *
     * @access protected
     * @param NodeTreeInterface $parent
     * @param NodeInterface     $node
     */
    protected function lexify($tokens)
    {
		$tree = array();
		$tmpTree = null;
		$lastOpen = null;
		
		foreach ($tokens as $id => $token) {

			if ($token->isOpeningTag() && !$token::isStandalone()) {
				if (! $lastOpen) {
					if ($token::getCanonicalLexemeName() != 'PlainText') {
						$lastOpen = $token;
						$tmpTree = array();
					}
				} else {
					// start again, nothing found, so dump tmpTree into main tree.
					$lastOpen = $token;
					foreach ($tmpTree as $branch) {
						$tree[] = $branch;
					}
					
					$tmpTree = null;
				}
			}

			if ($lastOpen) {
				$tmpTree[] = $token;
				if ($token->isClosingTag() && $token::getCanonicalLexemeName() == $lastOpen::getCanonicalLexemeName()) {
					$subTree = $this->table->createNodeTree();

					foreach ($tmpTree as $branch) {
						$subTree->addNode($branch);
					}
					
					$tree[] = $subTree;
					$subTree = null;
					$tmpTree = null;
				}
			} else {
				$tree[] = $token;
			}
		}
		
		// Catch anything left in tmp tree, and append it to what we have.
		// We will make it a subnode to prevent it from being relexed into
		// infinitely, because clearly something is missing, i.e a closing tag.
		if ($tmpTree) {
			$subTree = $this->table->createNodeTree();
			foreach ($tmpTree as $branch) {
				$subTree->addNode($branch);
			}
		}
		
		return $tree;
    }
}
