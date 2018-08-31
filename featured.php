<?php

/*

  Plugin Name: Simple featured posts
  Plugin URI: www.27technologies.com
  Description: this is a simple featured plugin
  Author: samad
  Author URI: www.27technologies.com/samad
  Text Domain: Simple Featured Post
  Version: 1.0.0
  License: GPL-3.0
  License URI: https://opensource.org/licenses/GPL-3.0 
 
  */


/* add checkbox */ 

function setup_featured_checkbox()
{
  $featured = get_post_meta( get_the_ID() );
   ?>

    <div class="sm-row-content">
      <label for="featured-checkbox">
        <input type="checkbox" name="featured-checkbox" id="featured-checkbox" value="yes"  <?php if( $featured['featured-checkbox'][0] =='yes' ) echo 'checked' ?> />
        Featured this post
      </label>
    </div>
    <?php
}

function add_featured_checkbox()
{
    add_meta_box("featured-checkbox", "Featured Post Option", "setup_featured_checkbox", "post");
}

add_action("add_meta_boxes", "add_featured_checkbox");


/* !add checkbox */


/* save/update checkbox */

function save_featured_checkbox(){ 
  if( isset( $_POST['featured-checkbox'] ) && $_POST['featured-checkbox'] == 'yes' ){
    update_post_meta(get_the_ID(), 'featured-checkbox' , 'yes');
  }else{
    update_post_meta(get_the_ID(),'featured-checkbox' , '');
  }
}

add_action( 'save_post', 'save_featured_checkbox' );

/* !save/update checkbox */



/* display in frontend */

function featured_shortcode(){

      /* getting url for pagination */ 

      global $wp;
      // print_r($wp->request);
      $current_url = home_url( $wp->request );
      // echo $current_url;

      $posi = strpos($current_url , '/page');

      (!empty($posi)) ? $not_first = substr($current_url,0,$posi) : $in_first = $current_url.'/page/' ;
      
      if($not_first) 
      {
        $not_first = $not_first.'/page/';
      }
      ($not_first) ? $finalurl = $not_first : $finalurl = $in_first ;
      // echo $finalurl;

       /* getting url for pagination */ 

       $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
      // echo $paged;
      $next_page_count = $paged + 1;
      $prev_count = $paged - 1;
      // echo $prev_count;
      $args = array(
        'posts_per_page' => 1,
        'order' => 'desc',
        'meta_key' => 'featured-checkbox',
        'meta_value' => 'yes',
        'paged'        => $paged
    );
    $featured = new WP_Query($args);

    // print_r($featured);
    
    if ($featured->have_posts()) { 

      while($featured->have_posts()){ $featured->the_post(); ?>

        <h3><strong><a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></strong></h3>

        <?php if (has_post_thumbnail()) { ?>
      
           <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a> 
      
            <p ><?php the_excerpt();?></p>
            
        <?php } ?>

      <div style="background: #eee;padding: 1rem;">
        
       <?php if($prev_count != 0 ) { ?>

            <nav style="float:left"><a href="<?php echo $finalurl.$prev_count; ?>" name="next_post">< Prev Post</a></nav>

            <?php } if($featured->max_num_pages >= $next_page_count ){ ?>

            <nav style="float:right"><a href="<?php echo $finalurl.$next_page_count; ?>" name="next_post">Next Post ></a></nav>
      
        <?php }     ?>
              <div style="clear:both">
      </div>

<?php
    }
    
  }
  
}

add_shortcode('featured-post', 'featured_shortcode');


/* ! display in frontend */