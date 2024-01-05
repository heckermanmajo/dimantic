<?php

namespace cls\data\space;
#
use cls\DataClass;

/**
 * User can create a task with a bounty.
 *
 * Then other users submit a solution and
 * the best solution is picked by the creator of the task.
 *
 * The creator of the task can also pick multiple solutions.
 *
 * Then the bounty is distributed to the users who submitted
 * the picked solution(s).
 *
 */
class Task extends DataClass {

  var int $space_id = 0;

  var int $author_id = 0;


  /**
   * Returns the sum of all the prestige points
   * that are set as bounty for this task.
   * @return int
   */
  function get_all_prestige_points_for_this_task(): int {
    # todo: collect from Transaction table
    return 0;
  }

}