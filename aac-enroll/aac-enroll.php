<?php
/*
Plugin Name: Enroll
Version: 1.4
Description: List of patients.
Author: Andrea de Cerqueira
Author URI: http://www.andreaamado.work
Text Domain: aac-enroll
Copyright (c) 2014
*/

require_once dirname( __FILE__ ) . '/pagination.php';
require_once dirname( __FILE__ ) . '/shortcode.php';

global $wpdb, $base_page, $buscaResul;
$base_name = plugin_basename('aac-enroll/aac-enroll.php');
$base_page = 'admin.php?page=mt_toplevel_enroll_handle&' . $base_name;
$base_page_msg = 'admin.php?page=mt_sublevel_enroll_handle&' . $base_name;
$base_page_busca = 'admin.php?page=mt_sublevel_enroll_search_handle&' . $base_name;

//create mysql tables -----------------------
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$sql = "CREATE TABLE `aac_enroll` (
        `id_number` bigint(11) UNSIGNED NOT NULL,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) DEFAULT NULL,
        `address` varchar(255) DEFAULT NULL,
        `neighborhood` varchar(100) DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `province` varchar(2) DEFAULT NULL,
        `code` varchar(9) DEFAULT NULL,
        `gender` varchar(1) DEFAULT NULL,
        `birth` date DEFAULT NULL,
        `phone` varchar(14) DEFAULT NULL,
        PRIMARY KEY (`id_number`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
dbDelta($sql);

$sql = "CREATE TABLE `aac_enroll_msg` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_number` bigint(11) NOT NULL,
        `doctor` bigint(20) NOT NULL,
        `msg` longtext NOT NULL,
        `date` date NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
dbDelta($sql);

//add new patient -----------------------
if (isset($_REQUEST['add'])) {
    $char_specials = array(".", "-", "/", "_");
    $id_number = str_replace($char_specials, "", $_REQUEST['id_number']);
    
    $birthPart = explode('/', $_REQUEST['birth']);
    $birth = $birthPart[2] . '-' . $birthPart[1] . '-' . $birthPart[0];
    
    //check if id exists in db
    $sql = "SELECT id_number FROM aac_enroll WHERE id_number='$id_number'";
    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0){
        $query = "INSERT INTO aac_enroll (id_number, name, email, address, neighborhood, city, province, code, gender, birth, phone)
                  VALUES ('" . $id_number . "', '" . $_REQUEST['name'] . "', '" . $_REQUEST['email'] . "', '" . $_REQUEST['address'] . "', '" . $_REQUEST['neighborhood'] . "', '" . $_REQUEST['city'] . "', '" . $_REQUEST['province'] . "', '" . $_REQUEST['code'] . "', '" . $_REQUEST['gender'] . "', '" . $birth . "', '" . $_REQUEST['phone'] . "')";
        $count = $wpdb->query($query);
        if ($count > 0) {
            echo '<meta http-equiv="refresh" content="0;url=' . $base_page . '&type=&aac_page=1&idEdit=&msg=1' . '" />';
        }
    } else {
        echo '<div class="error"><p>ID already exists!</p></div>';
    } 
    
}

//delete patient -----------------------
if (isset($_REQUEST['delete'])) {
    $queryPatient = "DELETE FROM aac_enroll WHERE id_number = '" . $_REQUEST['id'] . "'";
    $count = $wpdb->query($queryPatient);

    $queryMsg = "DELETE FROM aac_enroll_msg WHERE id_number = '" . $_REQUEST['id'] . "'";
    $wpdb->query($queryMsg);
    
    if ($count > 0) {
        echo '<meta http-equiv="refresh" content="0;url=' . $base_page . '&type=&aac_page=1&idEdit=&msg=2' . '" />';
    }
}

//delete message -----------------------
if (isset($_REQUEST['deleteMsg'])) {
    $query = "DELETE FROM aac_enroll_msg WHERE id = '" . $_REQUEST['id'] . "'";
    $count = $wpdb->query($query);

    if (($count > 0) && $_REQUEST['deleteMsg'] == null) {
        echo '<meta http-equiv="refresh" content="0;url=' . $base_page_msg . '&type=&aac_page=1&idEdit=&msg=3' . '" />';
    }
}

