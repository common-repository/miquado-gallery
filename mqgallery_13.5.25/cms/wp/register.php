<?php

// #######################################################################################
//               Widgets
// #######################################################################################

class MGGalleryWidget extends WP_Widget {

  function MGGalleryWidget() {
    // Instantiate the parent object
    parent::WP_Widget( false, 'Miquado Gallery' );
  }

  function widget( $args, $instance ) {
    if(isset($instance['params']) && '' < $instance['params']){
      $params = $instance['params'];
    }else{
      $params = '';
    }
    
    if(!isset($instance['widgettype'])) $instance['widgettype'] = '';
    switch($instance['widgettype']){
      case 'navigation':
        $output = MQGallery::getNavigation($params);
        break;
      case 'thumbs':
        if(defined('MQGalleryMain') 
        && 'external'==MQGConfig::$showthumbs
        && NULL!== MQGallery::$activeGallery){
          $output = MQGallery::getThumbs($params);
        }else{
          $output = '';
        }
        break;
      case 'cartsummary':
        $output = MQGallery::getCartsummary($params);
        break;
    }
     // Widget Output (Nur wenn Inhalt vorhanden);
    if(''==trim($output)){
      echo '';
    }else{
      echo '<aside class="widget">';
      if(isset($instance['title']) && '' < $instance['title']){
        echo '<h3 class="widget-title">'.$instance['title'].'</h3>';
      }
      echo $output;
      echo '</aside>';
    }
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['widgettype'] = strip_tags( $new_instance['widgettype'] );
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['params'] = strip_tags($new_instance['params']);
    return $instance;
  }
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = __( 'New title', 'text_domain' );
    }
    echo '<p>'.
         '<label for="'.$this->get_field_id('title').'">'.
         _e( 'Title:' ).'</label>'. 
         '<input class="widefat" id="'.$this->get_field_id('title').
         '" name="'.$this->get_field_name('title').'" type="text" value="'.
         esc_attr( $title ).'" /></p>';

    // Auswahl
    if(isset($instance['widgettype'])){
      $widgettype = $instance['widgettype'];
    }else{
      $widgettype = __('','text_domain');
    }
    $a = array(
      'navigation'=>'Navigation',
      'thumbs'=>'Thumbs',
      'cartsummary'=>'Cart Summary',
    );
    echo '<p>'.
      '<label for="'.$this->get_field_id('widgettype').'">'.
      _e('Type:').'</label>'.
      '<select id="'.$this->get_field_id('widgettype').'"'.
      '" name="'.$this->get_field_name('widgettype').'">';
    foreach($a as $key=>$value){
      if(esc_attr($widgettype) == $key){
        $s = ' selected="selected"';
      }else{
        $s = '';
      }
      echo '<option value="'.$key.'"'.$s.'>'.$value.'</option>';
    }
    echo '</select></p>';
    
    if(isset($instance['params'])){
      $params = $instance['params'];
    }else{
      $params = __('','text_domain');
    }
    echo '<p>'.
      '<label for="'.$this->get_field_id('params').'">'.
      _e('Params:').'</label>'.
      '<input class="widefat" id="'.$this->get_field_id('params').
      '" name="'.$this->get_field_name('params').'" type="text"'.
      ' value="'.esc_attr($params).'" /></p>';
  }

}

function mqgallery_register_widgets() {
  register_widget( 'MGGalleryWidget' );
}
add_action( 'widgets_init', 'mqgallery_register_widgets' );

// ########################################################################
// Media Button              
// #######################################################################################

function wp_mqgallery_media_button($context) {
  ob_start();
  include MQGallery::getDir('app').'cms/wp/module.php';
  echo ob_get_clean();
}
add_action('media_buttons','wp_mqgallery_media_button',100);



