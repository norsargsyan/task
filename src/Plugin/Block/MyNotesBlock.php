<?php

namespace Drupal\nt_note\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'My Notes' Block.
 *
 * @Block(
 *   id = "my_notes",
 *   admin_label = @Translation("My notes Block"),
 *   category = @Translation("Custom"),
 * )
 */
class MyNotesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * Creates a SystemBrandingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity form builder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entity_type_manager, EntityFormBuilderInterface $entity_form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFormBuilder = $entity_form_builder;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity.form_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $note = $this->entityTypeManager->getStorage('note')->create();
    $form = $this->entityFormBuilder->getForm($note, 'add');
    return [
      '#markup' => $this->t('<p>List of my notes</p>'),
      'form' => $form,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['note']);
  }

}