//update patient info -----------------------
if (isset($_REQUEST['saveEdit'])) {
    $birthPart = explode('/', $_REQUEST['birth']);
    $birth = $birthPart[2] . '-' . $birthPart[1] . '-' . $birthPart[0];
    
    $query = "UPDATE aac_enroll SET name = '" . $_REQUEST['name'] . "', email = '" . $_REQUEST['email'] . "', address = '" . $_REQUEST['address'] . "', neighborhood = '" . $_REQUEST['neighborhood'] . "', city = '" . $_REQUEST['city'] . "', province = '" . $_REQUEST['province'] . "', code = '" . $_REQUEST['code'] . "', gender = '" . $_REQUEST['gender'] . "', birth = '" . $birth . "', phone = '" . $_REQUEST['phone'] . "' WHERE id_number = '" . $_REQUEST['id_number'] . "'";
    $count = $wpdb->query($query);
    if ($count > 0) {
        echo '<meta http-equiv="refresh" content="0;url=' . $base_page . '&type=&aac_page=1&idEdit=&msg=4' . '" />';
    }
}

function enroll_option_page() {
    if (!current_user_can('publish_posts')) {
        wp_die(__('You do not have permission to access this page.'));
    }

    global $base_page;

    if ($_REQUEST['idEdit'] == NULL) {
        ?>

        <div class="wrap">

            <h2>Patients <a href="<?php echo $base_page; ?>&type=&aac_page=&idEdit=1" class="add-new-h2">New Patient</a></h2>

            <br />
            
            <?php
            if($_REQUEST['msg'] == 1) {
                echo '<div class="updated"><p>Patient added</p></div>';
            } elseif($_REQUEST['msg'] == 2) {
                echo '<div class="updated"><p>Patient deleted!</p></div>';
            } elseif($_REQUEST['msg'] == 2) {
                echo '<div class="updated"><p>Message deleted!</p></div>';
            }elseif($_REQUEST['msg'] == 4) {
                echo '<div class="updated"><p>Patient edited!</p></div>';
            }
            ?>

            <?php
            global $wpdb, $wp_query;
            if ($_REQUEST['aac_page'] == 0) {
                $_REQUEST['aac_page'] = 1;
            }
            $total = "SELECT id_number, name, email, phone FROM aac_enroll ORDER BY name ASC";
            $result = $wpdb->get_results($total, ARRAY_N);
            $parameters = array("aac_page" => $_REQUEST['aac_page'], "url" => $base_page, "type" => $_REQUEST['type']);
            $pag = pagination($parameters, $result);
            // echo "<pre>"; print_r($result); echo "</pre>";
            ?>

            <table width="100%" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th width="120px">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th width="120px">Phone number</th>
                        <th width="80px">Edit</th>
                        <th width="80px">Delete</th>
                    </tr>
                </thead>
                <?php
                for ($i = $pag[0]['current']; $i < ($_REQUEST['aac_page'] * $pag[0]['qtd']); $i++) :
                    if (isset($result[$i][0])) : // so imprimi se existir resgistro
                        ?>
                        <tr>
                            <td><?php echo $result[$i][0]; ?></td>
                            <td><?php echo $result[$i][1]; ?></td>
                            <td><?php echo $result[$i][2]; ?></td>
                            <td><?php echo $result[$i][3]; ?></td>
                            <td><input name="edit" type="button" value="Edit" class="add-new-h2" onclick="javascript:location.href = '<?php echo $base_page; ?>&type=<?php echo $_REQUEST['type']; ?>&aac_page=<?php echo $_REQUEST['aac_page']; ?>&idEdit=<?php echo $result[$i][0]; ?>'" /></td>
                            <td>
                                <form name="form2" method="POST" action="<?php $PHP_SELF; ?>">
                                    <input name="id" type="hidden" value="<?php echo $result[$i][0]; ?>" />
                                    <input type="submit" name="delete" value="Delete" class="add-new-h2" />
                                </form>
                            </td>
                        </tr>
                <?php endif; endfor; ?>
            </table>

            <?php for ($g = 0; $g < count($pag); $g++) echo $pag[$g]['pag']; ?><br /><br />

        </div>

    <?php } elseif ($_REQUEST['idEdit'] > 1) { ?>

        <?php
        global $wpdb, $wp_query;
        $total = "SELECT * FROM aac_enroll WHERE id_number = " . $_REQUEST['idEdit'];
        $result = $wpdb->get_results($total, OBJECT);

        wp_enqueue_script('mask_script', plugin_dir_url( __FILE__ ) . '/jquery.mask.js');
        ?>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                // masks
                jQuery('.phone').mask('(999) 999-9999');
                jQuery('.id_number').mask('999.999.999-99');
                jQuery('.code').mask('AAA-AAA');
                jQuery('.date').mask('99/99/9999');
            });
        </script>

        <div class="wrap">

            <h2>Edit Patient</h2>
            
            <br />
        
            <form name="form_edit" method="POST" action="<?php $PHP_SELF; ?>">
                <table class="wp-list-table widefat fixed posts">
                    <?php foreach ($result as $result) : ?>
                        <tr>
                            <td width="100">ID</td>
                            <td width="550">
                                <input name="visual" type="text" value="<?php echo $result->id_number; ?>" size="45" class="id_number" disabled="disabled" style="background: #f2f2f2; color: #ccc;" />
                                <input name="id_number" type="hidden" value="<?php echo preg_replace('/[^0-9,.]+/i', '', $result->id_number); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td><input name="name" type="text" value="<?php echo $result->name; ?>" size="45" /></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input name="email" type="text" value="<?php echo $result->email; ?>" size="45" /></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><input name="address" type="text" value="<?php echo $result->address; ?>" size="45" /></td>
                        </tr>
                        <tr>
                            <td>Neighborhood</td>
                            <td><input name="neighborhood" type="text" value="<?php echo $result->neighborhood; ?>" size="45" /></td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td><input name="city" type="text" value="<?php echo $result->city; ?>" size="45" /></td>
                        </tr>
                        <tr>
                            <td>Province</td>
                            <td>
                                <select name="province">
                                    <option value="AB" <?php if($result->province == "AB") echo 'selected="selected"'; ?>>AB</option>
                                    <option value="BC" <?php if($result->province == "BC") echo 'selected="selected"'; ?>>BC</option>
                                    <option value="MB" <?php if($result->province == "MB") echo 'selected="selected"'; ?>>MB</option>
                                    <option value="NB" <?php if($result->province == "NB") echo 'selected="selected"'; ?>>NB</option>
                                    <option value="NL" <?php if($result->province == "NL") echo 'selected="selected"'; ?>>NL</option>
                                    <option value="NS" <?php if($result->province == "NS") echo 'selected="selected"'; ?>>NS</option>
                                    <option value="NT" <?php if($result->province == "NT") echo 'selected="selected"'; ?>>NT</option>
                                    <option value="NU" <?php if($result->province == "NU") echo 'selected="selected"'; ?>>NU</option>
                                    <option value="ON" <?php if($result->province == "ON") echo 'selected="selected"'; ?>>ON</option>
                                    <option value="PE" <?php if($result->province == "PE") echo 'selected="selected"'; ?>>PE</option>
                                    <option value="QC" <?php if($result->province == "QC") echo 'selected="selected"'; ?>>QC</option>
                                    <option value="SK" <?php if($result->province == "SK") echo 'selected="selected"'; ?>>SK</option>
                                    <option value="YT" <?php if($result->province == "YT") echo 'selected="selected"'; ?>>YT</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Postal Code</td>
                            <td><input name="code" type="text" value="<?php echo $result->code; ?>" size="20" class="code" /></td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>
                                <select name="gender">
                                    <option value="f" <?php if($result->gender == "f") echo 'selected="selected"'; ?>>Female</option>
                                    <option value="m" <?php if($result->gender == "m") echo 'selected="selected"'; ?>>Male</option>
                                </select>
                            </td>
                        </tr>
                        <?php $birthPart = explode("-", $result->birth); ?>
                        <tr>
                            <td>Date of birth</td>
                            <td><input name="birth" type="text" value="<?php echo $birthPart[2] . '/' . $birthPart[1] . '/' . $birthPart[0]; ?>" size="45" class="date" /></td>
                        </tr>
                        <tr>
                            <td>Phone number</td>
                            <td><input name="phone" type="text" value="<?php echo $result->phone; ?>" size="20" maxlength="14" class="phone" /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input name="saveEdit" type="submit" value="Save" class="add-new-h2" /></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </form>
        
        </div>

        <?php
    } elseif ($_REQUEST['idEdit'] == 1) { 
        wp_enqueue_script('mask_script', plugin_dir_url( __FILE__ ) . '/jquery.mask.js', true);
        ?>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                // masks
                jQuery('.phone').mask('(999) 999-9999');
                jQuery('.id_number').mask('999.999.999-99');
                jQuery('.code').mask('AAA-AAA');
                jQuery('.date').mask('99/99/9999');
            });
        </script>

        <div class="wrap">

            <h2>New Patient</h2>

            <br />

            <form name="form_cadastro" method="POST" action="<?php $PHP_SELF; ?>">
                <table class="wp-list-table widefat fixed posts">
                    <tr>
                        <td width="100">ID</td>
                        <td width="550">
                            <input name="id_number" type="text" size="45" class="id_number" />
                        </td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><input name="name" type="text" size="45" /></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><input name="email" type="text" size="45" /></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><input name="address" type="text" size="45" /></td>
                    </tr>
                    <tr>
                        <td>Neighborhood</td>
                        <td><input name="neighborhood" type="text" size="45" /></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><input name="city" type="text" size="45" /></td>
                    </tr>
                    <tr>
                        <td>Province</td>
                        <td>
                            <select name="province">
                                <option value="AB">AB</option>
                                <option value="BC">BC</option>
                                <option value="MB">MB</option>
                                <option value="NB">NB</option>
                                <option value="NL">NL</option>
                                <option value="NS">NS</option>
                                <option value="NT">NT</option>
                                <option value="NU">NU</option>
                                <option value="ON">ON</option>
                                <option value="PE">PE</option>
                                <option value="QC">QC</option>
                                <option value="SK">SK</option>
                                <option value="YT">YT</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Postal Code</td>
                        <td><input name="code" type="text" size="20" class="code" /></td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>
                            <select name="gender">
                                <option value="f">Female</option>
                                <option value="m">Male</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Date of birth</td>
                        <td><input name="birth" type="text" size="45" class="date" /></td>
                    </tr>
                    <tr>
                        <td>Phone number</td>
                        <td><input name="phone" type="text" size="20" maxlength="14" class="phone" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input name="add" type="submit" value="Add" class="add-new-h2" /></td>
                    </tr>
                </table>
            </form>
        
        </div>

        <?php
        
    } else if ($_REQUEST['idEdit'] == 'empty' && $_REQUEST['id_number'] > 0) {
        
        global $wpdb, $wp_query;
        $total = "SELECT * FROM aac_enroll WHERE id_number = " . $_REQUEST['id_number'];
        $result = $wpdb->get_results($total, OBJECT);
        
        $totalMsg = "SELECT msg.id, msg.doctor, msg.msg, msg.date
                     FROM aac_enroll_msg AS msg
                     INNER JOIN aac_enroll AS cad ON ( cad.id_number = msg.id_number ) 
                     WHERE msg.id_number = " . $_REQUEST['id_number'] . "
                     AND msg.doctor = " . $_REQUEST['idDoctor'] . "
                     ORDER BY msg.date DESC ";
        $resultMsg = $wpdb->get_results($totalMsg, OBJECT);
        // echo $totalMsg;
        ?>

        <div class="wrap">
            
            <br />

            <h2>Patient</h2>

            <table class="wp-list-table widefat fixed posts">
                <?php foreach ($result as $result) : ?>
                    <tr>
                        <td width="100">ID</td>
                        <td><?php echo $result->id_number; ?></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?php echo $result->name; ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $result->email; ?></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><?php echo $result->address; ?></td>
                    </tr>
                    <tr>
                        <td>Neighborhood</td>
                        <td><?php echo $result->Neighborhood; ?></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><?php echo $result->city; ?></td>
                    </tr>
                    <tr>
                        <td>Province</td>
                        <td><?php echo $result->province; ?></td>
                    </tr>
                    <tr>
                        <td>Postal Code</td>
                        <td><?php echo $result->code; ?></td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>
                            <?php
                            if($result->gender == "f") {
                                echo "Female";
                            } else {
                                echo "Male";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php $birthPart = explode("-", $result->birth); ?>
                    <tr>
                        <td>Date of birth</td>
                        <td><?php echo $birthPart[2] . '/' . $birthPart[1] . '/' . $birthPart[0]; ?></td>
                    </tr>
                    <tr>
                        <td>Phone number</td>
                        <td><?php echo $result->phone; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <br />

            <h2>Messages</h2>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th>Message</th>
                        <td width="80px">Date</td>
                        <td width="80px">Delete</td>
                    </tr>
                </thead>
                <?php foreach ($resultMsg as $resultMsg) : ?>
                    <tbody id="the-list">
                        <?php $date = explode("-", $resultMsg->date); ?>
                        <tr>
                            <th class="comments column-comments" style="padding:0 0 0 15px;"><?php echo $resultMsg->msg; ?></th>
                            <td class="comments column-comments"><?php echo $date[2] . '/' . $date[1] . '/' . $date[0]; ?></td>
                            <td class="check-column">
                                <form name="form2" method="POST" action="<?php $PHP_SELF; ?>">
                                    <input name="id" type="hidden" value="<?php echo $resultMsg->id; ?>" />
                                    <input name="volta" type="hidden" value="<?php $PHP_SELF; ?>" />
                                    <input type="submit" name="deleteMsg" value="Delete" class="add-new-h2" />
                                </form>
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
            
        <?php
    }
}

