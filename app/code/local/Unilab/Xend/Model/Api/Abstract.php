<?php 
abstract class Unilab_Xend_Model_Api_Abstract extends Varien_Object
{  
    protected $_config = null; 	
    protected $_cart   = null;
    protected $_client = null;
	
	protected $_rate 				= '0.00';   
	protected $_rates 				= array();
	protected $_valid_service_types = array();
	
	
	const XEND_SOAP_HEADER_URL  = 'https://www.xend.com.ph/api/';
	const XEND_SOAP_HEADER_Auth = 'AuthHeader';
	 
	const SERVICE_TYPE_METROMANILAEXPRESS = 'MetroManilaExpress';
	const SERVICE_TYPE_PROVINCIALEXPRESS  = 'ProvincialExpress';
	const SERVICE_TYPE_INTLPOSTAL         = 'InternationalPostal';
	const SERVICE_TYPE_INTLEXPRESS        = 'InternationalExpress';
	const SERVICE_TYPE_INTLEMS	          = 'InternationalEMS';
	const SERVICE_TYPE_RIZALMANILAEXPRESS = 'RizalMetroManilaExpress';
	
	
	const POUND_TO_KILOGRAM		= 0.453592;
	const GRAM_TO_KILOGRAM		= 0.001;
	const MILLIGRAM_TO_KILOGRAM	= 0.000001; 
	
	const PH_CODE			    = 'PH'; 
	
