<?php

namespace Drupal\nt_note\Plugin\FormAlter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\nt_note\Service\NoteManager;
use Drupal\pluginformalter\Plugin\FormAlterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Note form alter.
 *
 * @FormAlter(
 *   id = "nt_note_form_alter",
 *   label = @Translation("Alters all Note forms."),
 *   form_id = {
 *    "note_add_form",
 *   },
 * )
 *
 * @package Drupal\nt_note\Plugin\FormAlter
 */
class NoteFormAlter extends FormAlterBase {

  /**
   * The Note manager.
   *
   * @var \Drupal\nt_note\Service\NoteManager
   */
  protected $noteManager;

  /**
   * Creates a SystemBrandingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\nt_note\Service\NoteManager $note_manager
   *   The note manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, NoteManager $note_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->noteManager = $note_manager;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('nt_note.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface &$form_state, $form_id) {
    $form['actions']['submit']['#ajax'] = [
      'callback' => [$this, 'submitCallback'],
      'wrapper' => $form['#id'],
    ];
    $form['note_list'] = $this->collectNoteList();

  }

  /**
   * Form submission handler for "Save" button.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitCallback(array $form, FormStateInterface $form_state) {
    // If there is error messages don't rebuild the form.
    if (!empty($form_state->getErrors())) {
      return $form;
    }
    $form['note_list'] = $this->collectNoteList();
    return $form;
  }

  /**
   * Collect render array for note list.
   */
  private function collectNoteList() {
    $notes = $this->noteManager->getCurrentUserNotes();
    // Case when there is no Notes for current user.
    if (empty($notes)) {
      return [
        '#markup' => $this->t("You don't have any notes."),
        '#weight' => -10,
      ];
    }

    $rows = [];
    foreach ($notes as $note) {
      $deleteRenderer = [
        'data' => [
          '#type' => 'link',
          '#title' => $this->t('Delete'),
          '#url' => Url::fromRoute('entity.note.delete_form', ['note' => $note->id()]),
          '#options' => [
            'attributes' => [
              'class' => [
                'use-ajax',
              ],
              'data-dialog-options' => Json::encode([
                'width' => 700,
                'minHeight' => 500,
              ]),
              'data-dialog-type' => 'modal',
            ],
          ],
        ],
      ];

      $rows[] = [
        $note->get('title')->value,
        Markup::create($note->get('description')->value),
        $note->get('priority')->value,
        $deleteRenderer,
      ];
    }

    return [
      '#type' => 'table',
      '#header' => [
        'title' => $this->t('title'),
        'description' => $this->t('Description'),
        'note' => $this->t('Priority'),
        'action' => $this->t('Action'),
      ],
      '#rows' => $rows,
      '#weight' => -10,
    ];
  }

}