function enroll_search_page() {
    if (!current_user_can('publish_posts')) {
        wp_die(__('You do not have permission to access this page.'));
    }

    global $wpdb, $base_page, $base_page_busca;
    
    if ($_REQUEST['id_number'] != NULL) {
        $busca = "WHERE id_number = '" . $_REQUEST['id_number'] . "'";
    } elseif ($_REQUEST['name'] != NULL) {
        $busca = "WHERE name LIKE '%" . $_REQUEST['name'] . "%'";
    } elseif ($_REQUEST['email'] != NULL) {
        $busca = "WHERE email = '" . trim($_REQUEST['email']) . "'";
    }
    if ($busca != NULL) {
        $query = "SELECT id_number, name, email, phone FROM aac_enroll $busca ORDER BY name ASC";
        $resultSearch = $wpdb->get_results($query, ARRAY_N);
        //echo $query;
    }
    ?>
    <div class="wrap">

        <h2>Search</h2>
        
        <br />

        <form name="form1" method="POST" action="<?php $PHP_SELF; ?>" style="width:560px;">
            <table class="wp-list-table widefat fixed posts">
                <tr>
                    <td width="87">ID</td>
                    <td width="266"><input name="id_number" type="text" size="41" /></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><input name="name" type="text" size="41" /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input name="email" type="text" size="41" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="buscar" value="Search" class="add-new-h2" /></td>
                </tr>
            </table>
        </form>

        <br />

        <?php
        global $wpdb, $wp_query;
        if ($_REQUEST['aac_page'] == 0) {
            $_REQUEST['aac_page'] = 1;
        }
        $parameters = array("aac_page" => $_REQUEST['aac_page'], "url" => $base_page_busca, "type" => 0);
        $pag = pagination($parameters, $resultSearch);
        //echo "<pre>"; print_r($resultSearch); echo "</pre>";

        if (count($resultSearch) > 0) :
            ?>
        
            <table width="100%" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th width="120px">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th width="120px">Phone number</th>
                        <th width="80px">Edit</th>
                        <th width="80px">Delete</th>
                    </tr>
                </thead>
                <?php
                for ($i = $pag[0]['current']; $i < ($_REQUEST['aac_page'] * $pag[0]['qtd']); $i++) :
                    if (isset($resultSearch[$i][0])) : // so imprimi se existir resgistro
                        ?>
                        <tr>
                            <td><?php echo $resultSearch[$i][0]; ?></td>
                            <td><?php echo $resultSearch[$i][1]; ?></td>
                            <td><?php echo $resultSearch[$i][2]; ?></td>
                            <td><?php echo $resultSearch[$i][3]; ?></td>
                            <td><input name="edit" type="button" value="Edit" class="add-new-h2" onclick="javascript:location.href = '<?php echo $base_page; ?>&type=<?php echo $_REQUEST['type']; ?>&aac_page=<?php echo $_REQUEST['aac_page']; ?>&idEdit=<?php echo $resultSearch[$i][0]; ?>'" /></td>
                            <td>
                                <form name="form2" method="POST" action="<?php $PHP_SELF; ?>">
                                    <input name="id" type="hidden" value="<?php echo $resultSearch[$i][0]; ?>" />
                                    <input type="submit" name="delete" value="Delete" class="add-new-h2" />
                                </form>
                            </td>
                        </tr>
                <?php endif; endfor; ?>
            </table>

        <?php endif; ?>

        <?php for ($g = 0; $g < count($pag); $g++) echo $pag[$g]['pag']; ?><br /><br />

    </div>
    <?php
}

