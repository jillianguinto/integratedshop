	<?php 
	//model

	class Summod extends CI_Model {
	    

	    function __construct()
		{
			parent::__construct();
			$this->load->database();

		}

		//summary report

		function getYear($store_ids){

			$sql = "SELECT DISTINCT(DATE_FORMAT( created_at,'%Y')) AS`Year`
					FROM sales_flat_order
					WHERE store_id in($store_ids)";

			$sqlResult = $this->db->query($sql);	
	
			return $sqlResult->result();
		}


		function getMonth($store_ids){

			$sql = "SELECT DISTINCT(DATE_FORMAT( created_at,'%M')) AS `month`
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}


		function getPaymentStatus($store_ids){

			$sql = "SELECT DISTINCT(status) AS`payment_status`
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		
		function getGender($store_ids){

			$sql = "SELECT DISTINCT(customer_gender) AS customer_gender
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}
	
		function getCountry($store_ids){

			$sql = "SELECT DISTINCT(sales_flat_order_address.country_id) AS country
					FROM sales_flat_order 
					INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
					WHERE sales_flat_order.store_id in($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		function getStatus($store_ids){

			$sql = "SELECT DISTINCT(status) AS status
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}
		function getPromo($store_ids){

			$sql = "SELECT DISTINCT(coupon_rule_name) AS promo
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}



		function getCity($store_ids){

			$sql = "SELECT DISTINCT(sales_flat_order_address.city) AS city
					FROM sales_flat_order 
					INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
					WHERE sales_flat_order.store_id IN($store_ids)
					ORDER BY sales_flat_order_address.city ASC";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		function getDet($store_ids){
			$sql='SELECT
				CASE(sales_flat_order.store_id) 
				WHEN 21 THEN "RiteMed" WHEN 20 THEN "Family Health at Home" 
				WHEN 19 THEN "Unilab Active Health" 
				WHEN 12 THEN "Heymom" 
				WHEN 11 THEN "Clickhealthplus" 
				WHEN 10 THEN "Celeteque" 
				WHEN 6 THEN "Evolife" 
				WHEN 1 THEN "Clickhealth" END `Store Name`,
				sales_flat_order_item.order_id,
				sales_flat_order_item.sku AS `Item Code`,
				sales_flat_order_item.`name` AS `Item Name`,
				CONCAT(FORMAT(qty_ordered,2)) AS Quantity,
				CONCAT("? ",FORMAT(base_original_price,2)) AS `Item Price`,
				CONCAT("? ",FORMAT(qty_ordered * base_original_price ,2)) AS `Order Amount`,
				sales_flat_order.increment_id AS `Order Number`,
				sales_flat_order. coupon_rule_name AS `Promo Name`,
				sales_flat_order.discount_amount AS `Discount Amount`,
				CONCAT("? ",FORMAT(base_shipping_amount,2)) AS `Shipping Rate`,
				sales_flat_order.`status` as "Payment Status",
				DATE_FORMAT(sales_flat_order_item.created_at,"%m/%d/%Y") AS `Order Date`,
				DATE_FORMAT(sales_flat_order_item.created_at,"%M") AS `Month`,
				CONCAT(sales_flat_order.customer_firstname," ",sales_flat_order.customer_lastname) AS `Shopper"s Name`,
				sales_flat_order_address.email AS Email,
				IF(sales_flat_order.customer_gender = 1, "Male", "Female") AS Gender,
				sales_flat_order_address.country_id AS Country,
				sales_flat_order_address.street AS Address,
				sales_flat_order_address.city AS City,
				sales_flat_order_address.telephone AS Telephone,
				sales_flat_order_address.mobile AS Mobile,
				DATE_FORMAT(sales_flat_order_item.created_at,"%Y") AS `Year`
				FROM sales_flat_order_item
				INNER JOIN sales_flat_order ON sales_flat_order_item.order_id = sales_flat_order.entity_id
				LEFT JOIN sales_flat_order_address ON sales_flat_order_address.entity_id = sales_flat_order.billing_address_id
				WHERE sales_flat_order.store_id IN($store_ids)
				GROUP BY item_id ORDER BY order_id DESC';
		
			$sqlResult = $this->db->query($sql);
			return $sqlResult->result();
		}

		function getsum0($true_store_id,$month,$gender,$sort,$sortBy,$city,$status,$promo,$between,$and,$year)
		{
			

			$sql="SELECT CASE(sales_flat_order.store_id)
			WHEN 21 THEN 'RiteMed'
			WHEN 20 THEN 'Family Health at Home' 
			WHEN 19 THEN 'Unilab Active Health' 
			WHEN 12 THEN 'Heymom' 
			WHEN 11 THEN 'Clickhealthplus' 
			WHEN 10 THEN 'Celeteque' 
			WHEN 6 THEN 'Evolife' 
			WHEN 1 THEN 'Clickhealth' END AS `Store Name`, 			
			sales_flat_order.increment_id AS `Order Number`,
			CONCAT(customer_firstname,' ',customer_lastname) AS `Shopper's Name`, 
			grand_total AS `Order Total`, 
			base_subtotal AS `Order Amount`, 
			base_shipping_amount AS `Order Shipping`, 
			sales_flat_order. coupon_rule_name AS `Promo Name`, 
			sales_flat_order.discount_amount AS `Discount Amount`,
			sales_flat_order.`status` as 'Payment Status',
			DATE_FORMAT(created_at,'%d-%M-%Y') AS `Order Date`, 
			sales_flat_order.unilab_waybill_number AS `Waybill Number`, 
			IF(sales_flat_order.customer_gender = 1, 'Male','Female') AS `Gender`,	
			sales_flat_order_address.country_id AS `Country`,
			sales_flat_order_address.street AS `Address`, 
			sales_flat_order_address.city AS `City`,

			-- sales_flat_order.entity_id AS `Order ID`, 	
			-- DATE_FORMAT(updated_at,'%d-%M') AS `Moderation Date`, 
			DATE_FORMAT(created_at,'%M') AS `Month`,		
			DATE_FORMAT(created_at,'%Y') AS `Year` 

			FROM sales_flat_order 
			INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
			WHERE sales_flat_order.store_id in($true_store_id)"; 

			// echo $between .'asdasd';
			if($between){ 
			$getdBetween = $between;
			$sql.="AND sales_flat_order.created_at BETWEEN STR_TO_DATE('$getdBetween','%d-%M-%Y')";			
			}
		

			if($and){				
				$getdAnd =$and;
				$sql.="AND STR_TO_DATE('$getdAnd','%d-%M-%Y')";	

			}
			
			if($year){
				$GtYear = $year;
				$sql.="AND YEAR(created_at) = '$GtYear'";
			}
			
			// echo $GtYear . 'asdasd';

			if($month){
				$GtMonth = $month;
				$sql.="AND MONTH(created_at) = '$GtMonth'";	
			}
						


			if($gender){
				$GtGender = $gender;
				$sql.="AND sales_flat_order.customer_gender = '$GtGender'";

			}
			
		
			if($status){
				$GtStatus = $status;
				
				$sql.="AND  sales_flat_order.status = '$GtStatus'";

			}
			// echo $city . 'asdasd';
				
			if(($city)){
			
				$GtCity = $city;
				$sql.="AND sales_flat_order_address.city = '$GtCity'";

			}
			
			
			if(($promo)){
				$GtdPromo = $promo;
				$sql.="AND sales_flat_order.coupon_rule_name = '$GtdPromo' ";

			}
			// echo $sort .'asdasd';
			if($sort){
			$sort = $sort;
			$sortby = $sortBy;
			$sql.=" GROUP BY increment_id ORDER BY $sort $sortby";
				}
				else
				{
				$sql.="GROUP BY increment_id ORDER BY sales_flat_order.created_at ASC ";
				}
			

		//	$sql.="GROUP BY increment_id ORDER BY sales_flat_order.created_at ASC ";

			 
// echo '<pre>'.$sql;
			// die();
			$sqlResult = $this->db->query($sql);
			// print_r($sqlResult->result());
			// die();
			return $sqlResult->result();
		}

		function getsum0Export(){
			$wid = isstore_id();
			$storeid=astore_id($wid);

			$where_id =  store_id(isstore_id());

			$sql="SELECT CASE(sales_flat_order.store_id)
			WHEN 21 THEN 'RiteMed'
			WHEN 20 THEN 'Family Health at Home' 
			WHEN 19 THEN 'Unilab Active Health' 
			WHEN 12 THEN 'Heymom' 
			WHEN 11 THEN 'Clickhealthplus' 
			WHEN 10 THEN 'Celeteque' 
			WHEN 6 THEN 'Evolife' 
			WHEN 1 THEN 'Clickhealth' END AS `Store Name`, 
			sales_flat_order.entity_id AS `Order ID`, sales_flat_order.increment_id 
			AS `Order Number`, CONCAT('? ',FORMAT(grand_total,2)) AS `Order Total`, CONCAT('? ',FORMAT(base_subtotal,2))
			AS `Order Amount`, CONCAT('? ',FORMAT(base_shipping_amount,2)) AS `Order Shipping`, sales_flat_order. coupon_rule_name 
			AS `Promo Name`, sales_flat_order.discount_amount AS `Discount Amount`, 
			sales_flat_order.`status` as 'Payment Status',
			DATE_FORMAT(created_at,'%d-%M') AS `Order Date`, DATE_FORMAT(updated_at,'%d-%M') AS `Moderation Date`, 
			DATE_FORMAT(created_at,'%M') AS `Month`, sales_flat_order.unilab_waybill_number AS `Waybill Number`, 
			CONCAT(customer_firstname,' ',customer_lastname) AS `Shopper's Name`, IF(sales_flat_order.customer_gender = 1, 'Male', 
			'Female') AS Gender, sales_flat_order_address.country_id AS Country, sales_flat_order_address.street AS `Address`, 
			sales_flat_order_address.city AS City, DATE_FORMAT(created_at,'%Y') AS `Year` 
			FROM sales_flat_order 
			INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
			WHERE sales_flat_order.store_id ='$where_id'
			GROUP BY increment_id ORDER BY created_at ASC";

			$sqlResult = $this->db->query($sql);
			return $sqlResult->result();
		}

		function exportsum0(){

			$wid = isstore_id();
			$storeid=astore_id($wid);

			$sql="
			SELECT CASE(sales_flat_order.store_id)
			WHEN 21 THEN 'RiteMed'
			WHEN 20 THEN 'Family Health at Home' 
			WHEN 19 THEN 'Unilab Active Health' 
			WHEN 12 THEN 'Heymom' 
			WHEN 11 THEN 'Clickhealthplus' 
			WHEN 10 THEN 'Celeteque' 
			WHEN 6 THEN 'Evolife' 
			WHEN 1 THEN 'Clickhealth' END AS `Store Name`, 			
			sales_flat_order.increment_id AS `Order Number`,
			CONCAT(customer_firstname,' ',customer_lastname) AS `Shopper's Name`, 
			CONCAT('? ',FORMAT(grand_total,2)) AS `Order Total`, 
			CONCAT('? ',FORMAT(base_subtotal,2)) AS `Order Amount`, 
			CONCAT('? ',FORMAT(base_shipping_amount,2)) AS `Order Shipping`, 
			sales_flat_order. coupon_rule_name AS `Promo Name`, 
			sales_flat_order.discount_amount AS `Discount Amount`, 
			sales_flat_order.`status` as 'Payment Status',
			DATE_FORMAT(created_at,'%d-%M-%Y') AS `Order Date`, 
			sales_flat_order.unilab_waybill_number AS `Waybill Number`, 
			IF(sales_flat_order.customer_gender = 1, 'Male','Female') AS Gender,	
			sales_flat_order_address.country_id AS Country,
			sales_flat_order_address.street AS `Address`, 
			sales_flat_order_address.city AS City,

			sales_flat_order.entity_id AS `Order ID`, 	
			DATE_FORMAT(updated_at,'%d-%M') AS `Moderation Date`, 
			DATE_FORMAT(created_at,'%M') AS `Month`,		
			DATE_FORMAT(created_at,'%Y') AS `Year` 

			FROM sales_flat_order 
			INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
			WHERE sales_flat_order.store_id ='$storeid'
			GROUP BY increment_id ORDER BY created_at ASC";

			$sqlResult = $this->db->query($sql);
			return $sqlResult->result();

		}	

		//details report
		function getdYear($store_ids){

			$sql = "SELECT  distinct
			DATE_FORMAT(sales_flat_order_item.created_at,'%Y') AS `Year`		
			

			FROM sales_flat_order_item
			INNER JOIN sales_flat_order ON sales_flat_order_item.order_id = sales_flat_order.entity_id
			LEFT JOIN sales_flat_order_address ON sales_flat_order_address.entity_id = sales_flat_order.billing_address_id 
			
			WHERE  sales_flat_order.store_id in($store_ids) order by DATE_FORMAT(sales_flat_order_item.created_at,'%Y')";

			$sqlResult = $this->db->query($sql);	
	
			return $sqlResult->result();
		}


		function getdMonth($store_ids){

			$sql = "SELECT DISTINCT(DATE_FORMAT( sales_flat_order_item.created_at,  '%M' )) AS  `Month` 
					FROM sales_flat_order_item
					INNER JOIN sales_flat_order ON sales_flat_order_item.order_id = sales_flat_order.entity_id
					LEFT JOIN sales_flat_order_address ON sales_flat_order_address.entity_id = sales_flat_order.billing_address_id
					WHERE sales_flat_order.store_id in($store_ids)";
					
			

			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}


		function getdPaymentStatus($store_ids){

			$sql = "SELECT DISTINCT(status) AS`payment_status`
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		
		function getdGender($store_ids){

			$sql = "SELECT DISTINCT(customer_gender) AS customer_gender
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}
	
		function getdCountry($store_ids){

			$sql = "SELECT DISTINCT(sales_flat_order_address.country_id) AS country
					FROM sales_flat_order 
					INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
					WHERE sales_flat_order.store_id in($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}
		
		function getdPromo($store_ids){

			$sql = "SELECT DISTINCT(coupon_rule_name) AS promo
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}
		
		function getdItemName($store_ids){

			$sql = "SELECT DISTINCT(name) AS name
					FROM sales_flat_order_item
					WHERE store_id IN($store_ids)";
				
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		function getdStatus($store_ids){

			$sql = "SELECT DISTINCT(status) AS status
					FROM sales_flat_order
					WHERE store_id IN($store_ids)";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}



		function getdCity($store_ids){

			$sql = "SELECT DISTINCT(sales_flat_order_address.city) AS city
					FROM sales_flat_order 
					INNER JOIN sales_flat_order_address ON sales_flat_order.customer_email = sales_flat_order_address.email 
					WHERE sales_flat_order.store_id IN($store_ids)
					ORDER BY sales_flat_order_address.city ASC";
			$sqlResult = $this->db->query($sql);		
			return $sqlResult->result();
		}

		
		function getDett($store_id,$month,$gender,$sort,$sortBy,$city,$status,$promo,$between,$and,$year,$itemName){
			// echo  'asdasd';
			// die();
			$sql="SELECT CASE(sales_flat_order.store_id) 
			WHEN 21 THEN 'RiteMed' WHEN 20 THEN 'Family Health at Home' 
			WHEN 19 THEN 'Unilab Active Health' 
			WHEN 12 THEN 'Heymom' 
			WHEN 11 THEN 'Clickhealthplus' 
			WHEN 10 THEN 'Celeteque' 
			WHEN 6 THEN 'Evolife' 
			WHEN 1 THEN 'Clickhealth' END AS `Store Name`,
			sales_flat_order_item.order_id AS `Order Id`,
			sales_flat_order_item.sku AS `Item Code`,
			sales_flat_order_item.`name` AS `Item Name`,
			qty_ordered AS Quantity,
			base_original_price AS `Item Price`,		
			qty_ordered * base_original_price AS `Order Amount`,
			sales_flat_order.increment_id AS `Order Number`,
			sales_flat_order.coupon_rule_name AS `Promo Name`,			
			sales_flat_order.discount_amount AS `Discount Amount`,
			base_shipping_amount AS `Shipping Rate`,
			sales_flat_order.`status` as 'Payment Status', 								
			DATE_FORMAT(sales_flat_order_item.created_at,'%d-%M-%Y') AS `Order Date`, 
			DATE_FORMAT(sales_flat_order_item.created_at,'%M') AS `Month`,
			CONCAT(sales_flat_order.customer_firstname,' ',sales_flat_order.customer_lastname) AS `Shopper's Name`,	

			sales_flat_order_address.email AS Email,
			IF(sales_flat_order.customer_gender = 1, 'Male', 'Female') AS Gender,
			sales_flat_order_address.country_id AS Country,				
			sales_flat_order_address.street AS Address,
			sales_flat_order_address.city AS City,
			sales_flat_order_address.telephone AS Telephone,
			sales_flat_order_address.mobile AS Mobile,
			DATE_FORMAT(sales_flat_order_item.created_at,'%Y') AS `Year`		
			

			FROM sales_flat_order_item
			INNER JOIN sales_flat_order ON sales_flat_order_item.order_id = sales_flat_order.entity_id
			LEFT JOIN sales_flat_order_address ON sales_flat_order_address.entity_id = sales_flat_order.billing_address_id 
			
			WHERE  sales_flat_order.store_id in($store_id)";
			
			
			if($between){
				$getdBetween = $between;
				$sql.="AND sales_flat_order_item.created_at BETWEEN STR_TO_DATE('$getdBetween','%d-%M-%Y')";			
			}
			if($and){				
				$getdAnd =$and;
				$sql.="AND STR_TO_DATE('$getdAnd','%d-%M-%Y')";	

			}
			if($year){	
				$GtdYear = $year;	
				$sql.="AND YEAR(sales_flat_order_item.created_at) = '$GtdYear'";
			}
			
			if($month){
				$GtdMonth = $month;
				$sql.="AND MONTH(sales_flat_order_item.created_at) = '$GtdMonth'";	
			}
			
			if($gender){
				$GtdGender = $gender;
				$sql.="AND sales_flat_order.customer_gender = '$GtdGender'";
			}
			if($promo){
				$GtdPromo = $promo;
				$sql.="AND sales_flat_order.coupon_rule_name = '$GtdPromo' ";

			}
			if($status){
				$GtdStatus = $status;
				$sql.="AND  sales_flat_order.status = '$GtdStatus'";

			}
			if($city){
				$GtdCity = $city;
				$sql.="AND  sales_flat_order_address.city = '$GtdCity'";

			}
			
			// echo $itemName .'asdasdasd';
			// die();
			if($itemName){
				$GtdName = $itemName;
				$sql.="AND  sales_flat_order_item.name = '$GtdName'";

			}
			// echo $between . 'asdasd';
			// die();
			if($sort){
			$sort = $sort;
			$sortby = $sortBy;
			$sql.=" ORDER BY $sort $sortby";
				}
				else
				{
				$sql.=" ORDER BY DATE_FORMAT(sales_flat_order_item.created_at,'%d-%M-%Y') ASC";
				}			
			
			
			// echo '<pre>'.$sql;
			// die();


			$sqlResult = $this->db->query($sql);
			// print_r($sqlResult->result());
			// die();
			return $sqlResult->result();
			/**/
		}
		

		function getDefault(){
			$sql='
	SELECT 
	CASE(sales_flat_order.store_id) WHEN 21 THEN "RiteMed" WHEN 20 THEN "Family Health at Home" WHEN 19 THEN "Unilab Active Health" WHEN 12 THEN "Heymom" WHEN 11 THEN "Clickhealthplus" WHEN 10 THEN "Celeteque" WHEN 6 THEN "Evolife" WHEN 1 THEN "Clickhealth" END AS `Store Name`,
	sales_flat_order.entity_id AS `Order ID`,
	sales_flat_order.increment_id AS `Order Number`, 
	CONCAT("? ",FORMAT(grand_total,2)) AS `Order Total`,
	CONCAT("? ",FORMAT(base_subtotal,2)) AS `Order Amount`,
	CONCAT("? ",FORMAT(base_shipping_amount,2)) AS `Order Shipping`,
	sales_flat_order. coupon_rule_name AS `Promo Name`,
	sales_flat_order.discount_amount AS `Discount Amount`,
	sales_flat_order.`status` as "Payment Status",
	DATE_FORMAT(created_at,"%d-%M") AS `Order Date`, 
	DATE_FORMAT(updated_at,"%d-%M") AS `Moderation Date`,
	DATE_FORMAT(created_at,"%M") AS `Month`,
	sales_flat_order.unilab_waybill_number AS `Waybill Number`,
	CONCAT(customer_firstname," ",customer_lastname) AS `Shopper"s Name`, 
	IF(sales_flat_order.customer_gender = 1, "Male", "Female") AS Gender,
	sales_flat_order_address.country_id AS Country,
	sales_flat_order_address.street AS `Address`,
	sales_flat_order_address.city AS City,
	DATE_FORMAT(created_at,"%Y") AS `Year`
	FROM sales_flat_order INNER JOIN sales_flat_order_address ON 
	sales_flat_order.customer_email = sales_flat_order_address.email GROUP BY increment_id 
	ORDER BY created_at ASC';
	$sqlResult = $this->db->query($sql);
	return $sqlResult->result();
		}

		function getsummbyDate($from,$to){
		
			$sql='
	SELECT 
	CASE(sales_flat_order.store_id) WHEN 21 THEN "RiteMed" WHEN 20 THEN "Family Health at Home" WHEN 19 THEN "Unilab Active Health" WHEN 12 THEN "Heymom" WHEN 11 THEN "Clickhealthplus" WHEN 10 THEN "Celeteque" WHEN 6 THEN "Evolife" WHEN 1 THEN "Clickhealth" END AS `Store Name`,
	sales_flat_order.entity_id AS `Order ID`,
	sales_flat_order.increment_id AS `Order Number`, 
	CONCAT("? ",FORMAT(grand_total,2)) AS `Order Total`,
	CONCAT("? ",FORMAT(base_subtotal,2)) AS `Order Amount`,
	CONCAT("? ",FORMAT(base_shipping_amount,2)) AS `Order Shipping`,
	sales_flat_order. coupon_rule_name AS `Promo Name`,
	sales_flat_order.discount_amount AS `Discount Amount`,
	sales_flat_order.`status` as "Payment Status",
	DATE_FORMAT(created_at,"%d-%M") AS `Order Date`, 
	DATE_FORMAT(updated_at,"%d-%M") AS `Moderation Date`,
	DATE_FORMAT(created_at,"%M") AS `Month`,
	sales_flat_order.unilab_waybill_number AS `Waybill Number`,
	CONCAT(customer_firstname," ",customer_lastname) AS `Shopper"s Name`, 
	IF(sales_flat_order.customer_gender = 1, "Male", "Female") AS Gender,
	sales_flat_order_address.country_id AS Country,
	sales_flat_order_address.street AS `Address`,
	sales_flat_order_address.city AS City,
	DATE_FORMAT(created_at,"%Y") AS `Year`
	FROM sales_flat_order INNER JOIN sales_flat_order_address ON 
	sales_flat_order.customer_email = sales_flat_order_address.email
	where created_at BETWEEN "'.$from.'" AND "'.$to.'"
	GROUP BY increment_id 
	ORDER BY created_at ASC';
	$sqlResult = $this->db->query($sql);
	//echo $this->db->last_query();
	return $sqlResult->result();


		}
		function getDetbydate($from, $to){
	$sql ='
	SELECT
	CASE(sales_flat_order.store_id) 
	WHEN 21 THEN "RiteMed" WHEN 20 THEN "Family Health at Home" 
	WHEN 19 THEN "Unilab Active Health" 
	WHEN 12 THEN "Heymom" 
	WHEN 11 THEN "Clickhealthplus" 
	WHEN 10 THEN "Celeteque" 
	WHEN 6 THEN "Evolife" 
	WHEN 1 THEN "Clickhealth" END `Store Name`,
	sales_flat_order_item.order_id,
	sales_flat_order_item.sku AS `Item Code`,
	sales_flat_order_item.`name` AS `Item Name`,
	CONCAT(FORMAT(qty_ordered,2)) AS Quantity,
	CONCAT("? ",FORMAT(base_original_price,2)) AS `Item Price`,
	CONCAT("? ",FORMAT(qty_ordered * base_original_price ,2)) AS `Order Amount`,
	sales_flat_order.increment_id AS `Order Number`,
	sales_flat_order. coupon_rule_name AS `Promo Name`,
	sales_flat_order.discount_amount AS `Discount Amount`,
	CONCAT("? ",FORMAT(base_shipping_amount,2)) AS `Shipping Rate`,
	sales_flat_order.status as "Payment Status",
	DATE_FORMAT(sales_flat_order_item.created_at,"%m/%d/%Y") AS `Order Date`,
	DATE_FORMAT(sales_flat_order_item.created_at,"%M") AS `Month`,
	CONCAT(sales_flat_order.customer_firstname," ",sales_flat_order.customer_lastname) AS `Shopper"s Name`,
	sales_flat_order_address.email AS Email,
	IF(sales_flat_order.customer_gender = 1, "Male", "Female") AS Gender,
	sales_flat_order_address.country_id AS Country,
	sales_flat_order_address.street AS Address,
	sales_flat_order_address.city AS City,
	sales_flat_order_address.telephone AS Telephone,
	sales_flat_order_address.mobile AS Mobile,
	DATE_FORMAT(sales_flat_order_item.created_at,"%Y") AS `Year`
	FROM 
	sales_flat_order_item
	INNER JOIN sales_flat_order ON sales_flat_order_item.order_id = sales_flat_order.entity_id
	LEFT JOIN sales_flat_order_address ON sales_flat_order_address.entity_id = sales_flat_order.billing_address_id
	where created_at BETWEEN "'.$from.'" AND "'.$to.'"
	GROUP BY item_id
	ORDER BY order_id DESC
	';
		}
		


	}