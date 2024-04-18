<?php

namespace src\app\tree\data\tables;

use src\core\table\Table;

/**
 * -> what children nodes are allowed to this node
 * can be set by the author of the node
 * ,but sometimes it is also restricted by the type
 * or context.
 *
 * - question
 * - discussion
 * - ratings
 * - pred-market
 * - definitions
 * - subtrees
 * - rules
 * - contracts
 *
 */
class NodeContainer extends Table {

}