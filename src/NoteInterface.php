<?php

namespace Drupal\nt_note;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a note entity type.
 */
interface NoteInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
