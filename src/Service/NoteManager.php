<?php

declare(strict_types=1);

namespace Drupal\nt_note\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Link management service.
 */
class NoteManager implements NoteManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * Constructs Ejp Links service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;

  }

  /**
   * Load current User Notes.
   */
  public function getCurrentUserNotes(): array {
    return $this->entityTypeManager->getStorage('note')->loadByProperties([
      'uid' => $this->currentUser->id(),
    ]);
  }

}
