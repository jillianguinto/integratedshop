<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

INSERT INTO `{$installer->getTable('directory_country_region')}` VALUES
(485, 'PH', 'AB', 'Abra'),
(486, 'PH', 'AN', 'Agusan del Norte'),
(487, 'PH', 'AS', 'Agusan del Sur'),
(488, 'PH', 'AK', 'Aklan'),
(489, 'PH', 'AL', 'Albay'),
(490, 'PH', 'AQ', 'Antique'),
(491, 'PH', 'AP', 'Apayao'),
(492, 'PH', 'AU', 'Aurora'),
(493, 'PH', 'BL', 'Basilan'),
(494, 'PH', 'BA', 'Bataan'),
(495, 'PH', 'BN', 'Batanes'),
(496, 'PH', 'BT', 'Batangas'),
(497, 'PH', 'BG', 'Benguet'),
(498, 'PH', 'BI', 'Biliran'),
(499, 'PH', 'BH', 'Bohol'),
(500, 'PH', 'BK', 'Bukidnon'),
(501, 'PH', 'BU', 'Bulacan'),
(502, 'PH', 'CG', 'Cagayan'),
(503, 'PH', 'CN', 'Camarines Norte'),
(504, 'PH', 'CS', 'Camarines Sur'),
(505, 'PH', 'CM', 'Camiguin'),
(506, 'PH', 'CP', 'Capiz'),
(507, 'PH', 'CT', 'Catanduanes'),
(508, 'PH', 'CA', 'Cavite'),
(509, 'PH', 'CB', 'Cebu'),
(510, 'PH', 'CV', 'Compostela Valley'),
(511, 'PH', 'CO', 'Cotabato'),
(512, 'PH', 'DN', 'Davao del Norte'),
(513, 'PH', 'DS', 'Davao del Sur'),
(514, 'PH', 'DR', 'Davao Oriental'),
(515, 'PH', 'DI', 'Dinagat Islands'),
(516, 'PH', 'ES', 'Eastern Samar'),
(517, 'PH', 'GU', 'Guimaras'),
(518, 'PH', 'IF', 'Ifugao'),
(519, 'PH', 'IN', 'Ilocos Norte'),
(520, 'PH', 'IS', 'Ilocos Sur'),
(521, 'PH', 'IL', 'Iloilo'),
(522, 'PH', 'IB', 'Isabela'),
(523, 'PH', 'KA', 'Kalinga'),
(524, 'PH', 'LU', 'La Union'),
(525, 'PH', 'LA', 'Laguna'),
(526, 'PH', 'LN', 'Lanao del Norte'),
(527, 'PH', 'LS', 'Lanao del Sur'),
(528, 'PH', 'LE', 'Leyte'),
(529, 'PH', 'MG', 'Maguindanao'),
(530, 'PH', 'MQ', 'Marinduque'),
(531, 'PH', 'MA', 'Masbate'),
(532, 'PH', 'MM', 'Metro Manila'),
(533, 'PH', 'MO', 'Misamis Occidental'),
(534, 'PH', 'MR', 'Misamis Oriental'),
(535, 'PH', 'MT', 'Mountain Province'),
(536, 'PH', 'NO', 'Negros Occidental'),
(537, 'PH', 'NR', 'Negros Oriental'),
(538, 'PH', 'NS', 'Northern Samar'),
(539, 'PH', 'NE', 'Nueva Ecija'),
(540, 'PH', 'NV', 'Nueva Vizcaya'),
(541, 'PH', 'OM', 'Occidental Mindoro'),
(542, 'PH', 'RM', 'Oriental Mindoro'),
(543, 'PH', 'PL', 'Palawan'),
(544, 'PH', 'PA', 'Pampanga'),
(545, 'PH', 'PG', 'Pangasinan'),
(546, 'PH', 'QZ', 'Quezon'),
(547, 'PH', 'QR', 'Quirino'),
(548, 'PH', 'RZ', 'Rizal'),
(549, 'PH', 'RO', 'Romblon'),
(550, 'PH', 'ES', 'Samar'),
(551, 'PH', 'SR', 'Sarangani'),
(552, 'PH', 'SQ', 'Siquijor'),
(553, 'PH', 'SO', 'Sorsogon'),
(554, 'PH', 'SC', 'South Cotabato'),
(555, 'PH', 'SL', 'Southern Leyte'),
(556, 'PH', 'SK', 'Sultan Kudarat'),
(557, 'PH', 'SU', 'Sulu'),
(558, 'PH', 'SN', 'Surigao Del Norte'),
(559, 'PH', 'SS', 'Surigao Del Sur'),
(560, 'PH', 'TA', 'Tarlac'),
(561, 'PH', 'TW', 'Tawi-Tawi'),
(562, 'PH', 'ZA', 'Zambales'),
(563, 'PH', 'ZN', 'Zamboanga del Norte'),
(564, 'PH', 'ZS', 'Zamboanga del Sur'),
(565, 'PH', 'ZY', 'Zamboanga Sibugay');


