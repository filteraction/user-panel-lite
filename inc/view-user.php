<?php 
$user_id = intval($_GET['user_id']); 
$author_obj = get_user_by('id', $user_id);
$img = get_user_meta($user_id, 'picture', true);
$img = ($img)? $img['url']: esc_url( plugins_url( '../img/NoImage-180x180.jpg', __FILE__ ) );
$business_name = get_user_meta($user_id, 'business_name', true);
$register_phone = get_user_meta($user_id, 'register_phone', true);
$register_phone2 = get_user_meta($user_id, 'register_phone2', true);
$state = get_user_meta($user_id, 'state', true);
$city = get_user_meta($user_id, 'city', true);
// $city = implode(",", $city);
$service= get_user_meta($user_id, 'service', true);
// $service = implode(",", $service);
$suburb = get_user_meta($user_id, 'suburb', true);
$certificate = get_user_meta($user_id, 'certificate', true);
?>
<div class="wrap">

  <div class="container">
    <div class="panel-group">
      <div class="panel panel-default" style="padding: 7px;">
        <div class="panel-heading">
          <div class="fb-profile">
            <img align="left" class="fb-image-lg" src="<?php echo esc_url( plugins_url( '../img/background.png', __FILE__ ) );?>" alt="Profile image example"/>
            <img align="left" class="fb-image-profile thumbnail" src="<?php echo $img;?>" alt="Profile image example"/>
            <div class="fb-profile-text">
              <h1>
                <?php echo $author_obj->display_name;?>
              </h1>
              <h4> 
                <span class="fa fa-envelope-o">
                </span> &nbsp;&nbsp;
                <?php echo $author_obj->user_email;?>
              </h4>
            </div>
          </div>
        </div>
        <br>
        <div class="clearfix">
        </div>
        <!-- <div style="padding: 0px 50px 0px 50px;"> -->
        <div class="row">
          <div class="col-md-4">    
            <div class="panel-group">
              <div class="panel panel-primary">
                <div class="panel-heading">Business Name
                </div>
                <div class="panel-body">
                  <?php echo $business_name;?>
                </div>
              </div>
              <div class="panel panel-primary">
                <div class="panel-heading">Service
                </div>
                <div class="panel-body">
                  <?php echo $service;?>
                </div>
              </div> 
             
            </div>
          </div> 

           <div class="col-md-4">    
            <div class="panel-group">
             <div class="panel panel-info">
                <div class="panel-heading">State
                </div>
                <div class="panel-body">
                  <?php echo $state;?>
                </div>
              </div>
              <div class="panel panel-info">
                <div class="panel-heading">Suburb
                </div>
                <div class="panel-body">
                  <?php echo $city;?>
                </div>
              </div> 
            </div>
          </div>





          <div class="col-md-4">
            <div class="panel-group">
            <div class="panel panel-success">
                <div class="panel-heading">Contact No.
                </div>
                <div class="panel-body">
                  <?php echo $register_phone;?>
                </div>
              </div>
              <div class="panel panel-success">
                <div class="panel-heading">Alternate No.
                </div>
                <div class="panel-body">
                  <?php echo $register_phone2;?>
                </div>
              </div>

            </div>
          </div>
        </div>

              <div class="row">
                <div class="col-md-12">
                <div class="panel-group">
                 <div class="panel panel-primary">
                  <div class="panel-heading"><i class="fa fa-file-pdf-o">
          </i> &nbsp; Certificates &nbsp; 
                </div>
                 <div class="panel-body">
                <?php
                $current_user = wp_get_current_user();

                $certificates = get_user_meta( $user_id,'certificates', false);
                if ( $certificates[0] ) {
                foreach ( $certificates[0] as $attachment_id ) {
                $thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                $full_size = wp_get_attachment_url( $attachment_id );
                $filetype = wp_check_filetype($full_size);
                ?>

                <div class="col-md-4">
                 
                  <div class="form-group">
                   
                    <?php
                   if($filetype['ext']=='pdf' ){ ?>
                     <a href="<?php echo $full_size;?>" target="_blank">
                    <img class="img-responsive img-thumbnail" alt="" src="<?php echo esc_url( plugins_url( '../img/pdf-thumb.png', __FILE__ ) );?>"></a>
                  <?php  }
                  elseif($filetype['ext']=='docx'|| $filetype['ext']=='doc'){ ?>
                  <a href="<?php echo $full_size;?>" target="_blank">
                    <img class="img-responsive img-thumbnail" alt="" src="<?php echo esc_url( plugins_url( '../img/word-thumb.jpg', __FILE__ ) );?>"></a>
                 <?php  }

                   else{ ?>
                     <a href="<?php echo $full_size;?>" target="_blank">
                    <img class="img-responsive img-thumbnail" alt="" src="<?php echo $full_size;?>"></a>
                  <?php } ?>

                  </div>
                </div>
                <?php }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- </div> -->
      </div>
    </div>
  </div> 
  <!-- /container -->  
</div>
