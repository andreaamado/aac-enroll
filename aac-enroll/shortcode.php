<?php
/*
  Author: Andrea de Cerqueira
  Author URI: http://www.andreaamado.work
*/

function form_creation(){

    global $wpdb;
    
    if (isset($_REQUEST['send'])) {
        global $wpdb;
        $cad = $_REQUEST['cad'];
        //echo '<pre>'; print_r($cad); echo '</pre>'; exit();
    
        $idClean = null;
        $data = null;
        $type = null;
    
        $char_specials = array(".", "-", "/", "_");
        $idClean = str_replace($char_specials, "", $cad['id_number']);
    
        $birthPart = explode('/', $cad['birth']);
        $birth = $birthPart[2] . '-' . $birthPart[1] . '-' . $birthPart[0];
    
        // get doctor name
        $doctor = get_user_by('id', $cad['doctor']);
        //echo '<pre>'; print_r($doctor); echo '</pre>';
        $cad['doctor_email'] = $doctor->data->user_email;
        $cad['doctor_name'] = $doctor->data->display_name;
    
        $data = array(
                        'id_number' => $idClean,
                        'name' => $cad['name'],
                        'email' => $cad['email'],
                        'address' => $cad['address'],
                        'neighborhood' => $cad['neighborhood'],
                        'city' => $cad['city'],
                        'province' => $cad['province'],
                        'code' => $cad['code'],
                        'gender' => $cad['gender'],
                        'birth' => $birth,
                        'phone' => $cad['phone']
                     );
        $type = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
        $wpdb->insert('aac_enroll', $data, $type);
        //echo '<pre>'; print_r($wpdb); echo '</pre>'; exit;
    
        // se não tiver retornado erro na primeira inserção faz a segunda
        if($wpdb->show_errors == '') {
            $data2 = array(
                            'id_number' => $idClean,
                            'doctor' => $cad['doctor'],
                            'msg' => $cad['msg'],
                            'date' => date("Y-m-d")
                         );
            $type2 = array( '%s', '%s', '%s' );
            $wpdb->insert('aac_enroll_msg', $data2, $type2);
            //echo '<pre>'; print_r($wpdb); echo '</pre>'; exit;
            
            // finalizado com sucesso
            print("<script>alert('Done, wait for our contact. Thank you!');</script>");
        }
    
    } else if (isset($_REQUEST['edit'])) {
        global $wpdb;
        $cad = $_REQUEST['cad'];
        // echo '<pre>'; print_r($cad); echo '</pre>'; exit;
    
        $idClean = null;
        $data = null;
        $type = null;
    
        $char_specials = array(".", "-", "/", "_");
        $idClean = str_replace($char_specials, "", $cad['id_number']);
    
        $birthPart = explode('/', $cad['birth']);
        $birth = $birthPart[2] . '-' . $birthPart[1] . '-' . $birthPart[0];
    
        // get doctor name
        $doctor = get_user_by('id', $cad['doctor']);
        // echo '<pre>'; print_r($doctor); echo '</pre>';
        $cad['doctor_email'] = $doctor->data->user_email;
        $cad['doctor_name'] = $doctor->data->display_name;
        
        $data = array(
                        'name' => $cad['name'],
                        'email' => $cad['email'],
                        'address' => $cad['address'],
                        'neighborhood' => $cad['neighborhood'],
                        'city' => $cad['city'],
                        'province' => $cad['province'],
                        'code' => $cad['code'],
                        'gender' => $cad['gender'],
                        'birth' => $birth,
                        'phone' => $cad['phone']
                     );
        $ID = array( 'id_number' => $idClean );
        $type = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
        $wpdb->update('aac_enroll', $data, $ID, $type);
        // echo '<pre>'; print_r($wpdb); echo '</pre>'; exit;
    
        // if the first insertion did not return error, make the second
        if($wpdb->show_errors == '') {
            //$doctor->data->user_email
            $data2 = array(
                            'id_number' => $idClean,
                            'doctor' => $cad['doctor'],
                            'msg' => $cad['msg'],
                            'date' => date("Y-m-d")
                         );
            $type2 = array( '%s', '%s', '%s' );
            $wpdb->insert('aac_enroll_msg', $data2, $type2);
            // echo '<pre>'; print_r($wpdb); echo '</pre>'; exit;
            
            // finalizado com sucesso
            print("<script>alert('Done, wait for our contact. Thank you!');</script>");
        }
    }

    //list of doctors
    function aac_author_dropdown_list() {
        $users = get_users();
        if(empty($users)) {
            return;
        }
        
        echo'<select name="cad[doctor]">';
        // echo'<select name="cad[doctor]" onchange="this.form.submit()">';
        // echo '<option value="">select</option>';
        foreach( $users as $user ){
            if($_POST['cad']['doctor'] == $user->data->ID) {
                $select = "selected";
            } else {
                $select = null;
            }
            echo '<option value="' . $user->data->ID . '" ' . $select . '>' . $user->data->display_name . '</option>';
        }
        echo'</select>';
    }

    wp_enqueue_script('mask_script', plugin_dir_url( __FILE__ ) . 'jquery.mask.js', true); // set true to load in the footer
    wp_enqueue_script('aac_script', plugin_dir_url( __FILE__ ) . 'main.aac.js', true); // set true to load in the footer
    ?>

    <form name="enroll" method="post" action="<?php $PHP_SELF ?>" enctype="multipart/form-data">
        <div>
            <label for="cad[doctor]">Doctor</label>
            <?php aac_author_dropdown_list(); ?>
        </div>
        <div class="idBox">
            <label for="cad[id_number]">ID</label>
            <input name="cad[id_number]" type="text" id="cad_id_number" class="id_number" />
            <input type="button" value="Search" id="search_id" />
            <input type="hidden" value="<?php echo get_site_url(); ?>/form-enroll-plugin" id="form_url" />
        </div>
        <div id="results"></div>
    </form>
    <?php
}
add_shortcode('aac_enroll', 'form_creation');
// header('Content-Type: text/javascript; charset=UTF-8');
?>