	protected $_service_types   = array('MetroManilaExpress'=>array('Metro Manila','Manila'),
										'ProvincialExpress'   =>array('Abra','Albay','Apayao','Aurora','Bataan','Batangas','Benguet','Bulacan','Cagayan','Camarines Sur',
																	  'Catanduanes','Cavite','Ilocos Norte','Ilocos Sur','Isabela','La Union','Laguna','Marinduque','Masbate',
																	  'Mindoro Occidental','Mindoro Oriental','Nueva Ecija','Nueva Vizcaya','Palawan','Pampanga',
																	  'Pangasinan','Quezon','Rizal','Romblon','Sorsogon','Tarlac','Zambales','Aklan',
																	  'Antique','Biliran','Bohol','Capiz','Cebu','Iloilo',
																	  'Leyte','Negros Occidental','Negros Oriental','Northern Samar',
																	  'Samar','Southern Leyte','Agusan del Norte','Agusan del Sur',
																	  'Basilan','Bukidnon','Compostela Valley','Davao','Davao del Sur',
																	  'Lanao del Norte','Lanao del Sur','Misamis Occidental','Misamis Oriental',
																	  'Sarangani','South Cotabato','Surigao del Norte',
																	  'Surigao del Sur','Zamboanga del Norte','Zamboanga del Sur',
																	  'Batanes','Camarines Norte','Mountain Province','Quirino','Eastern Samar','Guimaras',
																	  'Siquijor','Camiguin','Davao Oriental','North Cotabato','Maguindanao','Sultan Kudarat','Sulu','Tawi-Tawi'),
										'InternationalPostal' => array('Bangladesh','Brunei','Cambodia',"China, People's Republic of",
																		'Hong Kong','India','Indonesia','Korea, South','Laos','Malaysia',
																		'Nepal','Singapore','Sri Lanka','Taiwan','Thailand','Vietnam','American Samoa',
																		'Australia','Bahrain','Cook Islands','Fiji','French Polynesia','Guam',
																		'Iraq','Israel','Jordan','Kiribati','Kuwait','Lebanon','Marshall Islands',
																		'Micronesia, Federated States of','Mongolia','New Caledonia','New Zealand',
																		'Oman','Pakistan','Papua New Guinea','Qatar','Saudi Arabia','Solomon Islands',
																		'Syrian Arab Republic','United Arab Emirates','Yemen, Republic of','Afghanistan',
																		'Albania','Andorra','Austria','Belgium','Bosnia and Herzegovina','Bulgaria',
																		'Canada','Croatia','Cyprus','Czech Republic','Denmark','Finland','France',
																		'Georgia','Germany','Gibraltar','Greece','Ireland, Republic of','Italy','Liechtenstein',
																		'Luxembourg','Malta','Monaco (France)','Netherlands (Holland)','Norway','Poland','Portugal',
																		'Russia','Slovakia','Slovenia','Spain','Switzerland','Turkey','Ukraine',
																		'United Kingdom','United States','Algeria','Angola','Anguilla','Antigua and Barbuda',
																		'Argentina','Aruba','Bahamas','Belarus/Byelorussia','Belize','Benin','Bermuda',
																		'Bolivia','Botswana','Brazil','Burundi','Canary Islands (Spain)','Cayman Islands',
																		'Central African Republic','Chad','Chile','Colombia',"Costa Rica','Cote d'lvoire (Ivory Coast)",
																		'Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea',
																		'Eritrea','Estonia','Ethiopia','French Guiana','Gambia','Ghana','Grenada','Guadeloupe',
																		'Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland',
																		'Jamaica','Japan','Kazakhstan','Kenya','Latvia','Lesotho','Martinique','Mauritania',
																		'Mauritius','Mexico','Montserrat','Morocco','Mozambique','Namibia',
																		'Netherlands Antilles','Nicaragua','Niger','Nigeria','Panama','Paraguay','Peru','Puerto Rico',
																		'Reunion Island','Romania','Rwanda','Seychelles','South Africa','St. Kitts and Nevis',
																		'St. Lucia','St. Vincent & the Grenadines','Suriname','Sweden','Togo','Tonga','Trinidad & Tobago',
																		'Tunisia','Turkmenistan','Turks & Caicos Islands','Uganda','Uruguay','Vanuatu',
																		'Venezuela','Wallis & Futuna Islands','Zambia','Zimbabwe'),
										'InternationalEMS'=> array('Australia','Austria','Bahrain','Bangladesh','Belarus/Byelorussia','Belgium','Bhutan',
																  'Brazil','Bruneim','Bulgaria','Cambodia','Canada',"China, People's Republic of",'Cyprus','Denmark',
																  'Egypt','Fiji','Finland','France','Germany','Greece','Hong Kong','Hong Kong','Hungary',
																  'Iceland','India','Indonesia','Iran','Ireland, Republic of','Israel','Italy',
																  'Japan','Jordan','Korea, South','Kuwait','Laos','Luxembourg','Macau','Malaysia',
																  'Maldives','Mongolia','Myanmar','Nepal','Netherlands (Holland)','New Zealand','Norway',
																  'Oman','Pakistan','Papua New Guinea','Poland','Portugal','Qatar','Romania','Romania',
																  'Saudi Arabia','Singapore','Slovenia','Solomon Islands','South Africa','Spain','Sri Lanka',
																  'Sweden','Switzerland','Taiwan','Thailand','Ukraine','United Arab Emirates',
																  'United Kingdom','United States','Vietnam','Yemen, Republic of','Afghanistan','Albania',
																  'Algeria','American Samoa','Andorra','Angola','Anguilla','Antigua and Barbuda',"Argentina",
																  "Armenia","Aruba","Azerbaijan","Bahamas","Barbados","Belize","Benin","Bermuda","Bolivia",
																  "Bonaire (Netherlands Antilles)","Bosnia and Herzegovina","Botswana","Burkina Faso",
																  "Burundi","Cameroon","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile",
																  "Colombia","Comoros","Congo, Democratic Republic of","Congo, Democratic Republic of","Cook Islands",
																  "Costa Rica","Cote d'lvoire (Ivory Coast)","Croatia","Cuba","Curacao (Netherlands Antilles)","Czech Republic",
																  "Djibouti","Dominica","Dominican Republic","Ecuador","El Salvador","Equatorial Guinea","Eritrea","Estonia",
																  "Ethiopia","Faroe Islands","French Guiana","Gabon","Gambia","Georgia","Ghana","Gibraltar","Greenland","Grenada",
																  "Guadeloupe","Guam","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Jamaica","Kazakhstan",
																  "Kenya","Kirghizia (Kyrgyzstan)","Kiribati","Korea, North","Latvia","Lebanon","Lesotho","Liberia","Libyan Arab Jamahiriya",
																  "Liechtenstein","Lithuania","Macedonia (FYROM)","Madagascar","Malawi","Mali","Malta","Marshall Islands","Martinique","Mauritania",
																  "Mauritius","Mexico","Micronesia, Federated States of","Moldova","Monaco (France)","Montenegro","Montserrat","Morocco",
																  "Mozambique","Namibia","Nauru","New Caledonia","Nicaragua","Niger","Nigeria","Niue","Norfolk Island (Australia)",
																  "Panama","Paraguay","Peru","Puerto Rico","Reunion Island","Rwanda","Saipan (Northern Mariana Islands)","Samoa",
																  "Sao Tome and Principe","Senegal","Serbia","Seychelles","Sierra Leone","Slovakia","Somalia","St.Barthelemy (Guadeloupe)",
																  "St.Croix (U.S. Virgin Islands)","St.Eustatius (Netherlands Antilles)","St.Kitts and Nevis","St.Lucia",
																  "St.Maarten (Netherlands Antilles)","St.Thomas (U.S. Virgin Islands)","St.Vincent & the Grenadines",
																  "Sudan","Suriname","Swaziland","Syrian Arab Republic","Tahiti (French Polynesia)","Tajikistan","Tanzania, United Republic of",
																  "Togo","Tonga","Tortola (British Virgin Islands)","Trinidad & Tobago","Tunisia",
																  "Turkmenistan","Turks & Caicos Islands",
																  "Tuvalu","U.S. Virgin Islands","Uganda","Uruguay",
																  "Uzbekistan","Vanuatu","Venezuela","Zambia","Zimbabwe"),
									  'InternationalExpress'=> array("Malaysia","Thailand","Japan","Mongolia","Australia","Bangladesh","Bhutan","Cambodia",
																	"India","Laos ","Maldives","Nepal","New Zealand","Norfolk Island (Australia)",
																	"Pakistan","Sri Lanka","Vietnam","Canada","Mexico",
																	"Puerto Rico","United States","Belgium","Buesingen (Germany)",
																	"Campione/Lake Lugano (Italy)","Canary Islands (Spain)",
																	"Ceuta (Spain)","England (United Kingdom)","France","Germany",
																	"Heligoland (Germany)","Italy","Livigno (Italy)","Luxembourg",
																	"Melilla (Spain)","Monaco (France)","Netherlands (Holland)",
																	"Northern Ireland (United Kingdom)","Scotland (United Kingdom)",
																	"Spain","United Kingdom","Vatican City (Italy)","Wales (United Kingdom)",
																	"Aland Island (Finland)","Andorra","Austria","Azores (Portugal)",
																	"Channel Islands","Denmark","Faroe Islands","Finland","Greenland ",
																	"Guernsey (Channel Islands)","Ireland, Republic of","Jersey (Channel Islands)",
																	"Liechtenstein","Madeira (Protugal)","Norway","Portugal","San Marino","Sweden",
																	"Switzerland","American Samoa","Anguilla","Antigua and Barbuda","Argentina",
																	"Armenia","Aruba","Azerbaijan","Bahamas","Bahrain","Barbados","Belarus/Byelorussia",
																	"Belize","Bermuda","Bolivia","Bonaire (Netherlands Antilles)","Bosnia and Herzegovina",
																	"Brazil","British Virgin Islands","Bulgaria","Cayman Islands","Chile","Colombia",
																	"Cook Islands","Costa Rica","Croatia","Curacao (Netherlands Antilles)","Cyprus",
																	"Czech Republic","Dominica","Dominican Republic","East Timor (Timor Leste) ","Ecuador",
																	"Egypt","El Salvador","Estonia","Fiji","French Guiana","French Polynesia","Gibraltar",
																	"Greece","Grenada","Guadeloupe","Guam","Guatemala","Haiti","Honduras",
																	"Hungary","Iceland","Iraq","Jamaica","Kazakhstan","Kiribati",
																	"Kosrae (Micronesia, Federated States of)","Kuwait","Latvia",
																	"Lithuania","Malta","Marshall Islands","Martinique",
																	"Micronesia, Federated States of","Montenegro","Montserrat",
																	"Mount Athos (Greece)","Netherlands Antilles","New Caledonia",
																	"Nicaragua","Northern Mariana Islands","Oman","Palau","Panama",
																	"Papua New Guinea","Paraguay ","Peru","Poland",
																	"Ponape (Micronesia, Federated States of)","Qatar","Reunion Island",
																	"Romania","Rota (Northern Mariana Islands)","Russia","Saba (Netherlands Antilles)",
																	"Saipan (Northern Mariana Islands)","Samoa","Saudi Arabia","Serbia","Slovakia",
																	"Slovenia","Solomon Islands","St. Barthelemy (Guadeloupe)","St. Christopher (St. Kitts)",
																	"St. Croix (U.S. Virgin Islands)","St. Eustatius (Netherlands Antilles)",
																	"St. John (U.S. Virgin Islands)","St. Kitts and Nevis","St. Lucia",
																	"St. Maarten (Netherlands Antilles)","St. Martin (Guadeloupe)",
																	"St. Thomas (U.S. Virgin Islands)","St. Vincent & the Grenadines",
																	"Suriname","Tahiti (French Polynesia)","Tajikistan","Tinian (Northern Mariana Islands)",
																	"Tonga","Tortola (British Virgin Islands)","Trinidad & Tobago",
																	"Truk (Micronesia, Federated Islands)","Turkey","Turkmenistan",
																	"Turks & Caicos Islands","Tuvalu","U.S. Virgin Islands","Ukraine ",
																	"Union Islands (St. Vincent & the Grenadines)","United Arab Emirates",
																	"Uruguay","Uzbekistan","Vanuatu","Venezuela","Virgin Gorda (British Virgin Islands)",
																	"Wallis & Futuna Islands","Yap (Micronesia, Federated Islands)",
																	"Afghanistan","Albania","Algeria",
																	"Angola","Benin","Botswana","Burkina Faso","Burundi","Cameroon",
																	"Cape Verde","Central African Republic","Chad","Comoros",
																	"Congo (Brazzaville)","Congo, Democratic Republic of","Cote d'lvoire (Ivory Coast)",
																	"Djibouti","Equatorial Guinea","Eritrea","Ethiopia","Gabon","Gambia",
																	"Georgia","Ghana","Guinea","Guinea-Bissau","Guyana","Israel","Jordan",
																	"Kenya","Kirghizia (Kyrgyzstan)","Lebanon ","Lesotho","Liberia",
																	"Libyan Arab Jamahiriya","Macedonia (FYROM)","Madagascar","Malawi",
																	"Mali","Mauritania","Mauritius","Mayotte","Moldova","Morocco",
																	"Mozambique","Namibia","Niger","Nigeria","Rwanda","Senegal","Seychelles",
																	"Sierra Leone","South Africa","Swaziland","Syrian Arab Republic",
																	"Tanzania, United Republic of","Togo","Tunisia",
																	"Uganda","Yemen, Republic of","Zambia","Zimbabwe"),
									  'RizalMetroManilaExpress'=>array());
									  
