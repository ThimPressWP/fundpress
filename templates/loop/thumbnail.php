<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php if( has_post_thumbnail() ):  ?>

	<div class="entry-thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( ); ?>
		</a>
	</div>

<?php endif; ?>