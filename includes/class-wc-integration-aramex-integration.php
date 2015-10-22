<?php
/**
 * Integration Demo Integration.
 *
 * @package  WC_Integration_Demo_Integration
 * @category Integration
 * @author   WooThemes
 */

if ( ! class_exists( 'WC_Integration_Aramex_Integration' ) ) :

class WC_Integration_Aramex_Integration extends WC_Integration {

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		global $woocommerce;

		$this->id                 = 'integration-aramex';
		$this->method_title       = __( 'Aramex Shipping Integration', 'woocommerce-integration-aramex' );
		$this->method_description = __( 'Setting for integration with Aramex shipping requests.', 'woocommerce-integration-aramex' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->entity          = $this->get_option( 'entity' );
		$this->pin              =   $this->get_option('pin');
		$this->country            =   $this->get_option('country');
		$this->account_num            = $this->get_option( 'account_num' );
		$this->username        = $this->get_option('username');
		$this->password         =$this->get_option('password');
		$this->address_1        =$this->get_option('address_1');
		$this->address_2        =$this->get_option('address_2');
		$this->address_3        =$this->get_option('address_3');
		$this->city             =$this->get_option('city');
		$this->state            =$this->get_option('state');
		$this->postcode         =$this->get_option('postcode');		
		$this->dept             =$this->get_option('dept');
		$this->contact_name     =$this->get_option('contact_name');
		$this->contact_title    =$this->get_option('contact_title');
		$this->phonenumber1     =$this->get_option('phonenumber1');
		$this->phonenumber1ext  =$this->get_option('phonenumber1ext');
		$this->phonenumber2     =$this->get_option('phonenumber2');
		$this->phonenumber2ext  =$this->get_option('phonenumber2ext');
		$this->faxnumber        =$this->get_option('faxnumber');
		$this->cellphone        =$this->get_option('cellphone');
		$this->emailaddress     =$this->get_option('emailaddress');
		$this->verbose_reporting          = true;

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
        add_action('woocommerce_order_status_processing', array($this,'place_shipment_request'));
		// Filters.
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
        add_filter( 'woocommerce_checkout_fields' , array($this,'add_shipping_phone_field'));
        add_action('woocommerce_checkout_process', 'validate_shipping_phone_number');

	}


	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'entity' => array(
				'title'             => __( 'Aramex Entity', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Aramex entity code.', 'woocommerce-integration-aramex' ),
				'desc_tip'          => true,
				'default'           => 'BOM'
			),
			'pin' => array(
				'title'             => __( 'Aramex Pin', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Aramex Pick Up Pin code.', 'woocommerce-integration-aramex' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'country' => array(
				'title'             => __( 'Aramex Country', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Aramex Pick Up Country Code.', 'woocommerce-integration-aramex' ),
				'desc_tip'          => true,
				'default'           => 'IN'
			),
			'account_num' => array(
				'title'             => __( 'Aramex Account Number', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Aramex Account Number.', 'woocommerce-integration-aramex' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'username' => array(
				'title'             => __( 'Aramex Username', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'             => __( 'Enter your Aramex Username', 'woocommerce-integration-aramex' ),
				'default'           => '',
			),
			'password' => array(
				'title'             => __( 'Aramex Password', 'woocommerce-integration-aramex' ),
				'type'              => 'password',
				'description'       => __( 'Enter your Aramex Password.', 'woocommerce-integration-aramex' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'address_1' => array(
				'title'             => __( 'Pick Up Address Line 1', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),		
            'address_2' => array(
				'title'             => __( 'Pick Up Address Line 2', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),			
            'address_3' => array(
				'title'             => __( 'Pick Up Address Line 3', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),		
            'city' => array(
				'title'             => __( 'Pick Up City', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
            'state' => array(
				'title'             => __( 'Pick Up State', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
            'postcode' => array(
				'title'             => __( 'Pick Up Postcode', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'dept' => array(
				'title'             => __( 'Pick Up Department', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'contact_name' => array(
				'title'             => __( 'Contact Name', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'contact_title' => array(
				'title'             => __( 'Contact Title', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'phonenumber1' => array(
				'title'             => __( 'Phone Number 1', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'phonenumber1ext' => array(
				'title'             => __( 'Phone Number 1 ext', 'woocommerce-integration-aramex' ),
				'type'              => 'textContact ',
				'description'       => '',
				'default'           => '',
			),
			'phonenumber2' => array(
				'title'             => __( 'Phone Number 2', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'phonenumber2ext' => array(
				'title'             => __( 'Phone Number 2 ext', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'faxnumber' => array(
				'title'             => __( 'Fax Number', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'cellphone' => array(
				'title'             => __( 'Cell Phone', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
			'emailaddress' => array(
				'title'             => __( 'Email Address', 'woocommerce-integration-aramex' ),
				'type'              => 'text',
				'description'       => '',
				'default'           => '',
			),
        );
	}


	/**
	 * Generate Button HTML.
	 */
	public function generate_button_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'class'             => 'button-secondary',
			'css'               => '',
			'custom_attributes' => array(),
			'desc_tip'          => false,
			'description'       => '',
			'title'             => '',
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<button class="<?php echo esc_attr( $data['class'] ); ?>" type="button" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo wp_kses_post( $data['title'] ); ?></button>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * Santize our settings
	 * @see process_admin_options()
	 */
	public function sanitize_settings( $settings ) {
		// We're just going to make the api key all upper case characters since that's how our imaginary API works
		if ( isset( $settings ) &&
		     isset( $settings['api_key'] ) ) {
			$settings['api_key'] = strtoupper( $settings['api_key'] );
		}
		return $settings;
	}


	/**
	 * Validate the API key
	 * @see validate_settings_fields()
	 */
	public function validate_api_key_field( $key ) {
		// get the posted value
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

		// check if the API key is longer than 20 characters. Our imaginary API doesn't create keys that large so something must be wrong. Throw an error which will prevent the user from saving.
		if ( isset( $value ) &&
			 20 < strlen( $value ) ) {
			$this->errors[] = $key;
		}
		return $value;
	}


	/**
	 * Display errors by overriding the display_errors() method
	 * @see display_errors()
	 */
	public function display_errors( ) {

		// loop through each error and display it
		foreach ( $this->errors as $key => $value ) {
			?>
			<div class="error">
				<p><?php _e( 'Looks like you made a mistake with the ' . $value . ' field. Make sure it isn&apos;t longer than 20 characters', 'woocommerce-integration-demo' ); ?></p>
			</div>
			<?php
		}
	}
	
	public function add_shipping_phone_field($fields)
	{
    	global $current_user;
    	$current_user=wp_get_current_user();
     $fields['shipping']['shipping_phone'] = array(
        'label'     => __('Phone', 'woocommerce'),
        'placeholder'   => _x('Phone', 'placeholder', 'woocommerce'),
        'required'  => true,
        'class'     => array('form-row-wide'),
        'clear'     => true
        );
    if (is_user_logged_in())
        {
            $shipping_phone = get_user_meta($current_user->ID,'shipping_phone',true);
            if (empty($shipping_phone))
            $fields['shipping']['shipping_phone']['default']=get_user_meta($current_user->ID,'billing_phone',true);            
            else
            $fields['shipping']['shipping_phone']['default']=get_user_meta($current_user->ID,'shipping_phone',true);
        }
     return $fields;
	}
	
	public function validate_shipping_phone_number()
	{
     global $woocommerce;
    // Check if set, if its not set add an error.
        if ($_POST['ship_to_different_address']==1 && (!isset($_POST['shipping_phone']) || empty($_POST['shipping_phone'])))
            wc_add_notice( '<strong>Please enter a shipping phone number.</strong>','error');
        else{
            if ($_POST['ship_to_different_address']==1){
            global $current_user;
            $current_user=wp_get_current_user();
            update_user_meta($current_user->ID,'shipping_phone',$_POST['shipping_phone']);
            }
        }
    }
    
	public function place_shipment_request($order_id)
	{
    		$order = new WC_Order($order_id);
    		$codamount = array('Value'	=> 0,
							'CurrencyCode'=>'INR');
            $cashadditionalamount=null;
            $cashadditionaldescription=null;
            $items = $order->get_items();
            $itemarr=array();
            foreach ($items as $item){
                $itemarr[]=$item['name'];
            }
    		if ($order->payment_method == 'cod')
    		{
        		$codamount['Value']=$order->order_total;
        		$cashadditionalamount=$order->order_total;
        		$cashadditionaldescription="Cash to be picked up on delivery";
    		}
    		
    		if (isset($order->shipping_address_1) && !empty($order->shipping_address_1)){    		
    		$consignee_address=  array(
    										'Line1'					=> $order->shipping_address_1,
    										'Line2'					=> $order->shipping_address_2,
    										'Line3'					=> '',
    										'City'					=> $order->shipping_city,
    										'StateOrProvinceCode'   => $order->shipping_state,
    										'PostCode'				=> $order->shipping_postcode,
    										'CountryCode'			=> $order->shipping_country
                                );
            $consignee_contact=array(
    										'Department'		=> '',
    										'PersonName'		=> $order->shipping_first_name." ".$order->shipping_last_name,
    										'Title'				=> '',
    										'CompanyName'		=> $order->shipping_first_name." ".$order->shipping_last_name,
    										'PhoneNumber1'		=> $order->shipping_phone,
    										'PhoneNumber1Ext'	=> '',
    										'PhoneNumber2'		=> '',
    										'PhoneNumber2Ext'	=> '',
    										'FaxNumber'			=> '',
    										'CellPhone'			=> $order->shipping_phone,
    										'EmailAddress'			=> $order->billing_email,
    										'Type'				=> ''
    										);
            }else
            {
            $consignee_address=  array(
    										'Line1'					=> $order->billing_address_1,
    										'Line2'					=> $order->billing_address_2,
    										'Line3'					=> '',
    										'City'					=> $order->billing_city,
    										'StateOrProvinceCode'   => $order->billing_state,
    										'PostCode'				=> $order->billing_postcode,
    										'CountryCode'				=> $order->billing_country
                                );
            $consignee_contact=array(
    										'Department'			=> '',
    										'PersonName'			=> $order->billing_first_name." ".$order->billing_last_name,
    										'Title'				=> '',
    										'CompanyName'		=> $order->billing_first_name." ".$order->billing_last_name,
    										'PhoneNumber1'			=> $order->billing_phone,
    										'PhoneNumber1Ext'		=> '',
    										'PhoneNumber2'			=> '',
    										'PhoneNumber2Ext'		=> '',
    										'FaxNumber'			=> '',
    										'CellPhone'			=> $order->billing_phone,
    										'EmailAddress'			=> $order->billing_email,
    										'Type'				=> ''
    										);
            }
                            
    		$soapClient = new SoapClient(__DIR__.'/Shipping.wsdl');
            $params = array(
    			'Shipments' => array(
    				'Shipment' => array(
    						'Shipper'	=> array(
    									    'Reference1' 	=> $order_id,
    										'Reference2' 	=> '',
    										'AccountNumber' => $this->account_num,
    										'PartyAddress'	=> array(
    											'Line1'				=> $this->address_1,
    											'Line2'				=> $this->address_2,
    											'Line3'				=> $this->address_3,
    											'City'				=> $this->city,
    											'StateOrProvinceCode'		=> $this->state,
    											'PostCode'			=> $this->postcode,
    											'CountryCode'			=> $this->country
    										),
    										'Contact'		=> array(
    											'Department'			=> $this->dept,
    											'PersonName'			=> $this->contact_name,
    											'Title'				=> $this->contact_title,
    											'CompanyName'			=> $this->company_name,
    											'PhoneNumber1'			=> $this->phonenumber1,
    											'PhoneNumber1Ext'		=> $this->phonenumber1ext,
    											'PhoneNumber2'			=> $this->phonenumber2,
    											'PhoneNumber2Ext'		=> $this->phonenumber2ext,
    											'FaxNumber'			=> $this->faxnumber,
    											'CellPhone'			=> $this->cellphone,
    											'EmailAddress'			=> $this->emailaddress,
    											'Type'				=> ''				
    										),
    						),						
    						'Consignee'	=> array(
    									'Reference1'	=> $order_id,
    									'Reference2'	=> '',
    									'AccountNumber' => '',
    									'PartyAddress'	=>$consignee_address,
    									
    									'Contact' => $consignee_contact,
    						),
    						
    						'ThirdParty' => array(
    										'Reference1' 	=> '',
    										'Reference2' 	=> '',
    										'AccountNumber' => '',
    										'PartyAddress'	=> array(
    											'Line1'				=> '',
    											'Line2'				=> '',
    											'Line3'				=> '',
    											'City'				=> '',
    											'StateOrProvinceCode'		=> '',
    											'PostCode'			=> '',
    											'CountryCode'			=> ''
    										),
    										'Contact'		=> array(
    											'Department'			=> '',
    											'PersonName'			=> '',
    											'Title'				=> '',
    											'CompanyName'			=> '',
    											'PhoneNumber1'			=> '',
    											'PhoneNumber1Ext'		=> '',
    											'PhoneNumber2'			=> '',
    											'PhoneNumber2Ext'		=> '',
    											'FaxNumber'			=> '',
    											'CellPhone'			=> '',
    											'EmailAddress'			=> '',
    											'Type'				=> ''							
    										),
    						),
    						        'Reference1' => $order_id,
    								'Reference2' => '',
    								'Reference3' => '',
    								'ForeignHAWB' => '',
    								'TransportType'	=> 0,
    
    								'ShippingDateTime' => time(),
    								'DueDate' => time(),
    
    								'PickupLocation' => 'Reception',
    								'PickupGUID' => '',
    								'Comments' => '',
    								'AccountingInstrcutions' => '',
    								'OperationsInstructions' => '',
    								
    								'Details' => array(
    									'Dimensions' => array(
    										'Length'=> 0,
    										'Width'	=> 0,
    										'Height'=> 0,
    										'Unit'=> 'cm',
    									),
    									
    									'ActualWeight' => array(
    										'Value'=> 0.5,
    										'Unit'=> 'Kg',
    									),
                                        'ChargeableWeight' => array(
											'Value'					=> 0.5,
											'Unit'					=> 'Kg'
										),		
    									
    									'ProductGroup' => 'DOM',
    									'ProductType'=> 'CDA',
    									'PaymentType'=> 'P',
    									'PaymentOptions' => '',
    									'Services'=> 'CODS',
    									'NumberOfPieces'=> $order->get_item_count(),
    									'DescriptionOfGoods'=> 'Docs',
    									'GoodsOriginCountry'=> 'IN',
    									
    									'CashOnDeliveryAmount' 	=> $codamount,
    									
    									'InsuranceAmount'		=> NULL,
    									
    									'CollectAmount'			=> NULL,
    									
    									'CashAdditionalAmount'	=> NULL,
    									
    									'CashAdditionalAmountDescription' => NULL,
    									
    									'CustomsValueAmount' => Null,
    																	
    				
    										
    										'Items' 			=> array(),
    								),
    				),
    		),
    			'ClientInfo'  			=> array      ( 
    				         					'AccountCountryCode'	        => $this->country,
    										'AccountEntity'		 	=> $this->entity,
    										'AccountNumber'		 	=> $this->account_num,
    										'AccountPin'		 	=> $this->pin,
    										'UserName'			=>$this->username,
    										'Password'			=> $this->password,
    										'Version'			=> 'v1.0'),
    			'Transaction' 			=> array(
    										'Reference1'			=> $order_id,
    										'Reference2'			=> '', 
    										'Reference3'			=> '', 
    										'Reference4'			=> '', 
    										'Reference5'			=> '',									
    									),
    			'LabelInfo'				=> array(
    										'ReportID' 			=> 9729,
    										'ReportType'			=> 'URL'),
    	);
    	$params['Shipments']['Shipment']['Details']['Items'][] = array(
		'PackageType' 	=> 'Box',
		'Quantity'		=> 1,
		'Weight'		=> array(
				'Value'		=> 0.5,
				'Unit'		=> 'Kg',		
		),
		'Comments'		=> 'Docs',
		'Reference'		=> ''
	);
	
	try {
        update_post_meta($order_id,'aramex_createshipment_request',json_encode($params));
		$auth_call = $soapClient->CreateShipments($params);
		update_post_meta($order_id,'aramex_createshipment_response',json_encode($auth_call));
		if ($auth_call->Notifications->HasErrors==1 || $auth_call->HasErrors == 1)
		    {
        		    //Shipping call failed
            		$msg = "Aramex Create Shipment Request Failed due to the following error(s):<br>";
            		foreach ($auth_call->Notifications as $notification)
            		    {
                		    $msg.="Error ".$notification->Code.": ".$notification->Message."<br>";
            		    }
            		$order->add_order_note($msg);
                    if ($this->verbose_reporting == true)
                        wp_mail(get_bloginfo('admin_email'), 'Create Shipment request failed. Order ID:'.$order_id, $msg);
		    }
		  else
		  {
    		  //print_r($auth_call);
    		  //Shipping call was successfull
              $shipment_id = $auth_call->Shipments->ProcessedShipment->ID;
              $sipping_label = $auth_call->Shipments->ShipmentLabel->LabelURL;
              update_post_meta($order_id,'shipment_id',$shipment_id);
              $order->add_order_note("Aramex Shipment Request Successfull, ID:".$shipment_id);
              $this->get_shipment_label($order_id,$shipment_id);
              $this->make_pickup_request($order_id);
		  }
    	} catch (SoapFault $fault) {
        	//Aramex API Failed
    		$order->add_order_note($fault->faultstring);
            $message = "The system was unable to place a shipment and pick up request for Oder ID".$order_id."/r/n The error we received from Aramex is as follows:/r/n".$fault->faultstring."/r/n";
            wp_mail(get_bloginfo('admin_email'), 'Shipping request failed. Order ID:'.$order_id, $message);

    	}
    		
	}
	
	public function get_shipment_label($order_id,$shipment_id)
	{
  		$order = new WC_Order($order_id);
  		$soapClient = new SoapClient(__DIR__.'/Shipping.wsdl');
    	$params = array(
		'ClientInfo'  			=> array( 
    				         			    'AccountCountryCode'	=> $this->country,
    										'AccountEntity'		 	=> $this->entity,
    										'AccountNumber'		 	=> $this->account_num,
    										'AccountPin'		 	=> $this->pin,
    										'UserName'			    =>$this->username,
    										'Password'			    => $this->password,
    										'Version'			    => 'v1.0'),

		'Transaction' 			=> array(
    										'Reference1'			=> $order_id,
    										'Reference2'			=> '', 
    										'Reference3'			=> '', 
    										'Reference4'			=> '', 
    										'Reference5'			=> '',									
    									),
   		'ShipmentNumber'  => $shipment_id,
		'LabelInfo'				=> array(
										'ReportID' 	       => '9729',
										'ReportType'	       => 'URL',
			),					
            );
            try {
              //  update_post_meta($order_id,"shipment_label_request",json_encode($params));
                $auth_call = $soapClient->PrintLabel($params);
               // update_post_meta($order_id,"shipment_label_response",json_encode($auth_call));
                if (empty($auth_call->HasErrors) || ($auth_call->HasErrors==0)){
                    $shipping_label_url = $auth_call->ShipmentLabel->LabelURL;
                    $order->add_order_note("Aramex Shipment Label: <a href='".$shipping_label_url."' target='_blank'>Click Here</a>");
                    update_post_meta($order_id,'shipping_label',$shipping_label_url);
                }
                else{
            		$msg = "Aramex Shipment Label Request Failed due to the following error(s):<br>";
            		foreach ($auth_call->Notifications as $notification)
            		    {
                		    $msg.="Error ".$notification->Code.": ".$notification->Message."<br>";
            		    }
            		$order->add_order_note($msg);
                    if ($this->verbose_reporting == true)
                        wp_mail(get_bloginfo('admin_email'), 'Shipment label request failed. Order ID:'.$order_id, $msg);
        		}
                } 
            catch (SoapFault $fault) 
                {
                 $order->add_order_note("Failed generating shipment label. Error:".$fault->faultstring);
                 $message = "The system was unable to create a shipment label request for Order ID".$order_id."/r/n The error we received from Aramex is as follows:/r/n".$fault->faultstring."/r/n";
                 wp_mail(get_bloginfo('admin_email'), 'Shipping request failed. Order ID:'.$order_id, $message);
	            }
	   
            return;
	}
	
	public function make_pickup_request($order_id)
	{
    	$order = new WC_Order($order_id);
  		$soapClient = new SoapClient(__DIR__.'/Shipping.wsdl');
        date_default_timezone_set('Asia/Calcutta');
        $time = current_time('H',true);
        $day = current_time('N');
        //If greater than 3:00 PM
        if (($time)>=15)
        {
                $offset = " + 2 days";
                
          $order->add_order_note('Order placed after 3:00 PM cut off time');
        }
        else
        {
                $offset =' + 1 days';
            
            $order->add_order_note('Order placed before 3:00 PM cut off time');
        }
        switch ($day){
            case '5':
                $offset=' + 3 days';
                break;
            case '6':
                $offset=' + 2 days';
                break;
            case '7':
                $offset=' + 2 days';
                break;
            default:
                break;
        }
        $format = 'Y-m-d\TH:i:s';
        $pickupdate = date($format,strtotime(date("Y-m-d H:i:s",mktime(11,30,0)).$offset));
        $readytime = date($format,strtotime(date("Y-m-d H:i:s",mktime(12,30,0)).$offset));
        $lastpickuptime = strtotime(date($format,strtotime(date("Y-m-d H:i:s",mktime(17,30,0)).$offset)));
        $closingtime = strtotime(date($format,strtotime(date("Y-m-d H:i:s",mktime(19,00,0)).$offset)));
        $shippingdatetime = $pickupdate;
        $order->add_order_note("Pick up request time:".date("Y-m-d H:i:s",strtotime($pickupdate)));
        	$params = array(
			'Pickup' => array(
								'PickupAddress'	=> array(
    											'Line1'				=> $this->address_1,
    											'Line2'				=> $this->address_2,
    											'Line3'				=> $this->address_3,
    											'City'				=> $this->city,
    											'StateOrProvinceCode'		=> $this->state,
    											'PostCode'			=> $this->postcode,
    											'CountryCode'			=> $this->country
    										),
								'PickupContact'		=> array(
    											'Department'			=> $this->dept,
    											'PersonName'			=> $this->contact_name,
    											'Title'				=> $this->contact_title,
    											'CompanyName'			=> $this->company_name,
    											'PhoneNumber1'			=> $this->phonenumber1,
    											'PhoneNumber1Ext'		=> $this->phonenumber1ext,
    											'PhoneNumber2'			=> $this->phonenumber2,
    											'PhoneNumber2Ext'		=> $this->phonenumber2ext,
    											'FaxNumber'			=> $this->faxnumber,
    											'CellPhone'			=> $this->cellphone,
    											'EmailAddress'			=> $this->emailaddress,
    											'Type'				=> ''				
    										),
								'PickupLocation' 			=> 'Reception',
								'PickupDate' 				=> $pickupdate,
								'ReadyTime' 				=> $readytime,
								'LastPickupTime'			=> $lastpickuptime,
								'ClosingTime'				=> $closingtime,
								'ShippingDateTime' 			=> $shippingdatetime,
								'Comments'				=>  '',
								'Reference1'				=> $order_id,
								'Reference2'				=> '',
								'Vehicle'				=> '',
								'Status' 				=> 'Ready',
								
								'PickupItems' 			=> array(
									
									'PickupItemDetail' 			=> array(
											'ProductGroup' 			=> 'DOM',
											'ProductType'			=> 'ONP',
											'Payment'			=> 'P',
											'NumberOfShipments' 		=> 1,
											'PackageType'			=> '',
											'NumberOfPieces'		=> $order->get_item_count(),
											'Comments' 			=> '',
											'ShipmentWeight' => array(
													'Value'		=> 0.5,
													'Unit'		=> 'Kg'
														),
											'ShipmentVolume' => array(
													'Value'		=> 0.5,
													'Unit'		=> 'Kg'
														),
											'CashAmount' 	=> array(
													'Value'		=> 0,
													'CurrencyCode'	=> ''
														),
											'ExtraCharges' 	=> array(
													'Value'		=> 0,
													'CurrencyCode'	=> ''
														),
											'ShipmentDimensions' => array(
													'Length'	=> 0,
													'Width'		=> 0,
													'Height'	=> 0,
													'Unit'		=> 'cm',
											
														),
										),
									),
								),

			'ClientInfo'  => array( 
    				       		    'AccountCountryCode'	=> $this->country,
    								'AccountEntity'		 	=> $this->entity,
    								'AccountNumber'		 	=> $this->account_num,
    								'AccountPin'		 	=> $this->pin,
    								'UserName'			    =>$this->username,
    								'Password'			    => $this->password,
    								'Version'			    => 'v1.0'),

			'Transaction' 			=> array(
										'Reference1'			=> $order_id,
										'Reference2'			=> '', 
										'Reference3'			=> '', 
										'Reference4'			=> '', 
										'Reference5'			=> '',									
									),
			'LabelInfo'				=> Null,
            );
            update_post_meta($order_id,'aramex_pickup_request',json_encode($params));
            	try {
        		$auth_call = $soapClient->CreatePickup($params);
            update_post_meta($order_id,'aramex_pickup_response',json_encode($auth_call));
        		if (empty($auth_call->HasErrors) || $auth_call->HasErrors == 0)
        		{
                    $pickup_id = $auth_call->ProcessedPickup->ID;
                    $pickup_guid = $auth_call->ProcessedPickup->GUID;
                    $order->add_order_note("Aramex Pickup Request Successful <br> 
                    Pickup Request ID:".$pickup_id."<br>Pickup Request GUID:".$pickup_guid);
                    update_post_meta($order_id,'pickup_id',$pickup_id);
                    update_post_meta($order_id,'pickup_guid',$pickup_guid);
        		}
        		else{
            		$msg = "Aramex Pickup Request Failed due to the following error(s):<br>";
            		foreach ($auth_call->Notifications as $notification)
            		    {
                		    $msg.="Error ".$notification->Code.": ".$notification->Message."<br>";
            		    }
            		$order->add_order_note($msg);
                    if ($this->verbose_reporting == true)
                    wp_mail(get_bloginfo('admin_email'), 'Pick up request failed. Order ID:'.$order_id, $msg);
        		}
        	} catch (SoapFault $fault) {
        		$order->add_order_note("Failed creating Aramex pickup request. Error:".$fault->faultstring);
                 $message = "The system was unable to create an Aramex pickup request for Order ID".$order_id."/r/n The error we received from Aramex is as follows:/r/n".$fault->faultstring."/r/n";
                 if ($this->verbose_reporting == true)
                     wp_mail(get_bloginfo('admin_email'), 'Pick up request failed. Order ID:'.$order_id, $message);

        	}
	}


}

endif;
