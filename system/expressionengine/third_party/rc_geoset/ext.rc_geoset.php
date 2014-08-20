
<?php
	class Rc_geoset_ext {
	
	  var $name       = 'Red Carrot Geoset for Publisher';
	  var $version        = '1.0';
	  var $description    = 'Automatically send the user to the correct language ';
	  var $settings_exist = 'y';
	  var $docs_url       = '';
	
    var $settings       = array();

    function __construct($settings = '')
    {
        $this->settings = $settings;
    }
    
    function activate_extension()
		{
		  // Check that Publisher is installed
		
		    $this->settings = array();
				
		    $data = array(
		        'class'     => __CLASS__,
		        'method'    => 'change_language',
		        'hook'      => 'publisher_session_end',
		        'settings'  => serialize($this->settings),
		        'priority'  => 10,
		        'version'   => $this->version,
		        'enabled'   => 'y'
		    );
		
		    ee()->db->insert('extensions', $data);
		    
		}
		
		function update_extension($current = '')
		{
		    if ($current == '' OR $current == $this->version)
		    {
		        return FALSE;
		    }
		
		    if ($current < '1.0')
		    {
		        // Update to version 1.0
		    }
		
		    ee()->db->where('class', __CLASS__);
		    ee()->db->update(
		                'extensions',
		                array('version' => $this->version)
		    );
		}


		function disable_extension()
		{
		    ee()->db->where('class', __CLASS__);
		    ee()->db->delete('extensions');
		}
		
		function settings() {
      $settings = array();
    
      $languages = ee()->publisher_model->get_languages('short_name', TRUE);
      
      
      // Add a new text field for each language in Publisher
      foreach($languages as $language){
        $settings[$language['short_name']]      = array('i', '', "");
      }

      return $settings;
    }
  		
  		
    function save_settings()
    {
        if (empty($_POST))
        {
            show_error(lang('unauthorized_access'));
        }
    
        unset($_POST['submit']);        
    
        ee()->db->where('class', __CLASS__);
        ee()->db->update('extensions', array('settings' => serialize($_POST)));
    
        ee()->session->set_flashdata('message_success', lang('preferences_updated'));
    }

  		
  		
		function change_language(){
  		  // Check if we've already done the language change
  		  // for future reference, cf_set means "cloudflare language has been set"
  		  if(ee()->input->cookie('cf_set')!=1){
    		  
          // Grab the current country from CloudFlare
          $currentRegion = $_SERVER['HTTP_CF_IPCOUNTRY'];
          
          // We're going to construct an array with countrys as the key and the language as the value. The dataset is the extension's settings
          $languageSettings = array();

          // Find all of Publisher's languages
          $languages = ee()->publisher_model->get_languages('short_name', TRUE);

          // Find the settings for that language and split the piped string...
          foreach($languages as $language){
            $pipedString = $this->settings[$language['short_name']];
            $splitString = explode('|',$pipedString);
            
            // For each country in the piped string add it as a new index in the languageSettings array
            foreach($splitString as $country) {
              $languageSettings[$country] = $language['id']; // we use the ID because Publisher's set_language wants this
            }
          }



  		    // We've about to change the language, before we do we just acknowledge that so we don't process this again and get in a redirect loop
    		  ee()->input->set_cookie('cf_set',"1",(3600*24)*30);
  		  
  		    if(array_key_exists($currentRegion,$languageSettings)){
  		      ee()->publisher_session->set_language($languageSettings[$currentRegion]);
  		    }
  		  }
  		  
		}
				

		

	}
	// END CLASS

?>
