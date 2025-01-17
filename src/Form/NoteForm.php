<?php

namespace Drupal\nt_note\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the note entity edit forms.
 */
class NoteForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New note %label has been created.', $message_arguments));
        $this->logger('nt_note')->notice('Created new note %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The note %label has been updated.', $message_arguments));
        $this->logger('nt_note')->notice('Updated note %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.note.collection');

    return $result;
  }

}
