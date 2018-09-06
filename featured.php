<?php

/*

  Plugin Name: Simple featured posts
  Plugin URI: www.27technologies.com
  Description: Simple featured posts is a simple featured plugin to show featured posts using the shortcode [featured-post type="post-type"]
  Author: samad
  Author URI: www.27technologies.com/samad
  Text Domain: featured
  Version: 1.0.0
  License: GPL-3.0
  License URI: https://opensource.org/licenses/GPL-3.0 
 
  */

 defined('ABSPATH') or die();

 
/* adding styles*/
  function load_featured_styles(){

    $featured_dir = plugin_dir_url( __FILE__ );
    wp_enqueue_style('custom-style', $featured_dir . 'assets/css/custom.css');
    // wp_enqueue_style('admin-style', $featured_dir . 'admin/assets/css/admin.css');
  
  }
  add_action('wp_enqueue_scripts', 'load_featured_styles');

/* !adding styles*/
function load_featured_scripts(){
  $featured_dir = plugin_dir_url( __FILE__ );  
  wp_enqueue_script('custom-script', $featured_dir . 'assets/js/custom.js');
}
add_action('wp_enqueue_scripts', 'load_featured_scripts');







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
  $args = array(
                    'public'   => true,
                    '_builtin' => false
                );

 $post_types = get_post_types( $args); 

  foreach ( $post_types as $post_type ) {
      add_meta_box("featured-checkbox", "Featured Post Option", "setup_featured_checkbox", ['post', $post_type] );
   }

    
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


function featured_shortcode( $arg_post_type ){
       /* getting url for pagination */ 

       $selected_option = get_option( 'choosen_post_type' );

      //  echo $selected_option;
      //  die();

        if(!$arg_post_type || $arg_post_type['type'] == ''){ 
          $arg_post_type = ['type' => 'post'];
          // print_r($arg_post_type);
      }
       $arg_post_type_value = shortcode_atts( ['type' => $selected_option],
                                              $arg_post_type,
                                              'featured-post'
                                            );
                                  


        if( $arg_post_type['type'] != 'post'){
        $args = array(
                            'public'   => true,
                            '_builtin' => false
                      );
        
         $post_types = get_post_types( $args); 
        
          foreach ( $post_types as $post_type ) {
              if($arg_post_type['type'] != $post_type){
                get_404_template();
                echo "<h2>Please check the shortcode argument! - ".$arg_post_type['type']."</h2>";
                die();
              }
           }
          }

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
        'posts_per_page' => 2,
        'order' => 'desc',
        'meta_key' => 'featured-checkbox',
        'meta_value' => 'yes',
        'paged'        => $paged,
        // 'post_type' => $selected_option
        'post_type' => array('testimonial', 'post')
    );
    $featured = new WP_Query($args);
    
    // print_r($featured);
    
    if ($featured->have_posts()) { 

      while($featured->have_posts()){ $featured->the_post(); ?>

        <h3><strong><a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></strong></h3>

        <?php if (has_post_thumbnail()) { ?>
      
           <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a> 
      
            <p><?php the_excerpt();?></p>
            
        <?php }
    }
    
  }

  ?>
   <!-- pagination -->
   <nav class="cust-paginate">
       
       <?php
       $start = 0;
       $start = $paged - 1;
       if($prev_count != 0 ) { ?>
         
         <a class="cust-page-numbers" href="<?php echo $finalurl.$prev_count; ?>" title="Previous Post"> < </a>
         <?php 
         if($start == 2) {  ?>
         <a class="cust-page-numbers" href="<?php echo $finalurl.'0'; ?>" title="First Post"> 1 </a>
         <?php
          }
         }
         $total_count = $featured->max_num_pages;
         
         $end = $paged + 1;
         
         if($start > 2 ){
           echo '<a class="cust-page-numbers" href="'.$finalurl.'0'.'" title="First Post"> 1 </a><span class="dots">...</span>' ;
          }
           for ($i=1; $i < $featured->max_num_pages + 1; $i++) {
                        
             if($i >= $start && $end >= $i) {
               echo '<a href="'.$finalurl.$i.'" class="cust-page-numbers '.(($i == $paged) ? 'current' : '') .'" title="Post '.$i.'">' .$i. '</a>';
             } 
       } 
       if($end < $total_count - 1 ){echo '<span class="dots">...</span>';  }
         
         if($featured->max_num_pages >= $next_page_count ){ 
           
           if($end != $total_count){ ?>
           <a class="cust-page-numbers" href="<?php echo $finalurl.$featured->max_num_pages; ?>" title="Last Post"> <?php echo $total_count; ?> </a>
           <?php }?>
           <a class="cust-page-numbers" href="<?php echo $finalurl.$next_page_count; ?>" title="Next Post"> > </a>
           
     
       <?php }
         ?>

         <label for="jumpTo" class="lblJump"> Jump to Page:
         <input id="jumpTo" type="number" min="1" max="<?php echo $total_count; ?>" value="<?php echo $paged ?>" class="jump-text" onchange="jumpToPage()
          ">
          </label>
          <input type="hidden" value="<?php echo $finalurl; ?>" id="hiddenTxt">
       </nav>

       <!-- !pagnation -->
  <?php
}

