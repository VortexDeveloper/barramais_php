
	<?php if (have_posts()) : ?>
	
	<?php while (have_posts()) : the_post(); ?>
	<?php if(is_sticky(get_the_id())) { ?>
	<div class="amory_sticky">
	<?php } ?>
	<?php
	$postmeta = get_post_custom(get_the_id()); ?>
		
	
	<?php
	if ( has_post_format( 'gallery' , get_the_id())) { 
	?>
	<div class="slider-category">
		
		<div class="blogpostcategory">			
			<?php
				$attachments = '';
				add_filter( 'shortcode_atts_gallery', 'amory_gallery_latest' );
				function amory_gallery_latest( $out )
				{
					remove_filter( current_filter(), __FUNCTION__ );
					$out['size'] = 'amory-news';
					return $out;
				}
				$attachments =  get_post_gallery_images( $post->ID);
				if ($attachments) {?>
					<div id="slider-category" class="slider-category">
					<script type="text/javascript">
					jQuery(document).ready(function($){
						jQuery('.bxslider').bxSlider({
						  easing : 'easeInOutQuint',
						  captions: true,
						  speed: 800,
						   buildPager: function(slideIndex){
							switch(slideIndex){
							<?php
							$i = 0;
							foreach ($attachments as  $image_url) { 
								echo 'case '.$i.':return "<img src=\"'. esc_url( $image_url) .'\"";';
								$i++;
							} ?>									
							}
						  }
						});
					});	
					</script>	
						<ul id="slider" class="bxslider">
							<?php 
								foreach ($attachments as  $image_url) {
								

									?>	
										<li>
											<img src="<?php echo esc_url( $image_url) ?>" alt="<?php ?>"/>							
										</li>
										<?php } ?>
						</ul>

					</div>
				<?php } ?>
		<?php get_template_part('includes/boxes/loopBlogGrid','single'); ?>
		</div>
	</div>
	<?php } 
	if ( has_post_format( 'video' , get_the_id())) { ?>
	<div class="slider-category">
	
		<div class="blogpostcategory">			
			<?php
			
			if(!empty($postmeta["video_post_url"][0])) {
				$embed_code = wp_oembed_get(esc_url($postmeta["video_post_url"][0]));
				echo $embed_code ;
			} ?>
			<?php get_template_part('includes/boxes/loopBlogGrid','single'); ?>
		</div>
		
	</div>
	<?php } 
	if ( has_post_format( 'link' , get_the_id())) {
	$postmeta = get_post_custom(get_the_id()); 
	if(isset($postmeta["link_post_url"][0])){
		$link = $postmeta["link_post_url"][0];
	} else {
		$link = "#";
	}			
	?>
	<div class="link-category">
		<div class="blogpostcategory">	
			<?php if(amory_getImage(get_the_id(), 'amory-postBlock') != '') { ?>	

			<a class="overdefultlink" href="<?php echo esc_url($link) ?>">
			<div class="overdefult">
			</div>
			</a>

			<div class="blogimage">	
				<div class="loading"></div>		
				<a href="<?php echo esc_url($link) ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo amory_getImage(get_the_id(), 'amory-postBlock'); ?></a>
			</div>
			<?php } ?>					
			<?php get_template_part('includes/boxes/loopBlogLink','single'); ?>								
		</div>
	</div>
	
	<?php 
	} 	
	if ( has_post_format( 'quote' , get_the_id())) {?>
	<div class="quote-category">
		<div class="blogpostcategory">				
			<?php get_template_part('includes/boxes/loopBlogQuote','single'); ?>								
		</div>
	</div>
	
	<?php 
	} 			
	?>
	<?php if ( has_post_format( 'audio' , get_the_id())) {?>
	<div class="blogpostcategory">		
		<div class="audioPlayerWrap">
			<div class="audioPlayer">
				<?php 
				if(isset($postmeta["audio_post_url"][0]))
					echo do_shortcode('[soundcloud  params="auto_play=false&hide_related=false&visual=true" width="100%" height="150"]'. esc_url($postmeta["audio_post_url"][0]) .'[/soundcloud]') ?>
			</div>
		</div>
		<?php get_template_part('includes/boxes/loopBlogGrid','single'); ?>		
	</div>	
	<?php
	}
	?>					
	
	
	<?php if ( !get_post_format() ) {?>


	<div class="blogpostcategory">					
		<?php if(amory_getImage(get_the_id(), 'amory-postBlock') != '') { ?>	

			<a class="overdefultlink" href="<?php the_permalink() ?>">
			<div class="overdefult">
			</div>
			</a>

			<div class="blogimage">	
				<div class="loading"></div>		
				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo amory_getImage(get_the_id(), 'amory-postBlock'); ?></a>
			</div>
			<?php } ?>
			<?php get_template_part('includes/boxes/loopBlogGrid','single'); ?>
	</div>
	
	<?php } ?>		
	<?php if(is_sticky()) { ?>
		</div>
	<?php } ?>
	
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
				
	<?php endif; ?>

