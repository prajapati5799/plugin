<?php
/**
 * Form Plugin
 * Plugin Name: Image SEO
 * Version:     1.0.0
 * Text Domain: imageseo_plugin
 * Domain Path: /languages
 * Requires PHP: 5.2.4
 */
/**
 * Register the "book" custom post type
 */

// cass and js enqueue
function imageseo_enqueue_scripts()
{
  wp_enqueue_style('style_image', plugin_dir_url(__FILE__) . '/assets/css/style.css');
  // wp_enqueue_style('style_sanjay_2', plugin_dir_url(__FILE__) . '/assets/css/style2.css');
  wp_enqueue_style('style_bootstrapcss_1', plugin_dir_url(__FILE__) . '/assets/css/bootstrap.min.css');
  wp_enqueue_script('style_bootstrapjs_1', plugin_dir_url(__FILE__) . '/assets/js/bootstrap.min.js', array('jquery'), '', true);
  wp_enqueue_script('bootstrap-bundle-min', plugin_dir_url(__FILE__) . '/assets/js/bootstrap.bundle.min.js', array('jquery'), '', true);
  wp_enqueue_script('custom_imageseojs', plugin_dir_url(__FILE__) . '/assets/js/custom.js', array('jquery'), '', true);
  wp_localize_script('custom_imageseojs', 'localajax_1', array('ajaxurl' => admin_url('admin-ajax.php'), ));
  wp_enqueue_script('jquery', plugin_dir_url(__FILE__) . '/assets/js/jquery.min.js', array('jquery'), '', true);
}
add_action('admin_enqueue_scripts', 'imageseo_enqueue_scripts');

//  add custome Option page
add_action('init', 'replace_alt_title_myplugin');
add_action('admin_menu', 'imageseo_replace_alt_title_options_page');
function imageseo_replace_alt_title_options_page()
{
  $main_page = add_options_page(
    'Image SEO', // page <title>Title</title>
    'Image SEO Friendly', // menu link text
    'manage_options', // capability to access the page
    'imageseo_page_slug', // page URL slug
    'imageseo_replace_alt_title_page_content', // callback function with content
   
  );
  add_action('admin_print_styles-' . $main_page, 'imageseo_enqueue_scripts');
}


//plugin inside form
function imageseo_replace_alt_title_page_content()
{

  $i = 0;
  ?>
  <div class="container">
    <div class="row">
      <?php screen_icon(); ?>
      <h2>Replace Alt Text Title </h2>
      <?php settings_fields('myplugin_options_group'); ?>
      <?php
      $query_images_args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
      );

      $query_images = new WP_Query($query_images_args);
      echo '<pre>';
          print_r($query_images);
          echo '</pre>';
          exit;
      $max_pages = $query_images->max_num_pages;
      $front_data = array();
      while ($query_images->have_posts()) {
        $query_images->the_post();
        global $post;
        $attachmentid = $post->ID;
        $image_title = get_post_field('post_title', $attachmentid, true);
        $alt_text = get_post_field('_wp_attachment_image_alt', $attachmentid, true);
        $image_url = wp_get_attachment_url($attachmentid);
        
        ?>
        <?php
        if (empty($alt_text) || empty($image_title)) {
          
          
          $front_data[] = array(
            
            'attachmentid' => '<div class="col-md-3 box_data_input" data-attid="' . $attachmentid . '">
                                <div class="news-box">
                                  <div class="img ">
                                    <img class="img_seo_box" src="' . $image_url . '" alt="' . $alt_text . '">
                                  </div>
                                  <div class="info">
                                    <div class="news-title">
                                      <input type="text" placeholder="Title :" class="cTitle" name="cTitle" value="' . $image_title . '">
                                    </div>
                                    <div class="post-col">
                                      <input type="text" placeholder="Alt Text :" class="cAlt" name="cAlt" value="' . $alt_text . '">
                                    </div>
                                  </div>
                                </div>
                              </div>',
          ); 
          $i++;
        }

        ?>
      <?php }
      $data_max = 0;
      if (!empty($front_data)) {
        $chunk_data = array_chunk($front_data, 4);
        $data_max = count($chunk_data);
        $front_data = json_encode($chunk_data);
      } else {
        ?>
        <h2 class="data_not_available" id="hide_update">No Result Found</h2>
      <?php }


      ?>
      <ul class="data_class_1" id="my-repeater-list-id"></ul>
    </div>
    <div class="row">

      <div class="show_more_main">
        <h6 class="number_center">Total number of images <?php echo $i; ?></h6>
        <!-- <a style="display:none;" data-max="<?php echo $data_max - 1; ?>" data-current="0" id="my-repeater-show-more-link" class="ni_show_more_btn show_more_div">Show More</a> -->
        <button type="button" style="display:none;" data-max="<?php echo $data_max - 1; ?>" data-current="0"
          id="my-repeater-show-more-link" class="ni_show_more_btn show_more_div">Load More</button>
      </div>
      <div class="update_data">
        <!-- <a style="display:block" data-max="<?php echo $data_max - 1; ?>" data-current="0" id="update_link" class="ni_show_more_btn show_more_div" >update</a> -->
        <button type="button" style="display:block" data-max="<?php echo $data_max - 1; ?>" data-current="0"
          id="update_link" class="ni_show_more_btn show_more_div">Update</button>
      </div>

    </div>
    <script type="text/javascript">
      function set_front_data_html(front_data) {
        jQuery(front_data).each(function (e) {
          jQuery(this).each(function (e) {
            jQuery('#my-repeater-list-id').append(this.attachmentid).append(this.image_url).append(this.alt_text).append(this.image_title);

          });

        });
      }
      const front_datas = <?php echo $front_data; ?>;
      jQuery(document).ready(function (e) {
        if (front_datas.length > 0) {
          set_front_data_html(front_datas[0]);
        }
        if (front_datas.length > 1) {
          jQuery('#my-repeater-show-more-link').show();
        }
        if (jQuery('#hide_update').css('display') == 'block') {
          jQuery('#update_link').css('display', 'none');
        }
        jQuery('#my-repeater-show-more-link').click(function (e) {
          jQuery('#my-repeater-list-id').css({
            opacity: '0.5',
            cursor: 'not-allowed'
          });
          var max_page = jQuery(this).attr('data-max');
          var data_current = jQuery(this).attr('data-current');
          data_current++;
          set_front_data_html(front_datas[data_current]);
          jQuery(this).attr('data-current', data_current);
          if (max_page == data_current) {
            jQuery('#my-repeater-show-more-link').hide();
          }
          jQuery('#my-repeater-list-id').css({
            opacity: '',
            cursor: ''
          });

        });
      });
    </script>
  </div>
<?php
}
require 'functions.php';