add_shortcode('featured-post', 'featured_shortcode');


/* ! display in frontend */



/**
 * *******************************************
 * ************* admin side ******************
 * *******************************************
 */ 


 /* admin page */

add_action( 'admin_menu', 'featured_admin_page_register' );


function featured_admin_page_register(){

  add_menu_page('Featured Post', 'Featured Post', 'manage_options', 'featured-post', 'featured_post_render', 'dashicons-format-aside');

}

//register settings
add_action( 'admin_init', 'register_featured_post_settings' );

function register_featured_post_settings() {

    register_setting( 'featured-post-choose-post-type', 'choosen_post_type' );

  }


function featured_post_render(){
  global $title;
  print '<h2>'.$title.' Settings'.'</h2>';
  $args = array(
    'public'   => true,
    '_builtin' => false
);

$post_types = get_post_types($args);
  ?>
      <form method="post" action="options.php">
          <?php settings_fields( 'featured-post-choose-post-type' ); ?>
          <?php do_settings_sections( 'featured-post-choose-post-type' ); ?>
          <table class="form-table">
            <tr valign="top">
      
            <td>
                  <!-- <label for="choosen_post_type"> Choose a Post Type
                  <select id="choosen_post_type" name="choosen_post_type">
                      <?php 
                      // array_push($post_types,"post");
                      // foreach ( $post_types as $post_type ) {
                      //     echo '<option value="'.$post_type.'" '.((get_option( 'choosen_post_type' ) == $post_type ) ? 'selected' : '' ).' >'.$post_type.'</option>';
                      // }
                      ?>
                  </select>
                </label> -->

                <p><strong > Choose From available Post Types </strong ></p>
                       
                      <?php 
                      array_push($post_types,"post");
                      foreach ( $post_types as $post_type ) {
                       echo ' <p>';
                          // echo '<input name="choosen_post_type" type="checkbox" id="'.$post_type.'" value="'.$post_type.'" '.((get_option( 'choosen_post_type' ) == $post_type ) ? 'checked' : '' ).' > ';
                          echo '<input name="choosen_post_type" type="checkbox" id="'.$post_type.'" value="'.$post_type.'" '.((get_option( 'choosen_post_type' ) == $post_type ) ? 'checked' : '' ).' > ';
                          echo '<label for="'.$post_type.'">'.$post_type.'<lable>';
                          // echo '<option value="'.$post_type.'" '.((get_option( 'choosen_post_type' ) == $post_type ) ? 'selected' : '' ).' >'.$post_type.'</option>';
                         echo '</p>';
                        }
                       ?>
                <!-- </p> -->
              </td>
              </tr>
          </table>
      <?php submit_button(); ?>
      </form>
  
  <?php } 



/* !admin page */


/* settings link in plugin   */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'featured_post_action_links' );

function featured_post_action_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=featured-post') ) .'">Settings</a>';
   return $links;
}

/* settings link in plugin   */


/* styles */

function featured_admin_style(){

  $featured_dir = plugin_dir_url( __FILE__ );

  wp_enqueue_style('admin-style', $featured_dir.'admin/assets/css/admin.css');

} 

add_action('admin_enqueue_scripts', 'featured_admin_style');




/**
 * *******************************************
 * ************* !admin side ******************
 * *******************************************
 */ 