<?php
require_once('base.php');
class Registration_form extends PieReg_Base
{
	var $id;
	var $name;
	var $field;	
	var $data;
	var $label_alignment;
	var $pages;
	
	function __construct()	
	{
		$this->data = $this->getCurrentFields();
		$this->label_alignment = $this->data['form']['label_alignment'];		
		$this->pages = 1;
		//add_action("Add_payment_option",		array($this,"Add_payment_option"));
		//add_action("add_payment_method_script", array($this,"add_payment_method_script"));
		
	}
	function addFormData()
	{
		$data = "";
		$data .= '<h2 id="piereg_pie_form_heading">'.$this->data['form']['label'].'</h2>';	
		$data .= '<p id="piereg_pie_form_desc" class="'.$this->addClass("").'" >'.$this->data['form']['desc'].'</p>';		
		return $data;
	
	}
	function addDefaultField()
	{
		$data = "";
		$this->name = $this->field['field_name'];
		if($this->field['field_name']=="description")
		{
			$data .= '<textarea name="description" id="description" rows="5" cols="80">'.$this->getDefaultValue().'</textarea>';	
		}
		else
		{
			$data .= '<input id="'.$this->id.'" name="'.$this->field['field_name'].'" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';	
		}	
		return $data;
	}
	function addTextField()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';
	}
	function addHiddenField()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'"  type="hidden" value="'.$this->getDefaultValue().'" />';		
	}
	function addUsername()
	{
		return '<input id="username" name="username" class="input_fields validate[required]" placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue('username').'" data-errormessage-value-missing="'.$this->field['validation_message'].'"  />';	
		
	}
	function addPassword()
	{
		$style = "";
		$data = "";
		if($this->label_alignment=="left")
			$style = 'class = "wdth-lft mrgn-lft"';
		
		
		$data .= '<input '; 
		
		if($this->field['show_meter']==1)
		{
			$data .= 'onkeyup="passwordStrength(this.value)" ';
		}
		
		$data .= 'id="'.$this->id.'" name="password" class="'.$this->addClass("input_fields",array("minSize[8]")).'" placeholder="'.$this->field['placeholder'].'" type="password" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" />';
				
		
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 
			
			$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'"><div class="fieldset"><label>Confirm Password</label><input id="confirm_password_'.$this->id.'" type="password" data-errormessage-value-missing="'.$this->field['validation_message'].'" data-errormessage-range-underflow="'.$this->field['validation_message'].'" data-errormessage-range-overflow="'.$this->field['validation_message'].'" class="input_fields validate[required,equals['.$this->id.']]" placeholder="'.$this->field['placeholder'].'" />';	
			
				
			return $data;
			
	}	
	function addEmail()
	{
		$data = "";
		$data .='<input id="'.$this->id.'" name="e_mail" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue("e_mail").'" />';
		
		if(isset($this->field['confirm_email']))
		{
			$class = '';
			$fclass = '';
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 	
		
			
			$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass .'"><div class="fieldset"><label>Confirm E-Mail</label><input  placeholder="'.$this->field['placeholder'].'" id="confirm_email_'.$this->id.'" '.$this->addValidation().' type="text" class="input_fields validate[required,equals['.$this->id.']]">';
			
			
			
		}	
		return $data;
	}
	function addUpload()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().' type="file"  />';	
	}
	function addProfilePicUpload()
	{
		return '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().' validate[funcCall[checkExtensions],ext[gif|jpeg|jpg|png|bmp]]"  '.$this->addValidation().' type="file"  />';	
	}
	function addTextArea()
	{
		return '<textarea id="'.$this->id.'" name="'.$this->name.'" rows="'.$this->field['rows'].'" cols="'.$this->field['cols'].'"  class="'.$this->addClass("").'"  placeholder="'.$this->field['placeholder'].'">'.$this->getDefaultValue().'</textarea>';		
	}
	function addName()
	{
		$data = "";
		$data .= '<div class="fieldset"><label>'.__("First Name","piereg").'</label>';
		$data .= '<input value="'.$this->getDefaultValue('first_name').'" id="'.$this->id.'_firstname" name="first_name" class="'.$this->addClass().' input_fields" '.$this->addValidation().'  type="text"  />';				
		
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top"; 					
	
		$data .= '</div></li><li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
			
		$data .= '<div class="fieldset"><label>'.__("Last Name","piereg").'</label>';
		$data .= '<input value="'.$this->getDefaultValue('last_name').'" id="'.$this->id.'_lastname" name="last_name" class="'.$this->addClass().' input_fields" '.$this->addValidation().'  type="text"  /></div>';	
		return $data;
		
	}
	function addTime()
	{
		$data = "";
		$this->field['hours'] = TRUE;
		$name = $this->name;
		
		$time_this_values = $this->getDefaultValue($name);
		
		$data .= '<div class="time"><div class="time_fields"><input value="'.$time_this_values["hh"].'" maxlength="2" id="hh_'.$this->id.'" name="'.$this->name.'[hh]" type="text"  class="'.$this->addClass().'"  '.$this->addValidation().'><label>'.__("HH","piereg").'</label></div>';
		$this->field['hours'] = FALSE;
		
		$this->field['mins'] = TRUE;
		$data .= '<span class="colon">:</span><div class="time_fields"><input value="'.$time_this_values["mm"].'" maxlength="2" id="mm_'.$this->id.'" type="text" name="'.$this->name.'[mm]"  class="'.$this->addClass().'"  '.$this->addValidation().'><label>'.__("MM","piereg").'</label></div><div id="time_format_field_'.$this->id.'" class="time_fields"></div>';
		$this->field['mins'] = FALSE;
		
		if($this->field['time_type']=="12")
		{
			$time_format_val = $time_this_values["time_format"];
			$data .= '<div class="time_fields"><select name="'.$this->name.'[time_format]" >
				<option value="am" ';
				$data .=($time_format_val == "am")?'selected=""':'';
				$data .='>AM</option>';
				$data .='<option value="pm"  ';
				$data .=($time_format_val == "pm")?'selected=""':'';
				$data .='>PM</option>
			</select></div>';
		}
		
		$data .= '</div>';
		return $data;
	}	
	function addDropdown()
	{
		$data = "";
		$multiple = "";
		$name = $this->name."[]";
		$thispostedvalue = $this->getDefaultValue();
		
		if($this->field['type']=="multiselect")
		{
			$multiple 	= 'multiple';			
		}		
		$data .= '<select '.$multiple.' id="'.$name.'" name="'.$name.'" class="'.$this->addClass("").'" '.$this->addValidation().'  >';
	
		if($this->field['list_type']=="country")
		{
			 $countries = get_option("pie_countries");			 
			$data .= $this->createDropdown($countries);			   	
		}
		else if($this->field['list_type']=="us_states")
		{
			 $us_states = get_option("pie_us_states");
			 $options 	= $this->createDropdown($us_states);				 
			 $data .= $options;						   	
		}
		else if($this->field['list_type'] == "can_states")
		{
			$can_states = get_option("pie_can_states");			
			$data .= $options 	= $this->createDropdown($can_states);					
		}
		else if(sizeof($this->field['value']) > 0)
			{	for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
				{
					$selected = '';
					if(is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					{
						$selected = 'selected="selected"';	
					}
					if(is_array($thispostedvalue)){
						foreach($thispostedvalue as $thissinglepostedval){
							if($thissinglepostedval == $this->field['value'][$a])
								$selected = 'selected="selected"';
							//$checked = 'checked="checked"';){
						}
					}
					if($this->field['value'][$a] !="" && $this->field['display'][$a] != "")
					$data .= '<option '.$selected.' value="'.$this->field['value'][$a].'">'.$this->field['display'][$a].'</option>';	
				}		
			}
		$data .= '</select>';	
		return $data;
	}
	function addNumberField()
	{
		$data = "";
		$data .= '<input id="'.$this->id.'" name="'.$this->name.'" class="'.$this->addClass().'"  '.$this->addValidation().'  placeholder="'.$this->field['placeholder'].'" type="number" value="'.$this->getDefaultValue().'"' ;
		
		if(!empty($this->field['min']))
		$data .= 'min="'.$this->field['min'].'"';
		
		if(!empty($this->field['max']))
		$data .= 'max="'.$this->field['max'].'"';
		
		$data .= '/>';	
		return $data;
	}
	function addPhone()
	{		
		return '<input id="'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' name="'.$this->name.'"  placeholder="'.$field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';	
	}
	function addList()
	{
		$data = "";
		$width  = 85 /  $this->field['cols']; 
		$name = $this->name;
		
		$list_this_values = $this->getDefaultValue($name);

		
		for($a = 1 ,$c=0; $a <= $this->field['rows'] ; $a++,$c++)
		{
			if($a==1)
			{
				$data .= '<div class="'.$this->id.'_'.$a.' pie_list">';
				
				
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input value="'.$list_this_values[$c][$b-1].'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields"> ';
				}
				if( ((int)$this->field['rows']) > 1)
				{
					$data .= ' <img src="'.get_bloginfo('url').'/wp-content/plugins/pie-register/images/plus.png" onclick="addList('.$this->field['rows'].','.$this->field['id'].');" alt="add" /></div>';		
				}
			}
			else
			{
				if(isset($list_this_values[$c]) != false)
					$display_list_style = (!array_filter($list_this_values[$c]))? "display:none;" : "display:block;";
				else
					$display_list_style = "display:none;";
					
				$data .= '<div style="'.$display_list_style.'" class="'.$this->id.'_'.$a.' pie_list">';
				for($b = 1 ; $b <= $this->field['cols'] ;$b++)
				{
					$data .= '<input value="'.$list_this_values[$c][$b-1].'" style="width:'.$width.'%;margin-right:2px;" type="text" name="'.$this->name.'['.$c.'][]" class="input_fields">';
				}
				
					$data .= ' <img src="'.get_bloginfo('url').'/wp-content/plugins/pie-register/images/minus.gif" onclick="removeList('.$this->field['rows'].','.$this->field['id'].','.$a.');" alt="add" /></div>';
				
				
			}
		}
		return $data;
	}
	function addHTML()
	{
		return html_entity_decode($this->field['html']);
	}
	function addSectionBreak()
	{
		$class = "";
		
		if($this->label_alignment == "left")
		$class .= "wdth-lft ";
		
		$class .= "sectionBreak";
		
		return '<div class="'.$class.'"></div>';	
	}
	function addCheckRadio()
	{
		$data = "";
		if(sizeof($this->field['value']) > 0)
		{
			$data .= '<div class="radio_wrap">';
			$thispostedvalue = $this->getDefaultValue();
			for($a = 0 ; $a < sizeof($this->field['value']) ; $a++)
			{
				$checked = '';
				
					
				if(is_array($this->field['selected']) && in_array($a,$this->field['selected']))
					$checked = 'checked="checked"';	
				else
					$checked = '';
				
				if(is_array($thispostedvalue)){
					foreach($thispostedvalue as $thissinglepostedval){
						if($thissinglepostedval == $this->field['value'][$a])
							$checked = 'checked="checked"';
						//$checked = 'checked="checked"';){
					}
				}
				
				
				//if(!empty($this->field['display'][$a]))
				//{	
					
					$data .= "<label>";
					$data .= $this->field['display'][$a];	
					$data .= "</label>";
					$data .= '<input '.$checked.' value="'.$this->field['value'][$a].'" type="'.$this->field['type'].'" '.$multiple.' name="'.$this->name.'[]" class="'.$this->addClass("").' radio_fields" '.$this->addValidation().'  >';
					
					
				//}
			}
			$data .= "</div>";		
		}
		return $data;
	}
	function addAddress()
	{
		$address_values = $this->getDefaultValue($this->name);
		$data = "";
		$data .= '<div class="address_main">';
		$data .= '<div class="address">
		  <input type="text" name="'.$this->name.'[address]" id="'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.$address_values['address'].'">
		  <label>'.__("Street Address","piereg").'</label>
		</div>';
		
		 if(!$this->field['hide_address2'])
		 {
		
			$data .= '<div class="address">
			  <input type="text" name="'.$this->name.'[address2]" id="address2_'.$this->id.'"  class="'.$this->addClass().'"  '.$this->addValidation().' value="'.$address_values['address2'].'">
			  <label>'.__("Address Line 2","piereg").'</label>
			</div>';
		 }
		
		$data .= '<div class="address">
		  <div class="address2">
			<input type="text" name="'.$this->name.'[city]" id="city_'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.$address_values['city'].'">
			<label>'.__("City","piereg").'</label>
		  </div>';
		
		
		 if(!$this->field['hide_state'])
		 {
			 	if($this->field['address_type'] == "International")
				{
					$data .= '<div class="address2"  >
					<input type="text" name="'.$this->name.'[state]" id="state_'.$this->id.'" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.$address_values['state'].'">
					<label>'.__("State / Province / Region","piereg").'</label>
				 	 </div>';		
				}
				else if($this->field['address_type'] == "United States")
				{
				  $us_states = get_option("pie_us_states");
				  $selectedoption = ($address_values['state'])?$address_values['state']:$this->field['us_default_state'];
				  $options 	= $this->createDropdown($us_states,$selectedoption);	
				 
				  $data .= '<div class="address2"  >
					<select id="state_'.$this->id.'" name="'.$this->name.'[state]" class="'.$this->addClass("").'">
					 '.$options.' 
					</select>
					<label>'.__("State","piereg").'</label>
				  </div>';	
				}
				else if($this->field['address_type'] == "Canada")
				{
					
					$can_states = get_option("pie_can_states");
					$selectedoption = ($address_values['state'])?$address_values['state']:$this->field['canada_default_state'];
				  	$options 	= $this->createDropdown($can_states,$selectedoption);
					$data .= '<div class="address2">
						<select id="state_'.$this->id.'" class="'.$this->addClass("").'" name="'.$this->name.'[state]">
						  '.$options.'
						</select>
						<label>'.__("Province","piereg").'</label>
					  </div>';		
				}
		 }
		
		
		 
		 
		$data .= '</div>';
		
		$data .= '<div class="address">';	
		
		$data .= ' <div class="address2">
		<input id="zip_'.$this->id.'" name="'.$this->name.'[zip]" type="text" class="'.$this->addClass().'"  '.$this->addValidation().' value="'.$address_values['zip'].'">
		<label>'.__("Zip / Postal Code","piereg").'</label>
		 </div>';	 
		
		
		 if($this->field['address_type'] == "International")
		 {
			 $countries = get_option("pie_countries");
			 $selectedoption = ($address_values['country'])?$address_values['country']:$this->field['default_country'];		 
			 $options 	= $this->createDropdown($countries,$selectedoption);  
			 $data .= '<div  class="address2" >
					<select id="country_'.$this->id.'" name="'.$this->name.'[country]" class="'.$this->addClass("").'"   '.$this->addValidation().'>
                    <option>'.__("Select Country","piereg").'</option>
					'. $options .'
					 </select>
					<label>'.__("Country","piereg").'</label>
		  		</div>';
		 }
		 
		 
		$data .= '</div>';
		$data .= '</div>';
		return $data;
	}	
	function addDate()
	{			
		$data = "";
		$date_this_values = $this->getDefaultValue($this->name);
		if($date_this_values != ""){
			$date_this_values['date']['mm']="";
			$date_this_values['date']['dd']="";
			$date_this_values['date']['yy']="";
		}
		
		if($this->field['date_type'] == "datefield")
		{
			
			if($this->field['date_format']=="mm/dd/yy")
			{
			
			$data .= '<div class="time date_format_field">
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.$date_this_values['date']['mm'].'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" '.$this->addValidation().' value="'.$date_this_values['date']['dd'].'">
					<label>'.__("DD","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" '.$this->addValidation().' value="'.$date_this_values['date']['yy'].'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				</div>';
			} 
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
				$data .= '<div class="time date_format_field">
				 <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.$date_this_values['date']['yy'].'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().' value="'.$date_this_values['date']['mm'].'">
					<label>'.__("MM","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.$date_this_values['date']['dd'].'">
					<label>'.__("DD","piereg").'</label>
				  </div>				  
				</div>';	
			}
			else
			{
				$data .= '<div class="time date_format_field">
				 <div class="time_fields">
					<input id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" maxlength="2"  type="text" class="'.$this->addClass("input_fields",array("custom[day]")).'" value="'.$date_this_values['date']['dd'].'">
					<label>'.__("DD","piereg").'</label>
				  </div>	
				 <div class="time_fields">
					<input id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" maxlength="4"  type="text" class="'.$this->addClass("input_fields",array("custom[year]")).'" value="'.$date_this_values['date']['yy'].'">
					<label>'.__("YYYY","piereg").'</label>
				  </div>
				  <div class="time_fields">
					<input id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" maxlength="2" type="text" class="'.$this->addClass("input_fields",array("custom[month]")).'" '.$this->addValidation().'" value="'.$date_this_values['date']['mm'].'">
					<label>'.__("MM","piereg").'</label>
				  </div>				  			  
				</div>';	
			}
		}
		else if($this->field['date_type'] == "datepicker")
		{
		
						
				$data .=	'<div class="time date_format_field">
				  <input id="'.$this->id.'" name="'.$this->name.'[date][]" readonly="readonly" type="text" class="'.$this->addClass().' date_start" title="'.$this->field['date_format'].'" value="';
				$data .=	(isset($date_this_values['date'][0]))?$date_this_values['date'][0] : "";
				$data .=	'">';
				  
				 $data .= '<input id="'.$this->id.'_format" type="hidden"  value="'.$this->field['date_format'].'">';
				 $data .= '<input id="'.$this->id.'_firstday" type="hidden"  value="'.$this->field['firstday'].'">';
				
				 $data .= '<input id="'.$this->id.'_startdate" type="hidden"  value="'.$this->field['startdate'].'">';
				  
				if($this->field['calendar_icon'] == "calendar")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.get_bloginfo("url").'/wp-content/plugins/pie-register/images/calendar.png"  />'; 
				}
				else if($this->field['calendar_icon'] == "custom")
				{
					 $data .=  '<img id="'.$this->id.'_icon" class="calendar_icon" src="'.$this->field['calendar_icon_url'].'"  />'; 
				}
				  
				 $data .= '</div>';	
		}
		else if($this->field['date_type'] == "datedropdown")
		{
				
			if($this->field['date_format']=="mm/dd/yy")
			{
			
					$data .= '<div class="time date_format_field">
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if($date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if($date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if($date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .= '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .= '
					</select>
				  </div>
				</div>';
			}
			else if($this->field['date_format']=="yy/mm/dd" || $this->field['date_format']=="yy.mm.dd")
			{
					$data .= '<div class="time date_format_field">
					 <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if($date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if($date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if($date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>				 
				</div>';
			
			}
			else
			{
				$data .= '<div class="time date_format_field">
				
				  
				  <div class="time_fields">
					<select id="dd_'.$this->id.'" name="'.$this->name.'[date][dd]" class="'.$this->addClass("").'">
					  <option value="">'.__("Day","piereg").'</option>';
					  for($a=1;$a<=31;$a++){
						  if($date_this_values['date']['dd'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>	
				  <div class="time_fields">
					<select id="mm_'.$this->id.'" name="'.$this->name.'[date][mm]" class="'.$this->addClass("").'">
					  <option value="">'.__("Month","piereg").'</option>';
					  for($a=1;$a<=12;$a++){
						  if($date_this_values['date']['mm'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>
				  	 <div class="time_fields">
					<select id="yy_'.$this->id.'" name="'.$this->name.'[date][yy]" class="'.$this->addClass("").'">
					  <option value="">'.__("Year","piereg").'</option>';
					  for($a=((int)date("Y"));$a>=(((int)date("Y"))-100);$a--){
						  if($date_this_values['date']['yy'] == $a)
						  	$sel = ' selected=""';
						  else
						  $sel = '';	
						  $data .=  '<option value="'.$a.'" '.$sel.'>'.__($a,"piereg").'</option>';
					  }
					  $data .=  '
					</select>
				  </div>			 
				</div>';	
			}			
		}
		return $data;
	}
	function addInvitationField()
	{
			return '<input id="'.$this->id.'" name="invitation" class="'.$this->addClass().'"  placeholder="'.$this->field['placeholder'].'" type="text" value="'.$this->getDefaultValue().'" />';		
	}	
		
	function createFieldName($text)
	{
		return $this->getMetaKey($text);			
	}
	function createFieldID()
	{
		return "field_".$this->field['id'];	
	}
	function getDefaultValue($name="")
	{
		if($name != "")
		{
			$this->name = $name;	
		}
		if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];	
		}
		return $this->field['default_value'];	
	}
	function addDesc()
	{
		if(!empty($this->field['desc']))
		{
			return '<span class="desc">'.$this->field['desc'].'</span>';
		}
	}
	function addLabel()
	{
		if($this->field['type']=="name" && $this->field['name_format']=="normal")
		{
			return "";
		}		
					
		$topclass = "";
		if($this->label_alignment=="top")
			$topclass = "label_top";
	
		return '<label class="'.$topclass .'" for="'.$this->name.'">'.$this->field['label'].'</label>';		
	}
	function addClass($default = "input_fields",$val = array())
	{
		$class = $default." ".$this->field['css'];
		
		
		if($this->field['required'])
		{
			$val[] = "required";		
		}
		
		
		if($this->field['validation_rule']=="number"  || $this->field['type']=="number")
		{
			$val[] = "custom[number]";		
		}
		else if($this->field['validation_rule']=="alphanumeric")
		{
			$val[] = "custom[alphanumeric]";		
		}
		else if($this->field['validation_rule']=="email" || $this->field['type']=="email")
		{
			$val[] = "custom[email]";		
		}
		else if($this->field['validation_rule']=="website" || $this->field['type']=="website")
		{
			$val[] = "custom[url]";		
		}		
		else if($this->field['validation_rule']=="standard" || $this->field['phone_format']=="standard")
		{
			$val[] = "custom[phone_standard]";		
		}
		else if($this->field['validation_rule']=="international" || $this->field['phone_format']=="international")
		{
			$val[] = "custom[phone_international]";		
		}
		else if($this->field['type']=="time")
		{
			
			$val[] = "custom[number]";	
			$val[] = "minSize[2]";
			$val[] = "maxSize[2]";
			$val[] = "min[0]";
			
			if($this->field['hours']==TRUE)
			{
				if($this->field['time_type']=="12")
				{
					$val[] = "max[12]";
				}
				else
				{
					$val[] = "max[23]";	
				}
			}
			else if($this->field['mins']==TRUE)
			{
				$val[] = "max[59]";	
			}
				
		}
		else if($this->field['type']=="upload" && explode(",",$this->field['file_types']) > 0)
		{
			if(!empty($this->field['file_types']))
			{
				$val[] = "funcCall[checkExtensions]";	
				$val[] = "ext[".str_replace(",","|",$this->field['file_types'])."]";			
			}
		}
		
		if(sizeof($val) > 0)
		{
			$val = " validate[".implode(",",$val)."]";
			$class .= $val;	
		}
		
		return $class;	
	}
	function addValidation()
	{
				
		
		if($this->field['required'] && !empty($this->field['validation_message']))
		{
			$val[] = 'data-errormessage-value-missing="'.$this->field['validation_message'].'"';
		}
		
		
		if($this->field['validation_rule']=="number" || $this->field['type']=="number" || $this->field['validation_rule']=="alphanumeric" || $this->field['validation_rule']=="email" || $this->field['type']=="email" || $this->field['validation_rule']=="website" || $this->field['type']=="website" || $this->field['type']=="phone" || $this->field['type']=="date")
		{
			$val[] = 'data-errormessage-custom-error="'.$this->field['validation_message'].'"';		
		}		
		else if($this->field['type']=="time")
		{
			$val[] = 'data-errormessage-custom-error="'.$this->field['validation_message'].'"';		
			$val[] = 'data-errormessage-range-underflow="'.$this->field['validation_message'].'"';	
			$val[] = 'data-errormessage-range-overflow="'.$this->field['validation_message'].'"';
		}
		
		
		if(sizeof($val) > 0)
		{
			return implode(" ",$val);			
		}
		
		
	}
	
	function addCaptcha()
	{
		$data = "";
		 $settings  	=  get_option("pie_register_2");
		 $publickey		= $settings['captcha_publc'] ;
		 
		 if($publickey)
		 {
			$data .= '<div id="recaptcha_widget_div">';
		 	require_once('wp-content/plugins/pie-register/recaptchalib.php');		
 			$data .= recaptcha_get_html($publickey);
			$data .= '</div>';
		 }
		 return $data;
	}
	function addSubmit()
	{
		$data = "";
		if($this->pages > 1)
		{
			$data .= '<input class="pie_prev" name="pie_prev" id="pie_prev_'.$this->pages.'" type="button" value="Previous" />';
			$data .= '<input id="pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';						
		}
		$check_payment = get_option("pie_register_2");
		
		//if($check_payment["enable_paypal"] == 1 ||  $check_payment["enable_authorize_net"] == 1)
		if($this->check_enable_payment_method() == "true")
		{
			do_action("add_select_payment_script"); // Add script
			$data .= "<label>".__("Select Payment","piereg")."</label>";
			$data .= '<select name="select_payment_method" id="select_payment_method">';
			$data .= '<option value="">'.__("Select","piereg").'</option>';
			do_action('Add_payment_option');
			$data .= "</select>";
			do_action("get_payment_content_area");
			$data .= '<input name="pie_submit" type="submit" value="'.$this->field['text'].'" />';	
		}
		else
		{
			$data .= '<input name="pie_submit" type="submit" value="'.$this->field['text'].'" />';	
		}
		if($this->field['reset']==1)
		{
			$data .= '<input name="pie_reset" type="reset" value="'.$this->field['reset_text'].'" />';		
		}
		return $data;
	}
	
	function addPaypal()
	{
		return '<input name="pie_submit" value="paypal" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />';	
	}
	function addPagebreak($fromwidget = false)
	{
		$data = "";
		if($fromwidget)
		$cl = 'piewid_';
		else
		$cl = '';
		
		$data .= '<input id="'.$cl.'total_pages" class="piereg_regform_total_pages" name="pie_total_pages" type="hidden" value="'.$this->countPageBreaks().'" />';
		if($this->pages > 1){
			
			$data .= '<input id="'.$cl.'pie_prev_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages-1).'" />';		
			
			if($this->field['prev_button']=="text")
			{
				$data .= '<input class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" type="button" value="'.$this->field['prev_button_text'].'" />';	
			}
			else if($this->field['prev_button']=="url")
			{
				$data .= '<img class="pie_prev" name="pie_prev" id="'.$cl.'pie_prev_'.$this->pages.'" src="'.$this->field['prev_button_url'].'"  />';		
			}
		}
		
		
		$data .= '<input id="'.$cl.'pie_next_'.$this->pages.'_curr" name="page_no" type="hidden" value="'.($this->pages+1).'" />';	
		if($this->field['next_button']=="text")
		{
			$data .= '<input class="pie_next" name="pie_next" id="'.$cl.'pie_next_'.$this->pages.'" type="button" value="'.__($this->field['next_button_text'],"piereg").'" />';			
		}
		else if($this->field['next_button']=="url")
		{
			$data .= '<img style="cursor:pointer;" src="'.$this->field['next_button_url'].'" class="pie_next" name="pie_next" id="'.$cl.'pie_next_'.$this->pages.'" />';	
		}
		return $data;	
	}
	function printFields($fromwidget = false)
	{
		$pie_reg_fields = "";
		$update = get_option( 'pie_register_2' );	
		//wp_enqueue_script( 'jquery' );
		/*if($update['outputcss']==1)//Output Form CSS
		{
			wp_register_style( 'prefix-style', $this->pluginURL("css/front.css") );
			wp_enqueue_style( 'prefix-style' );	
		}*/
		foreach($this->data as $this->field)
		{
			if ($this->field['type']=="")
			{
				continue;
			}
			
			if($this->field['type']=="invitation" && $update["enable_invitation_codes"]=="0")
			{
				continue;	
			}
			
			
			$this->name 	= $this->createFieldName($this->field['type']."_".$this->field['id']);
			$this->id 		= $this->name;
			
			//We don't need to print li for hidden field
			if ($this->field['type'] == "hidden")
			{
				$pie_reg_fieldsa .= $this->addHiddenField();
				continue;
			}
			
			$topclass = "";
			if($this->label_alignment=="top")
				$topclass = "label_top"; 
			
			$pie_reg_fields .= '<li class="fields pageFields_'.$this->pages.' '.$topclass.'">';
			
			//When to add label
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'password':			
				case 'email' :
				case 'textarea':
				case 'dropdown':
				case 'multiselect':
				case 'number':
				case 'radio':
				case 'checkbox':
				case 'html':								
				case 'time':				
				case 'upload':			
				case 'profile_pic':			
				case 'address':				
				case 'captcha':				
				case 'phone':				
				case 'date':				
				case 'list':								
				case 'sectionbreak':				
				case 'default':
				case 'invitation':
				$pie_reg_fields .= '<div class="fieldset">'.$this->addLabel();							
				break;							
			endswitch;
			
		
			
			if($this->field['type'] == "pagebreak")
			{
				$pie_reg_fields .= $this->addPagebreak($fromwidget);	
				$this->pages++;			
			}
			//Printting Field
			switch($this->field['type']) :				
				case 'text' :								
				case 'website' :
				$pie_reg_fields .= $this->addTextField();
				break;				
				case 'username' :
				$pie_reg_fields .= $this->addUsername();
				break;
				case 'password' :
				$pie_reg_fields .= $this->addPassword();
				break;
				case 'email' :
				$pie_reg_fields .= $this->addEmail();
				break;
				case 'textarea':
				$pie_reg_fields .= $this->addTextArea();
				break;
				case 'dropdown':
				case 'multiselect':
				$pie_reg_fields .= $this->addDropdown();
				break;
				case 'number':
				$pie_reg_fields .= $this->addNumberField();			
				break;
				case 'radio':
				case 'checkbox':
				$pie_reg_fields .= $this->addCheckRadio();
				break;
				case 'html':
				$pie_reg_fields .= $this->addHTML();
				break;
				case 'name':
				$pie_reg_fields .= $this->addName();
				break;
				case 'time':
				$pie_reg_fields .= $this->addTime();
				break;
				case 'upload':
				$pie_reg_fields .= $this->addUpload();
				break;
				case 'profile_pic':
				$pie_reg_fields .= $this->addProfilePicUpload();
				break;
				case 'address':
				$pie_reg_fields .= $this->addAddress();
				break;
				case 'captcha':
				$pie_reg_fields .= $this->addCaptcha();
				break;
				case 'phone':
				$pie_reg_fields .= $this->addPhone();
				break;
				case 'date':
				$pie_reg_fields .= $this->addDate();			
				break;
				case 'list':
				$pie_reg_fields .= $this->addList();
				break;
				case 'submit':
				$pie_reg_fields .= $this->addSubmit();
				break;				
				case 'sectionbreak':
				$pie_reg_fields .= $this->addSectionBreak();
				break;	
				case 'default':
				$pie_reg_fields .= $this->addDefaultField();
				break;
				case 'invitation':
				$pie_reg_fields .= $this->addInvitationField();
				break;							
			endswitch;
			
				switch($this->field['type']) :				
				case 'text' :								
				case 'website' :							
				case 'username' :
				case 'password':			
				case 'email' :
				case 'textarea':
				case 'dropdown':
				case 'multiselect':
				case 'number':
				case 'radio':
				case 'checkbox':
				case 'html':								
				case 'time':				
				case 'upload':			
				case 'profile_pic':
				case 'address':				
				case 'captcha':				
				case 'phone':				
				case 'date':				
				case 'list':		
				case 'default':
				case 'invitation':				
				$pie_reg_fields .= $this->addDesc();
				$pie_reg_fields .= '</div>';					
				break;							
			endswitch;
					
			$pie_reg_fields .=  '</li>';
			
			if($this->field['type'] == "password" && $this->field['show_meter']==1)
			{		
				$pie_reg_fields .=  '<li class="fields pageFields_'.$this->pages.'">';
				$pie_reg_fields .=  "<div id='password_meter' class='fieldset' ".$style.">";
				$pie_reg_fields .=  '<label id="piereg_passwordDescription">'.__("Password not entered","piereg").'</label>
				<div id="piereg_passwordStrength" class="piereg_strength0">&nbsp;</div>';
				$pie_reg_fields .=  "</div>";
				$pie_reg_fields .=  '</li>';
			}
		}		
		return $pie_reg_fields;
	}
	function validateRegistration($errors)
	{
		if(!is_wp_error($errors))
		$errors = new WP_Error();
		$piereg 	= get_option( 'pie_register_2' );
		
		
		global $wpdb;
		if ( empty( $_POST['username'] ) )
		{
			$errors->add( $slug , '<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Invalid_Username",__(' Invalid Username','piereg' )));
		}
		else if ( username_exists( $_POST['username'] ) )
		{
			$errors->add( $slug , '<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Username_already_exists",__(' Username already exists','piereg' )));
		}		
		
		if ( empty( $_POST['e_mail'] ) || !filter_var($_POST['e_mail'],FILTER_VALIDATE_EMAIL) )
		{
			$errors->add( $slug , '<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Invalid_Email_address",__(' Invalid E-mail address','piereg' )));
		}
		else if ( email_exists( $_POST['e_mail'] ) )
		{
			$errors->add( $slug , '<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Email_address_already_exists",__(' E-mail address already exists','piereg' )));
					
		}
			
		 foreach($this->data as $field)
		 {
			$slug 				= $this->createFieldName($field['type']."_".$field['id']);			
			if($field['type']=="username" || $field['type']=="email"  || $field['type']=="password")
			{
				  $slug  = $this->createFieldName($field['label']);	
			}			
			
			$field_name			= $_POST[$slug];			
			$required 			= $field['required'];
			$rule				= $field['validation_rule'];
			$validation_message	= (!empty($field['validation_message']) ? $field['validation_message'] : $field['label'] ." is required.");
			
			//Handling File Field
			if($field['type']=="profile_pic")
			{
				if($_FILES[$slug]['name'] != ''){
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],array("gif","jpeg","jpg","png","bmp"));
					if(!$result){
						$errors->add( $slug , '<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Invalid_File_Type_In_Profile_Picture",__(' Invalid File Type In Profile Picture.','piereg' )));
					}
				}
			}
			elseif($field['type']=="upload"){
				if($_FILES[$slug]['name'] != '' and $field['file_types'] != ""){
					$filter_array = stripcslashes($field['file_types']);
					$filter_array = explode(",",$filter_array);
					$result = $this->piereg_validate_files($_FILES[$slug]['name'],$filter_array);
					if(!$result){
						$errors->add( $slug , apply_filters("piereg_invalid_file",'<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.apply_filters("piereg_Invalid_File_Type",__(' Invalid File Type','piereg' ))));
					}
				}
			}
			else if($field['type']=="invitation"  && $piereg["enable_invitation_codes"]=="1" && $required != "")
			{
				$field_name = $code = $_POST['invitation'];
				
				$codetable	= $this->codeTable();				
				$codes = $wpdb->get_results( "SELECT * FROM $codetable where name = '$code' and status = 1");
				foreach($codes as $c)
				{
					$times_used = $c->count;
					$usage 		= $c->code_usage;	
				}
				if(count($codes) != 1)
				{
					$errors->add( $slug , apply_filters("piereg_invalid_invitaion_code",'<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.__(' Invalid Invitation Code.','piereg' )));		
						
				}
				elseif($times_used >= $usage and $usage != 0)
				{
					$errors->add( $slug , apply_filters("piereg_invitaion_code_expired",'<strong>'.__(ucwords('Error'),'piereg').'</strong>: '.__(' Invitation Code has expired.','piereg' )));
				}
				
			}
			else if($field['type']=="captcha")
			{
				$settings  		=  get_option("pie_register_2");
		 		$privatekey		= $settings['captcha_private'] ;
				require_once('wp-content/plugins/pie-register/recaptchalib.php');	
				
				$resp = recaptcha_check_answer ($privatekey,

												$_SERVER["REMOTE_ADDR"],
												$_POST["recaptcha_challenge_field"],
												$_POST["recaptcha_response_field"]);
				
				if (!$resp->is_valid) {				 
				  $errors->add('recaptcha_mismatch',"<strong>". __(ucwords("Error"),"piereg")."</strong>: ". apply_filters("piereg_Invalid_Security_Code",__("Invalid Security Code.", 'piereg')));
				}	
			
			}
			else if($field['type']=="name")
			{
				$field_name	= $_POST["first_name"];	
			}
			
			
			if( (!isset($field_name) || empty($field_name)) && $required)
			{
				$errors->add( $slug , "<strong>". __(ucwords("Error"),"piereg").":</strong> " .$validation_message );				
			}
			else if($rule=="number")
			{
				if(!is_numeric($field_name))
				{
					$errors->add( $slug , "<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_only_numbers",__(" field must contain only numbers." ,"piereg")));		
				}	
			}
			else if($rule=="alphanumeric")
			{
				if(! preg_match("/^([a-z0-9])+$/i", $field_name))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_may__alpha_numeric_characters",__(" field may only contain alpha-numeric characters."  ,"piereg")));		
				}	
			}	
			else if($rule=="email")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_EMAIL))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_field_must_contain_valid_email",__(" field must contain a valid email address." ,"piereg")));		
				}	
			}	
			else if($rule=="website")
			{
				if(!filter_var($field_name,FILTER_VALIDATE_URL))
				{
					$errors->add( $slug ,"<strong>". __(ucwords("Error"),"piereg").":</strong> ".$field['label'] .apply_filters("piereg_must_be_a_valid_URL",__(" must be a valid URL." ,"piereg")));
				}	
			}				 
		 }
		return $errors;
	}
	function addUser($user_id)
	{
		global $wpdb;
		foreach($this->data as $field)
		{
			//Some form fields which we can't save like paypal, submit,formdata
			if(!isset($field['meta']))
			{
				if($field['type']=="default")
				{
					/* && $field['field_name'] != 'url'*/
					$slug 				= $field['field_name'];				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
				}
				else if($field['type']=="invitation")
				{
					$prefix		= $wpdb->prefix."pieregister_";
					$codetable	= $prefix."code";				
					$codes 		= $wpdb->query( "update $codetable set count = count + 1 where name = '".$_POST['invitation']."' and status = 1");
					
					update_user_meta($user_id, "invite_code", $_POST['invitation']);			
				}
				else if($field['type']=="name")
				{
					$slug 				= "first_name";				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
					
					$slug 				= "last_name";				
					$value				= $_POST[$slug];
					update_user_meta($user_id, $slug, $value);	
				}
				else if($field['type']=="profile_pic")
				{
					$slug 			= $this->createFieldName($field['type']."_".$field['id']);
					$field_name		= $_POST[$slug];
					$this->pie_profile_pictures_upload($user_id,$field,$slug);
				}
				else if($field['type']=="upload")
				{
					$slug 			= $this->createFieldName($field['type']."_".$field['id']);
					$field_name		= $_POST[$slug];
					$this->pie_upload_files($user_id,$field,$slug);
				}
				else
				{
					$slug 				= $this->createFieldName($field['type']."_".$field['id']);
					$field_name			= $_POST[$slug];
					update_user_meta($user_id, "pie_".$slug, $field_name);
				}
			}
		}
	} 
	function countPageBreaks()
	{
		$pages = 1;
		if(count($this->data) > 0):
			foreach($this->data as $field)
			{
				if($field['type']=="pagebreak")
					$pages++;	
			}
		endif;
		return $pages ;
	}					
}