function enroll_msg() {
    if (!current_user_can('publish_posts')) {
        wp_die(__('You do not have permission to access this page.'));
    }

    global $wpdb, $base_page, $base_page_msg;
    ?>
        
<!--    <link rel="stylesheet" href="<?php echo TEMPLATE_URL ?>/css/tooltip-classic.css" type="text/css" />-->
        
    <div class="wrap">

        <h2>Messages</h2>
        
        <?php
        if($_REQUEST['msg'] == 1) {
            echo '<div class="updated"><p>Patient added!</p></div>';
        } elseif($_REQUEST['msg'] == 2) {
            echo '<div class="updated"><p>Patient deleted!</p></div>';
        } elseif($_REQUEST['msg'] == 2) {
            echo '<div class="updated"><p>Message deleted!</p></div>';
        }elseif($_REQUEST['msg'] == 4) {
            echo '<div class="updated"><p>Patient edited!</p></div>';
        }
        ?>

        <br />

        <?php
        global $wpdb, $wp_query;
        if ($_REQUEST['aac_page'] == 0) {
            $_REQUEST['aac_page'] = 1;
        }
        
        $current_user = wp_get_current_user();
        //echo $current_user->roles[0];
        // echo "<pre>"; print_r($current_user); echo "</pre>"; exit;
        
        if($current_user->roles[0] == 'administrator') {
            $total = "SELECT msg.id, cad.id_number, cad.name, cad.phone, cad.email, msg.msg, msg.doctor FROM aac_enroll_msg AS msg
                      INNER JOIN aac_enroll AS cad ON ( cad.id_number = msg.id_number )
                      ORDER BY msg.id DESC";
        } else {
            $total = "SELECT msg.id, cad.id_number, cad.name, cad.phone, cad.email, msg.msg, msg.doctor FROM aac_enroll_msg AS msg
                      INNER JOIN aac_enroll AS cad ON ( cad.id_number = msg.id_number )
                      WHERE msg.doctor = " . $current_user->data->ID . "
                      ORDER BY msg.id DESC";
        }
        $result = $wpdb->get_results($total, ARRAY_N);
        $parameters = array("aac_page" => $_REQUEST['aac_page'], "url" => $base_page_msg, "type" => $_REQUEST['type']);
        $pag = pagination($parameters, $result);
        //echo "<pre>"; print_r($result); echo "</pre>";
        ?>

        <table width="100%" class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <?php if($current_user->roles[0] == 'administrator') : ?>
                        <th width="120px">ID</th>
                        <th>Patient</th>
                        <th>Phone number</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Doctor</th>
                        <th width="80px">Read</th>
                        <th width="80px">Delete</th>
                    <?php else : ?>
                        <th width="120px">ID</th>
                        <th>Patient</th>
                        <th>Phone number</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th width="80px">Read</th>
                        <th width="80px">Delete</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <?php
            for ($i = $pag[0]['current']; $i < ($_REQUEST['aac_page'] * $pag[0]['qtd']); $i++) :
                if (isset($result[$i][0])) : // so imprimi se existir resgistro
                    $user = get_user_by('id', $result[$i][6]);
                    ?>
                    <tr>
                        <?php if($current_user->roles[0] == 'administrator') : ?>
                            <td><?php echo $result[$i][1]; ?></td>
                            <td><?php echo $result[$i][2]; ?></td>
                            <td><?php echo $result[$i][3]; ?></td>
                            <td><?php echo $result[$i][4]; ?></td>
                            <td><?php echo $result[$i][5]; ?></td>
                            <td><?php echo $user->display_name; ?></td>
                            <td><input name="openMsg" type="button" value="Read" class="add-new-h2" onclick="javascript:location.href = '<?php echo $base_page; ?>&type=<?php echo $_REQUEST['type']; ?>&aac_page=<?php echo $_REQUEST['aac_page']; ?>&id_number=<?php echo $result[$i][1]; ?>&idEdit=empty&idDoctor=<?php echo $user->ID; ?>'" /></td>
                            <td>
                                <form name="form2" method="POST" action="<?php $PHP_SELF; ?>">
                                    <input name="id" type="hidden" value="<?php echo $result[$i][0]; ?>" />
                                    <input type="submit" name="deleteMsg" value="Delete" class="add-new-h2" />
                                </form>
                            </td>
                        <?php else : ?>
                            <td><?php echo $result[$i][1]; ?></td>
                            <td><?php echo $result[$i][2]; ?></td>
                            <td><?php echo $result[$i][3]; ?></td>
                            <td><?php echo $result[$i][4]; ?></td>
                            <td><?php echo $result[$i][5]; ?></td>
                            <td><input name="openMsg" type="button" value="Read" class="add-new-h2" onclick="javascript:location.href = '<?php echo $base_page; ?>&type=<?php echo $_REQUEST['type']; ?>&aac_page=<?php echo $_REQUEST['aac_page']; ?>&id_number=<?php echo $result[$i][1]; ?>&idEdit=empty&idDoctor=<?php echo $user->ID; ?>'" /></td>
                            <td>
                                <form name="form2" method="POST" action="<?php $PHP_SELF; ?>">
                                    <input name="id" type="hidden" value="<?php echo $result[$i][0]; ?>" />
                                    <input type="submit" name="deleteMsg" value="Delete" class="add-new-h2" />
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
            <?php endif; endfor; ?>
        </table>

        <?php for ($g = 0; $g < count($pag); $g++) echo $pag[$g]['pag']; ?><br /><br />

    </div>
    <?php
}

