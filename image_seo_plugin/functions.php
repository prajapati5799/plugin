<?php
add_action('wp_ajax_update_data', 'imageseo_thumbnail_fetch');
add_action('wp_ajax_nopriv_update_data', 'imageseo_thumbnail_fetch');
function imageseo_thumbnail_fetch()
{
  
  $box_data = $_POST['box_data'];
  //       echo "<pre>";
  // print_r($box_data);
  // echo "</pre>";
  // die('ddd');
 
  $query_images_args = array(
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => -1,
    
  );

  $query_images = new WP_Query($query_images_args);

  $max_pages = $query_images->max_num_pages;
  while ($query_images->have_posts()) {
    $query_images->the_post();
    global $post;
    $attachmentid = $post->ID;

    $image_title = get_post_field('post_title', $attachmentid, true);
    $alt_text = get_post_field('_wp_attachment_image_alt', $attachmentid, true);
    $image_url = wp_get_attachment_url($attachmentid);
    $my_image_meta = array(
      'ID'    => $attachmentid,      // Specify the image (ID) to be updated
      'post_title'  => $image_title,    // Set image Title to sanitized title
      'post_alt'  => $alt_text,    // Set image Caption (Excerpt) to sanitized title

    );

    


      foreach ($box_data as $box_data_list) {

        $newAlt = '';
  //       echo "<pre>";
  // print_r($box_data);
  // echo "</pre>";
  // die('ddd');
        $img_title = get_post_field('post_title', $box_data_list['attid'], true);
        $newURL = wp_get_attachment_url($box_data_list['attid']);
        $path_parts = pathinfo($newURL);
        $image_name = $path_parts['filename'];
        if (!empty($box_data_list['cAlt'])) {
          update_post_meta($box_data_list['attid'], '_wp_attachment_image_alt', $box_data_list['cAlt']);
        } else {
          if ($img_title) {
            $newAlt = $img_title;
          } else {
            $newAlt = $image_name;
          }
          update_post_meta($box_data_list['attid'], '_wp_attachment_image_alt', $newAlt);
        }
        if (!empty($box_data_list['cTitle'])) {
          $data = array(
            'ID' => $box_data_list['attid'],
            'post_title' => $box_data_list['cTitle'],
          );
          wp_update_post($data);
        } else {
          $data = array(
            'ID' => $box_data_list['attid'],
            'post_title' => $image_name,
          );
          wp_update_post($data);
        }
      }
    

    echo 'The image has been updated';
  }
  wp_die();
}



