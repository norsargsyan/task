<?php

declare(strict_types=1);

namespace Drupal\nt_note\Service;

/**
 * Note Manager interface.
 */
interface NoteManagerInterface {

  /**
   * Load the current user notes.
   *
   * @return array
   *   Returns the array with current User notes.
   */
  public function getCurrentUserNotes(): array;

}