add_action('admin_menu', 'enroll_menu');

function enroll_menu() {
    add_menu_page('Patients', 'Patients', 'manage_options', 'mt_toplevel_enroll_handle', 'enroll_option_page', plugins_url('aac-enroll/symbol.png'));
    add_submenu_page('mt_toplevel_enroll_handle', 'Messages', 'Messages', 'manage_options', 'mt_sublevel_enroll_handle', 'enroll_msg');
    add_submenu_page('mt_toplevel_enroll_handle', 'Search', 'Search', 'manage_options', 'mt_sublevel_enroll_search_handle', 'enroll_search_page');
}


/*
 * ---------------------------------------------------
 * Ajax form page, front 
 * ---------------------------------------------------
 */

// create a page to use with ajax, add Events page on activation
function install_events_pg() {
    $new_page_title = 'Form - Enroll Plugin';
    $new_page_content = '';
    $new_page_template = 'template-form-enroll.php';
    
    $page_check = get_page_by_title($new_page_title);
    $new_page = array(
            'post_type' => 'page',
            'post_title' => $new_page_title,
            'post_content' => $new_page_content,
            'post_status' => 'publish',
            'post_author' => 1,
    );
    if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);
    }
    if(!empty($new_page_template)){
        update_post_meta($page_check->ID, '_wp_page_template', $new_page_template);
    }
}
register_activation_hook(__FILE__, 'install_events_pg');

// set page template
add_filter('template_include', 'catch_plugin_template'); //single_template
function catch_plugin_template($template) {
    if( is_page_template('template-form-enroll.php') ) {
        $template = dirname( __FILE__ ) . '/template-form-enroll.php';
    }
    return $template;
}