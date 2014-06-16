<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

function template_options()
{
  global $context, $settings, $options, $scripturl, $txt;

  $context['theme_options'] = array(
    array(
      'id' => 'return_to_post',
      'label' => $txt['return_to_post'],
      'default' => true,
    ),
    array(
      'id' => 'view_newest_first',
      'label' => $txt['recent_posts_at_top'],
      'default' => true,
    ),
    array(
      'id' => 'view_newest_pm_first',
      'label' => $txt['recent_pms_at_top'],
      'default' => true,
    ),
    array(
      'id' => 'copy_to_outbox',
      'label' => $txt['copy_to_outbox'],
      'default' => true,
    ),
    array(
      'id' => 'topics_per_page',
      'label' => $txt['topics_per_page'],
      'options' => array(
        0 => $txt['per_page_default'],
        5 => 5,
        10 => 10,
        25 => 25,
        50 => 50,
      ),
      'default' => true,
    ),
    array(
      'id' => 'messages_per_page',
      'label' => $txt['messages_per_page'],
      'options' => array(
        0 => $txt['per_page_default'],
        5 => 5,
        10 => 10,
        25 => 25,
        50 => 50,
      ),
      'default' => true,
    ),
  );
}

function template_settings()
{
  global $context, $settings, $options, $scripturl, $txt;

  $context['theme_settings'] = array(
    		array(
			'id' => 'page_transition_animation',
			'label' => 'Page transition animation',
			'options' => array(
				'none' => 'none',
				'fade' => 'fade',
				'pop' => 'pop',
				'flip' => 'flip',
				'turn' => 'turn',
				'flow' => 'flow',
				'slidefade' => 'slide-fade',
				'slide' => 'slide',
				'slideup' => 'slide up',
				'slidedown' => 'slide down',
			),
			'type' => 'text',
		),
  );
}

?>​