<?php
// Template Name: Form Enroll

if (isset($_REQUEST['id_number'])) {
    global $wpdb;
    // echo preg_replace('/[^0-9,.]+/i', '', $_REQUEST['id_number']);
    $_results = $wpdb->get_results("SELECT * FROM aac_enroll WHERE id_number = '" . preg_replace('/[^0-9,.]+/i', '', $_REQUEST['id_number']) . "'", OBJECT);
    // echo "<pre>"; print_r($_results); echo "</pre>"; exit;

    if($_results) {
        
        foreach ($_results as $_result) {
            $birthPart = explode("-", $_result->birth);
            echo '<div>
                    <label for="id_number">ID*</label>
                    <input name="id_number" type="text" class="id_number" value="' . $_result->id_number . '" disabled="disabled" />
                    <input name="cad[id_number]" type="hidden" class="id_number" value="' . $_result->id_number . '" />
                </div>
                <div>
                    <label for="cad[name]">Name*</label>
                    <input name="cad[name]" type="text" value="' . utf8_encode(stripslashes($_result->name)) . '" />
                </div>
                <div>
                    <label for="cad[email]">Email*</label>
                    <input name="cad[email]" type="text" value="' . $_result->email . '" />
                </div>
                <div>
                    <label for="cad[address]">Address</label>
                    <input name="cad[address]" type="text" value="' . $_result->address . '" />
                </div>
                <div>
                    <label for="cad[neighborhood]">Neighborhood</label>
                    <input name="cad[neighborhood]" type="text" value="' . $_result->neighborhood . '" />
                </div>
                <div>
                    <label for="cad[city]">City</label>
                    <input name="cad[city]" type="text" value="' . $_result->city . '" />
                </div>
                <div>
                    <label for="cad[province]">Province</label>';
                    ?>
                    <select name="cad[province]">
                        <option value="AB" <?php if($_result->province == "AB") echo 'selected="selected"'; ?>>AB</option>
                        <option value="BC" <?php if($_result->province == "BC") echo 'selected="selected"'; ?>>BC</option>
                        <option value="MB" <?php if($_result->province == "MB") echo 'selected="selected"'; ?>>MB</option>
                        <option value="NB" <?php if($_result->province == "NB") echo 'selected="selected"'; ?>>NB</option>
                        <option value="NL" <?php if($_result->province == "NL") echo 'selected="selected"'; ?>>NL</option>
                        <option value="NS" <?php if($_result->province == "NS") echo 'selected="selected"'; ?>>NS</option>
                        <option value="NT" <?php if($_result->province == "NT") echo 'selected="selected"'; ?>>NT</option>
                        <option value="NU" <?php if($_result->province == "NU") echo 'selected="selected"'; ?>>NU</option>
                        <option value="ON" <?php if($_result->province == "ON") echo 'selected="selected"'; ?>>ON</option>
                        <option value="PE" <?php if($_result->province == "PE") echo 'selected="selected"'; ?>>PE</option>
                        <option value="QC" <?php if($_result->province == "QC") echo 'selected="selected"'; ?>>QC</option>
                        <option value="SK" <?php if($_result->province == "SK") echo 'selected="selected"'; ?>>SK</option>
                        <option value="YT" <?php if($_result->province == "YT") echo 'selected="selected"'; ?>>YT</option>
                    </select>
                    <?php
        echo '  </div>
                <div>
                    <label for="cad[code]">Postal Code</label>
                    <input name="cad[code]" type="text" maxlength="9" class="code" value="' . $_result->code . '" />
                </div>
                <div>
                    <label for="cad[gender]">Gender*</label>';
                    ?>
                    <select name="cad[gender]">
                        <option value="f" <?php if($_result->gender == "f") echo 'selected="selected"'; ?>>Female</option>
                        <option value="m" <?php if($_result->gender == "m") echo 'selected="selected"'; ?>>Male</option>
                    </select>
                    <?php
        echo '  </div>
                <div>
                    <label for="cad[birth]">Date of birth*</label>
                    <input name="cad[birth]" type="text" maxlength="10" class="date" value="' . $birthPart[2] . '/' . $birthPart[1] . '/' . $birthPart[0] . '" />
                </div>
                <div>
                    <label for="cad[phone]">Phone number</label>
                    <input name="cad[phone]" type="text" maxlength="14" class="phone" value="' . $_result->phone . '" />
                </div>
                <div>
                    <label for="cad[msg]">Message</label>
                    <textarea name="cad[msg]">' . $_result->msg . '</textarea>
                </div>
                <div>
                    <label for="edit"></label>
                    <input type="submit" value="Make an appointment" id="btn_enroll" name="edit">
                </div>';
        }
        
    } else {
        
        echo '  <div>
                    ID not found, please register now.
                </div>
                <div>
                    <label for="cad[id_number]">ID*</label>
                    <input name="cad[id_number]" type="text" class="id_number" value="' . $_REQUEST['id_number'] . '" />
                </div>
                <div>
                    <label for="cad[name]">Name*</label>
                    <input name="cad[name]" type="text" />
                </div>
                <div>
                    <label for="cad[email]">Email*</label>
                    <input name="cad[email]" type="text" />
                </div>
                <div>
                    <label for="cad[address]">Address</label>
                    <input name="cad[address]" type="text" />
                </div>
                <div>
                    <label for="cad[neighborhood]">Neighborhood</label>
                    <input name="cad[neighborhood]" type="text" />
                </div>
                <div>
                    <label for="cad[city]">City</label>
                    <input name="cad[city]" type="text" />
                </div>
                <div>
                    <label for="cad[province]">Province</label>
                    <select name="cad[province]">
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
                </div>
                <div>
                    <label for="cad[code]">Postal Code</label>
                    <input name="cad[code]" type="text" maxlength="9" class="code" />
                </div>
                <div>
                    <label for="cad[gender]">Gender*</label>
                    <select name="cad[gender]">
                        <option value="f">Female</option>
                        <option value="m">Male</option>
                    </select>
                </div>
                <div>
                    <label for="cad[birth]">Date of birth*</label>
                    <input name="cad[birth]" type="text" maxlength="10" class="date" />
                </div>
                <div>
                    <label for="cad[phone]">Phone number</label>
                    <input name="cad[phone]" type="text" maxlength="14" class="phone" />
                </div>
                <div>
                    <label for="cad[msg]">Message</label>
                    <textarea name="cad[msg]">' . $_result->msg . '</textarea>
                </div>
                <div>
                    <label for="send"></label>
                    <input type="submit" value="Make an appointment" id="btn_enroll" name="send">
                </div>';
        
    }
}
?>