<?php

namespace Drupal\nt_note\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\nt_note\NoteInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the note entity class.
 *
 * @ContentEntityType(
 *   id = "note",
 *   label = @Translation("Note"),
 *   label_collection = @Translation("Notes"),
 *   label_singular = @Translation("note"),
 *   label_plural = @Translation("notes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count notes",
 *     plural = "@count notes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\nt_note\NoteListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\nt_note\Form\NoteForm",
 *       "edit" = "Drupal\nt_note\Form\NoteForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\nt_note\Routing\NoteHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "note",
 *   admin_permission = "administer note",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/note",
 *     "add-form" = "/note/add",
 *     "canonical" = "/note/{note}",
 *     "edit-form" = "/note/{note}",
 *     "delete-form" = "/note/{note}/delete",
 *   },
 * )
 */
class Note extends ContentEntityBase implements NoteInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['priority'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Priority'))
      ->setSettings([
        'allowed_values' => [
          'high' => 'High',
          'medium' => 'Medium',
          'low' => 'Low',
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'list_default',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the note was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the note was last edited.'));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Owner'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
