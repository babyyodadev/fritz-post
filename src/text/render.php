<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$posts = true;

if ($posts !== false) {
	?>
	<div <?php echo get_block_wrapper_attributes(); ?>>
		<?php echo $attributes['text']; ?>
	</div>
	<?php
} else {
	?>
	<p <?php echo get_block_wrapper_attributes(); ?>>
		<?php echo "No Posts" ?>
	</p>
	<?php
}
?>
