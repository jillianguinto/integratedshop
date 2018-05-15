<?php



class RRA_Admincontroller_Model_Attribute_Source_Sources extends Mage_Eav_Model_Entity_Attribute_Source_Abstract

{

    public function getAllOptions()
    {

		
		for($desc = 0; $desc <= 2; $desc++){

			if($desc == 0){

		
				$label = "Web";

				$val = 0;

			}

			if($desc == 1){

				$label = "Mobile";

				$val = 1;


			}

			if($desc == 2){

				

				$label = "CRM";

				$val = 2;



			}

					$sourceid[] = array(

							'value'     => $val,



							'label'      => $label,

						);		

			  



		}



		return $sourceid;		



    }

	

	

	 public function toOptionArray()

    {



        return $this->getAllOptions();



    }

}