	public function connect(Varien_Object $config)
	{ 
		$this->_config = $config; 
		try{   
			$client = new soapclient($config->getServiceUrl(), array());			
			$funcs  = $client->__getFunctions();
			
			$headerbody = array('UserToken' => $config->getUserToken()); 
			$header 	= new SOAPHeader(self::XEND_SOAP_HEADER_URL, self::XEND_SOAP_HEADER_Auth, $headerbody);
			
			$client->__setSoapHeaders($header);					
			$this->_client = $client; 
		}catch(Exception $e){			
			Mage::log($e->getMessage());
			//Mage::getSingleton('customer/session')->addError(Mage::helper('xend')->__('Could not establish connection to xend server.'));   
		} 	  
		
		return $this;
	} 
	
	public function getApiServiceUrl()
    {
        return $this->_config->getServiceUrl();
    }
	
    public function getApiUserToken()
    {
        return $this->_config->getUserToken();
    }
	 
	public function getApiDeveloperId()
	{
        return $this->_config->getDeveloperId();	
	} 
	
	public function getApiShipmentType()
	{
        return $this->_config->getShipmentType();	
	}  
	
	public function getServiceTypes()
	{
		return $this->_service_types;
	}
	
	public function getShipppingTypeLabel($shippingType)
	{	 
		if($shippingType == self::SERVICE_TYPE_METROMANILAEXPRESS){
			return Mage::helper("xend")->__("Metro Manila Express");
		}else if($shippingType == self::SERVICE_TYPE_PROVINCIALEXPRESS){
			return Mage::helper("xend")->__("Provincial Express");
		}else if($shippingType == self::SERVICE_TYPE_INTLPOSTAL){
			return Mage::helper("xend")->__("International Postal");
		}else if($shippingType == self::SERVICE_TYPE_INTLEXPRESS){
			return Mage::helper("xend")->__("International Express");
		}else if($shippingType == self::SERVICE_TYPE_INTLEMS){
			return Mage::helper("xend")->__("International EMS");
		}else if($shippingType == self::SERVICE_TYPE_RIZALMANILAEXPRESS){
			return Mage::helper("xend")->__("RIZAL-MANILA Express");
		}else{
			return Mage::helper("xend")->__("Unknown");
		}
	}
	
	
	
