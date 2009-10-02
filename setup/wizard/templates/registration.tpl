<?php if (AJAX) { ?>
<form id="database_registration_install" action="index.php?step_name=<?php echo $step_name; ?>" method="post" onsubmit="w.dummy();">
<?php } else { ?>
<form id="registration" action="index.php?step_name=<?php echo $step_name; ?>" method="post" onsubmit="javascript:{ if(w.validateRegistration()) { w.sendRegistration() };return false;}">
<?php } ?>

	<p class="title">Registering KnowledgeTree</p>
	<?php
		//echo $sel_country;
	?>
	<p class="description">
	Register with KnowledgeTree to receive important product updates.
	<a href="index.php?step_name=install">Skip Registration</a>
	</p>
	<!-- Hidden Fields -->
	<input type="hidden" id="sendAll" name="" value="" />
	<input type="hidden" id="state" name="" />
	<p class='disclaimer'>
	We will not share your information with 3rd-parties, nor will we send you information not directly related to KnowledgeTree's products
	and services. Please see our <a href="http://www.knowledgetree.com/about/legal" target="_blank">Privacy and Data Retention policies</a> for more information.
	</p>
	<br/>
	<div id="step_content_registration" class="step">
		<span class="error" id="reg_error"></span>
		<?php if(WINDOWS_OS) $input_width = 40; else { $input_width = 32; } ?>
		<table>
		    <tr>
		        <td><label for='first'>First Name</label></td>
		        <td rowspan='6' width='5%'>&nbsp;</td>
		        <td><input value="<?php echo $first_name; ?>" name='submitted[first_name]' id='first' size='<?php echo $input_width; ?>' style="float:left;"/></td>
		        <td rowspan='6' width='5%'>&nbsp;</td>
		        <td rowspan='6'> <?php echo $html->image('dropbox.png'); ?> </td>
		    </tr>
		    
		    <tr>
		        <td><label for='last'>Last Name</label></td>
		        <td><input value="<?php echo $last_name; ?>" name='submitted[last_name]' id='last' size='<?php echo $input_width; ?>' style="float:left;"/></td>
		    </tr>
		    <tr>
		        <td><label for='email'>Email Address</label></td>
		        <td><input value="<?php echo $email_address; ?>" name='submitted[email_address]' id='email' size='<?php echo $input_width; ?>' style="float:left;"/></td>
		    </tr>
		    <tr>
		        <td><label for='country'>Country</label></td>
		        <td>
		            <select id='country' name='submitted[country]'>
		                <?php
		                    $str = '';
		                    foreach ($countries as $code => $country) {
		                    	if ($sel_country == $country) {
		                    		$str .= "<option name='{$code}' value='{$country}' selected='selected'>{$country}</option>";
		                    	} else {
		                    		$str .= "<option name='{$code}' value='{$country}'>{$country}</option>";
		                    	}
		                        
		                    }
		
		                    echo $str;
		                ?>
		            </select>
		        </td>
		    </tr>
		    <tr>
		        <td><label for='industry'>Industry</label></td>
		        <td>
		            <select id='industry' name='submitted[industry]'>
		                <?php
		                    $str = '';
		                    foreach ($industries as $code => $industry) {
		                    	if ($sel_industry == $industry) {
		                    		$str .= "<option name='{$code}' value='{$industry}' selected='selected'>{$industry}</option>";
		                    	} else {
		                        	$str .= "<option name='{$code}' value='{$industry}'>{$industry}</option>";
		                    	}
		                    }
		
		                    echo $str;
		                ?>
		            </select>
		        </td>
		    </tr>
		    <tr>
		        <td><label for='size'>Organization Size</label></td>
		        <td>
		            <select id='size' name='submitted[organization_size]'>
		                <?php
		                    $str = '';
		                    foreach ($org_size as $code => $size) {
	                    		if ($sel_organization_size == $size) {
	                    			$str .= "<option name='{$code}' value='{$size}' selected='selected'>{$size}</option>";
	                    		} else {
		                        	$str .= "<option name='{$code}' value='{$size}'>{$size}</option>";
	                    		}
		                    }
		
		                    echo $str;
		                ?>
		            </select>
		        </td>
		    </tr>
		</table>
		
		<input type='hidden' name='form_id' value='webform_client_form_242' />
		<input type='hidden' name='op' value='Submit' />
	</div>
	
	<input type="submit" name="Previous" value="Previous" onclick="w.pClick()" class="button_previous"/>
	<input type="submit" name="Next" value="Register" onclick="w.nClick()" class="button_next"/>
</form>
<?php if (AJAX) { echo $html->js('form.js'); } ?>
<script type="text/javascript">
	$('#first').focus();
</script>