INSERT INTO `{$installer->getTable('directory_country_region_name')}` VALUES 
('en_US',485,'Abra'),
('en_US',486,'Agusan del Norte'),
('en_US',487,'Agusan del Sur'),
('en_US',488,'Aklan'),
('en_US',489,'Albay'),
('en_US',490,'Antique'),
('en_US',491,'Apayao'),
('en_US',492,'Aurora'),
('en_US',493,'Basilan'),
('en_US',494,'Bataan'),
('en_US',495,'Batanes'),
('en_US',496,'Batangas'),
('en_US',497,'Benguet'),
('en_US',498,'Biliran'),
('en_US',499,'Bohol'),
('en_US',500,'Bukidnon'),
('en_US',501,'Bulacan'),
('en_US',502,'Cagayan'),
('en_US',503,'Camarines Norte'),
('en_US',504,'Camarines Sur'),
('en_US',505,'Camiguin'),
('en_US',506,'Capiz'),
('en_US',507,'Catanduanes'),
('en_US',508,'Cavite'),
('en_US',509,'Cebu'),
('en_US',510,'Compostela Valley'),
('en_US',511,'Cotabato'),
('en_US',512,'Davao del Norte'),
('en_US',513,'Davao del Sur'),
('en_US',514,'Davao Oriental'),
('en_US',515,'Dinagat Islands'),
('en_US',516,'Eastern Samar'),
('en_US',517,'Guimaras'),
('en_US',518,'Ifugao'),
('en_US',519,'Ilocos Norte'),
('en_US',520,'Ilocos Sur'),
('en_US',521,'Iloilo'),
('en_US',522,'Isabela'),
('en_US',523,'Kalinga'),
('en_US',524,'La Union'),
('en_US',525,'Laguna'),
('en_US',526,'Lanao del Norte'),
('en_US',527,'Lanao del Sur'),
('en_US',528,'Leyte'),
('en_US',529,'Maguindanao'),
('en_US',530,'Marinduque'),
('en_US',531,'Masbate'),
('en_US',532,'Metro Manila'),
('en_US',533,'Misamis Occidental'),
('en_US',534,'Misamis Oriental'),
('en_US',535,'Mountain Province'),
('en_US',536,'Negros Occidental'),
('en_US',537,'Negros Oriental'),
('en_US',538,'Northern Samar'),
('en_US',539,'Nueva Ecija'),
('en_US',540,'Nueva Vizcaya'),
('en_US',541,'Occidental Mindoro'),
('en_US',542,'Oriental Mindoro'),
('en_US',543,'Palawan'),
('en_US',544,'Pampanga'),
('en_US',545,'Pangasinan'),
('en_US',546,'Quezon'),
('en_US',547,'Quirino'),
('en_US',548,'Rizal'),
('en_US',549,'Romblon'),
('en_US',550,'Samar'),
('en_US',551,'Sarangani'),
('en_US',552,'Siquijor'),
('en_US',553,'Sorsogon'),
('en_US',554,'South Cotabato'),
('en_US',555,'Southern Leyte'),
('en_US',556,'Sultan Kudarat'),
('en_US',557,'Sulu'),
('en_US',558,'Surigao Del Norte'),
('en_US',559,'Surigao Del Sur'),
('en_US',560,'Tarlac'),
('en_US',561,'Tawi-Tawi'),
('en_US',562,'Zambales'),
('en_US',563,'Zamboanga del Norte'),
('en_US',564,'Zamboanga del Sur'),
('en_US',565,'Zamboanga Sibugay'); 

");
$installer->endSetup();