	/* Convert items weight to kilogram */
	protected function getConvertedWeight($weight,$weight_unit)
	{	
		$converted_weight = $weight;
		if($weight_unit == Unilab_Xend_Model_System_Config_Source_Shipping_Productweightunit::WEIGHT_UNIT_POUND){
			$converted_weight = $weight * self::POUND_TO_KILOGRAM;
		}elseif($weight_unit == Unilab_Xend_Model_System_Config_Source_Shipping_Productweightunit::WEIGHT_UNIT_GRAM){
			$converted_weight = $weight * self::GRAM_TO_KILOGRAM;
		}elseif($weight_unit == Unilab_Xend_Model_System_Config_Source_Shipping_Productweightunit::WEIGHT_UNIT_MILLIGRAM){
			$converted_weight = $weight * self::MILLIGRAM_TO_KILOGRAM;
		}
		return $converted_weight;
	}
	
	
	protected function filterShippingServices($customer_shipping_location,$allowed_destinations = array())
	{
		$services_type_location = $this->_service_types;		
		foreach($services_type_location as $service_type=>$service_locations){
			if(!in_array($service_type,$allowed_destinations)){continue;}
			if(in_array(strtolower($customer_shipping_location),array_map('strtolower',$service_locations))){
				$this->_valid_service_types[$service_type] = $service_type;
			}	
		} 
	} 
	
	public function getRates()
	{
		return $this->_rates;
	} 
	 
}
