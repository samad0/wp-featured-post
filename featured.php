<?php

/*

  Plugin Name: Simple featured posts
  Plugin URI: www.27technologies.com
  Description: this is a simple featured plugin
  Author: samad
  Author URI: www.27technologies.com/samad
  Text Domain: featured
  Version: 1.0.0
  License: GPL-3.0
  License URI: https://opensource.org/licenses/GPL-3.0 
 
  */



/* adding styles*/

  function load_featured_styles(){
    $featured_dir = plugin_dir_url( __FILE__ );
    wp_enqueue_style('custom-style', $featured_dir . 'assets/css/custom.css');
  }
  add_action('wp_enqueue_scripts', 'load_featured_styles');

/* !adding styles*/



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
      

       /* getting url for pagination */ 

       $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
      
      $next_page_count = $paged + 1;
      $prev_count = $paged - 1;
      // echo $paged;
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

        <!-- pagination -->
        <nav class="cust-paginate">
        <?php if($prev_count != 0 ) { ?>
          <a class="cust-page-numbers" href="<?php echo $finalurl.'0'; ?>" title="First Post"> |< </a>
          <a class="cust-page-numbers" href="<?php echo $finalurl.$prev_count; ?>" title="Previous Post"> < </a>

          <?php }
          $total_count = $featured->max_num_pages;
          $start = 0;
          $start = $paged - 2;
              $end = $paged + 2;
          if( $paged == $total_count || $start > 1 ){echo '<span class="dots">...</span>' ;}
            for ($i=1; $i < $featured->max_num_pages + 1; $i++) {
                         
              if($i >= $start && $end >= $i) {
                echo '<a href="'.$finalurl.$i.'" class="cust-page-numbers '.(($i == $paged) ? 'current' : '') .'" title="Post '.$i.'">' .$i. '</a>';
              } 
        } 
        if($paged <=  $total_count - 3){echo '<span class="dots">...</span>' ;}
          
          if($featured->max_num_pages >= $next_page_count ){ ?>

            <a class="cust-page-numbers" href="<?php echo $finalurl.$next_page_count; ?>" title="Next Post"> > </a>
            <a class="cust-page-numbers" href="<?php echo $finalurl.$featured->max_num_pages; ?>" title="Last Post"> >| </a>
      
        <?php }
          ?>
        </nav>

        <!-- !pagnation -->
      
<?php
    }
    
  }
  
}

add_shortcode('featured-post', 'featured_shortcode');


/* ! display in frontend */