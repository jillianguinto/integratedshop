<?php


echo "http://onlinestore.ecomqa.com/media/catalog/product/cache/12/small_image/252x/9df78eab33525d08d6e5fb8d27136e95/m/_/m_154391_1_medres_primary_1_1.jpg
																													/m/_/m_154391_1_medres_primary_1_1.jpg";

																													/m/_/m_154391_1_medres_primary_1_1.jpg

$img_url = "http://onlinestore.ecomqa.com/media/catalog/product/cache/12/small_image/252x/9df78eab33525d08d6e5fb8d27136e95/m/_/m_154391_1_medres_primary_1_1.jpg/m/_/m_154391_1_medres_primary_1_1.jpg";

echo '<pre>';

print_r(array_unique(explode('/', $img_url)));


print_r(parse_str($img_url));




echo $t = "http://onlinestore.ecomqa.com/media/catalog/product/cache/12/small_image/252x/9df78eab33525d08d6e5fb8d27136e95/b/l/blush_and_face_contour_kit2-pink1_1.png
																														 /b/l/blush_and_face_contour_kit2-pink1_1.png";



?>