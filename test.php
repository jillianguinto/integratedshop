<?php 
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();


// echo get_remote_data($_SERVER['HTTP_REFERER']); 


echo $url = $_SERVER['HTTP_REFERER'];

echo '<pre>';

print_r($_COOKIE);
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);    // The url to get links from
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
    // $result = curl_exec($ch);

    // $regex='#.*link rel=\"alternate\" href=\"(.*)\"./>#';
    // preg_match($regex,$result,$parts);

    // foreach ($parts as $part) {
    //    echo $part;
    // }




// echo '<pre>';

// print_r(headers_list()); 
?>

<script type="text/javascript">


   // alert(window.location.pathname);


</script>

<?php 




// function get_remote_data($url, $post_paramtrs = false) {
//     $c = curl_init();
//     curl_setopt($c, CURLOPT_URL, $url);
//     curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
//     if ($post_paramtrs) {
//         curl_setopt($c, CURLOPT_POST, TRUE);
//         curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&" . $post_paramtrs);
//     } curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
//     curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");
//     curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
//     curl_setopt($c, CURLOPT_MAXREDIRS, 10);
//     $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
//     if ($follow_allowed) {
//         curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
//     }curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
//     curl_setopt($c, CURLOPT_REFERER, $url);
//     curl_setopt($c, CURLOPT_TIMEOUT, 60);
//     curl_setopt($c, CURLOPT_AUTOREFERER, true);
//     curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
//     $data = curl_exec($c);
//     $status = curl_getinfo($c);
//     curl_close($c);
//     preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si', $status['url'], $link);
//     $data = preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si', '$1=$2' . $link[0] . '$3$4$5', $data);
//     $data = preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si', '$1=$2' . $link[1] . '://' . $link[3] . '$3$4$5', $data);
//     if ($status['http_code'] == 200) {
//         return $data;
//     } elseif ($status['http_code'] == 301 || $status['http_code'] == 302) {
//         if (!$follow_allowed) {
//             if (empty($redirURL)) {
//                 if (!empty($status['redirect_url'])) {
//                     $redirURL = $status['redirect_url'];
//                 }
//             } if (empty($redirURL)) {
//                 preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m);
//                 if (!empty($m[2])) {
//                     $redirURL = $m[2];
//                 }
//             } if (empty($redirURL)) {
//                 preg_match('/href\=\"(.*?)\"(.*?)here\<\/a\>/si', $data, $m);
//                 if (!empty($m[1])) {
//                     $redirURL = $m[1];
//                 }
//             } if (!empty($redirURL)) {
//                 $t = debug_backtrace();
//                 return call_user_func($t[0]["function"], trim($redirURL), $post_paramtrs);
//             }
//         }
//     } return "ERRORCODE22 with $url!!<br/>Last status codes<b/>:" . json_encode($status) . "<br/><br/>Last data got<br/>:$data";
// }


// $url = $_SERVER['HTTP_REFERER'];

// print_r(get_headers($url));

// print_r(get_headers($url, 1));


// $categories = Mage::getModel('catalog/category')
//         ->getCollection()
//         ->addAttributeToSelect('*')
//         ->addIsActiveFilter();



//         echo '<pre>';

//         print_r($categories->getData());



die();

echo 'HTTP_REFERER: ' .$_SERVER['HTTP_REFERER'];

// echo '<br/>';
// echo Mage::getSingleton('core/session')->getLastUrl();	


die();

// Mage::app()->getStore()->getRootCategoryId();
$rootcatId= 190;
$categories = Mage::getModel('catalog/category')->getCategories($rootcatId);	

// This is the recursive function created and here we pass the a collection of categories.
function  get_categories($categories) { 
// $array is a variable to store all the category detail . 

	?>	



		<?php 
		
		$array= '<ul class="categories">';  
		foreach($categories as $category) 
		{
			$cat = Mage::getModel('catalog/category')->load($category->getId());
			//$count the total no of products in the category
			$count = $cat->getProductCount(); 
			//In this line we get an a link for the product and product count of that category
			$array .= '<li>'.'<a data-href="' . Mage::getUrl($cat->getUrlPath()). '">' . $category->getName() . "</a>\n"; 
			//if category has children or not. If yes then it proceed in inside loop.
			if($category->hasChildren()) 
			{ 

				// $children get a list of all subcategories
				$children = Mage::getModel('catalog/category')->getCategories($category-> getId()); 
				//recursive call the get_categories function again.
				$array .=  get_categories($children);
			}
			$array .= '</li>';
		}

		return $array .='</ul>';
		?>
	
	
	<?php 			
}

 echo get_categories($categories);
?>

<?php //echo get_categories($categories); //echo all categories in the website, with number of products ?>


	<!-- <div class="faq_accordian">	

		<div class="arrowlistmenu">

			<ul class="categories">
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine.html/">Medicines and Therapeutics</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/allergy.html/">Allergy</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/arthritic-pain.html/">Arthritic Pain</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/body-and-muscle-pain.html/">Body and Muscle Pain</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/cough-and-colds.html/">Cough and Colds</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/gut-and-stomach-health.html/">Gut and Stomach Health</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/headache-fever-and-flu.html/">Headache, Fever and Flu</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/medicine/therapeutics.html/">Therapeutics</a>
						</li>
					</ul>
				</li>
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-nutrition.html/">Vitamins and Nutrition</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-nutrition/weight-management-sports.html/">Weight Management & Sports</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-nutrition/men-s-health.html/">Men's Health</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-nutrition/women-s-health.html/">Women's Health</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-nutrition/senior-s-health.html/">Senior's Health</a>
						</li>
					</ul>
				</li>
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/personal-care.html/">Personal Care</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/personal-care/skincare.html/">Skincare</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/personal-care/intimate-care.html/">Intimate Care</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/personal-care/oral-care.html/">Oral Care</a>
						</li>
					</ul>
				</li>
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/children-s-health.html/">Children's Health</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/children-s-health/children-s-vitamins.html/">Children's Vitamins</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/children-s-health/children-s-medicine.html/">Children's Medicines</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/children-s-health/children-s-skincare.html/">Children's Skincare</a>
						</li>
					</ul>
				</li>
				<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/pregnancy-and-motherhood.html/">Pregnancy and Motherhood</a>
				</li>
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/sports-solutions.html/">Sports Solutions</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/sports-solutions/nutrition.html/">Nutrition</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/sports-solutions/gear.html/">Gear</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/sports-solutions/services.html/">Services</a>
						</li>
					</ul>
				</li>
				<li>
					<a data-href="http://onlinestore.ecomqa.com/test.php/default-category/senior-s-health.html/">Senior's Health</a>
					<ul class="categories">
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/senior-s-health/nutrition.html/">Nutrition</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/senior-s-health/pain-management.html/">Pain Management</a>
						</li>
						<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/senior-s-health/nerve-health.html/">Nerve Health</a>
						</li>
					</ul>
				</li>
				<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/products-for-babies.html/">Products for Babies</a>
				</li>
				<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/beauty-and-personal-hydration.html/">Beauty and Personal Hydration</a>
				</li>
				<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/products-for-pregnant-moms.html/">Products for Pregnant Moms</a>
				</li>
				<li><a data-href="http://onlinestore.ecomqa.com/test.php/default-category/vitamins-and-supplements.html/">Vitamins and Supplements</a>
				</li>
			</ul>	

		</div>
	</div> --> 
