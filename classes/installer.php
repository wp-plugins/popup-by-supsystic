<?php
class installerPps {
	static public $update_to_version_method = '';
	static private $_firstTimeActivated = false;
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. PPS_DB_PREF. 'db_version', 0);
		if(!$current_version)
			self::$_firstTimeActivated = true;
		/**
		 * modules 
		 */
		if (!dbPps::exist("@__modules")) {
			dbDelta(dbPps::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `code` varchar(32) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(64) DEFAULT NULL,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;"));
			dbPps::query("INSERT INTO `@__modules` (id, code, active, type_id, label) VALUES
				(NULL, 'adminmenu',1,1,'Admin Menu'),
				(NULL, 'options',1,1,'Options'),
				(NULL, 'user',1,1,'Users'),
				(NULL, 'pages',1,1,'Pages'),
				(NULL, 'templates',1,1,'templates'),
				(NULL, 'supsystic_promo',1,1,'supsystic_promo'),
				(NULL, 'admin_nav',1,1,'admin_nav'),
				
				(NULL, 'popup',1,1,'popup'),
				(NULL, 'subscribe',1,1,'subscribe'),
				(NULL, 'sm',1,1,'sm'),
				(NULL, 'statistics',1,1,'statistics'),
				
				(NULL, 'mail',1,1,'mail');");
		}
		/**
		 *  modules_type 
		 */
		if(!dbPps::exist("@__modules_type")) {
			dbDelta(dbPps::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules_type` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"));
			dbPps::query("INSERT INTO `@__modules_type` VALUES
				(1,'system'),
				(6,'addons');");
		}
		/**
		 * Popup table
		 */
		if (!dbPps::exist("@__popup")) {
			dbDelta(dbPps::prepareQuery("CREATE TABLE IF NOT EXISTS `@__popup` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`label` VARCHAR(255) NOT NULL,
				`active` TINYINT(1) NOT NULL,
				`original_id` INT(11) NOT NULL DEFAULT '0',
				`params` TEXT NOT NULL,
				`html` TEXT NOT NULL,
				`css` TEXT NOT NULL,
				`img_preview` VARCHAR(128) NULL DEFAULT NULL,
				`show_to` TINYINT(1) NOT NULL DEFAULT '0',
				`show_pages` TINYINT(1) NOT NULL DEFAULT '0',
				`type_id` TINYINT(1) NOT NULL DEFAULT '1',
				`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;"));
			self::initBasePopups();
		}
		if (!dbPps::exist("@__popup_show_pages")) {
			dbDelta(dbPps::prepareQuery("CREATE TABLE `@__popup_show_pages` (
				`popup_id` INT(10) NOT NULL,
				`post_id` INT(10) NOT NULL,
				`not_show` TINYINT(1) NOT NULL DEFAULT '0'
			) DEFAULT CHARSET=utf8;"));
		}
		/**
		* Plugin usage statistics
		*/
		if(!dbPps::exist("@__usage_stat")) {
			dbDelta(dbPps::prepareQuery("CREATE TABLE `@__usage_stat` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT '0',
			  `spent_time` int(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8"));
			dbPps::query("INSERT INTO `@__usage_stat` (code, visits) VALUES ('installed', 1)");
		}
		/**
		 * Statistics
		 */
		if (!dbPps::exist("@__statistics")) {
			  dbDelta(dbPps::prepareQuery("CREATE TABLE `@__statistics` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`popup_id` int(11) NOT NULL DEFAULT '0',
				`type` TINYINT(2) NOT NULL DEFAULT '0',
				`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8;"));
		}
		/**
		 * Subscribers
		 */
		if (!dbPps::exist("@__subscribers")) {
			  dbDelta(dbPps::prepareQuery("CREATE TABLE `@__subscribers` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`username` VARCHAR(128) NULL DEFAULT NULL,
				`email` VARCHAR(128) NOT NULL,
				`hash` VARCHAR(128) NOT NULL,
				`activated` TINYINT(1) NOT NULL DEFAULT '0',
				`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8;"));
		}
		installerDbUpdaterPps::runUpdate();
		if($current_version && !self::$_firstTimeActivated) {
			self::setUsed();
		}
		update_option($wpPrefix. PPS_DB_PREF. 'db_version', PPS_VERSION);
		add_option($wpPrefix. PPS_DB_PREF. 'db_installed', 1);
	}
	static public function setUsed() {
		update_option(PPS_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		// No welcome page for now
		return true;
		return (int) get_option(PPS_DB_PREF. 'plug_was_used');
	}
	static public function delete() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."modules`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."modules_type`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."usage_stat`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."popup`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."popup_show_pages`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."statistics`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.PPS_DB_PREF."subscribers`");
		delete_option($wpPrefix. PPS_DB_PREF. 'db_version');
		delete_option($wpPrefix. PPS_DB_PREF. 'db_installed');
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. PPS_DB_PREF. 'db_version', 0);
		if(!$currentVersion || version_compare(PPS_VERSION, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. PPS_DB_PREF. 'db_version', PPS_VERSION);
		}
	}
	private function _insertCountries() {
		dbPps::query('INSERT INTO @__countries VALUES 
			(1, "Afghanistan", "AF", "AFG"),
			(2, "Albania", "AL", "ALB"),
			(3, "Algeria", "DZ", "DZA"),
			(4, "American Samoa", "AS", "ASM"),
			(5, "Andorra", "AD", "AND"),
			(6, "Angola", "AO", "AGO"),
			(7, "Anguilla", "AI", "AIA"),
			(8, "Antarctica", "AQ", "ATA"),
			(9, "Antigua and Barbuda", "AG", "ATG"),
			(10, "Argentina", "AR", "ARG"),
			(11, "Armenia", "AM", "ARM"),
			(12, "Aruba", "AW", "ABW"),
			(13, "Australia", "AU", "AUS"),
			(14, "Austria", "AT", "AUT"),
			(15, "Azerbaijan", "AZ", "AZE"),
			(16, "Bahamas", "BS", "BHS"),
			(17, "Bahrain", "BH", "BHR"),
			(18, "Bangladesh", "BD", "BGD"),
			(19, "Barbados", "BB", "BRB"),
			(20, "Belarus", "BY", "BLR"),
			(21, "Belgium", "BE", "BEL"),
			(22, "Belize", "BZ", "BLZ"),
			(23, "Benin", "BJ", "BEN"),
			(24, "Bermuda", "BM", "BMU"),
			(25, "Bhutan", "BT", "BTN"),
			(26, "Bolivia", "BO", "BOL"),
			(27, "Bosnia and Herzegowina", "BA", "BIH"),
			(28, "Botswana", "BW", "BWA"),
			(29, "Bouvet Island", "BV", "BVT"),
			(30, "Brazil", "BR", "BRA"),
			(31, "British Indian Ocean Territory", "IO", "IOT"),
			(32, "Brunei Darussalam", "BN", "BRN"),
			(33, "Bulgaria", "BG", "BGR"),
			(34, "Burkina Faso", "BF", "BFA"),
			(35, "Burundi", "BI", "BDI"),
			(36, "Cambodia", "KH", "KHM"),
			(37, "Cameroon", "CM", "CMR"),
			(38, "Canada", "CA", "CAN"),
			(39, "Cape Verde", "CV", "CPV"),
			(40, "Cayman Islands", "KY", "CYM"),
			(41, "Central African Republic", "CF", "CAF"),
			(42, "Chad", "TD", "TCD"),
			(43, "Chile", "CL", "CHL"),
			(44, "China", "CN", "CHN"),
			(45, "Christmas Island", "CX", "CXR"),
			(46, "Cocos (Keeling) Islands", "CC", "CCK"),
			(47, "Colombia", "CO", "COL"),
			(48, "Comoros", "KM", "COM"),
			(49, "Congo", "CG", "COG"),
			(50, "Cook Islands", "CK", "COK"),
			(51, "Costa Rica", "CR", "CRI"),
			(52, "Cote D\'Ivoire", "CI", "CIV"),
			(53, "Croatia", "HR", "HRV"),
			(54, "Cuba", "CU", "CUB"),
			(55, "Cyprus", "CY", "CYP"),
			(56, "Czech Republic", "CZ", "CZE"),
			(57, "Denmark", "DK", "DNK"),
			(58, "Djibouti", "DJ", "DJI"),
			(59, "Dominica", "DM", "DMA"),
			(60, "Dominican Republic", "DO", "DOM"),
			(61, "East Timor", "TP", "TMP"),
			(62, "Ecuador", "EC", "ECU"),
			(63, "Egypt", "EG", "EGY"),
			(64, "El Salvador", "SV", "SLV"),
			(65, "Equatorial Guinea", "GQ", "GNQ"),
			(66, "Eritrea", "ER", "ERI"),
			(67, "Estonia", "EE", "EST"),
			(68, "Ethiopia", "ET", "ETH"),
			(69, "Falkland Islands (Malvinas)", "FK", "FLK"),
			(70, "Faroe Islands", "FO", "FRO"),
			(71, "Fiji", "FJ", "FJI"),
			(72, "Finland", "FI", "FIN"),
			(73, "France", "FR", "FRA"),
			(74, "France, Metropolitan", "FX", "FXX"),
			(75, "French Guiana", "GF", "GUF"),
			(76, "French Polynesia", "PF", "PYF"),
			(77, "French Southern Territories", "TF", "ATF"),
			(78, "Gabon", "GA", "GAB"),
			(79, "Gambia", "GM", "GMB"),
			(80, "Georgia", "GE", "GEO"),
			(81, "Germany", "DE", "DEU"),
			(82, "Ghana", "GH", "GHA"),
			(83, "Gibraltar", "GI", "GIB"),
			(84, "Greece", "GR", "GRC"),
			(85, "Greenland", "GL", "GRL"),
			(86, "Grenada", "GD", "GRD"),
			(87, "Guadeloupe", "GP", "GLP"),
			(88, "Guam", "GU", "GUM"),
			(89, "Guatemala", "GT", "GTM"),
			(90, "Guinea", "GN", "GIN"),
			(91, "Guinea-bissau", "GW", "GNB"),
			(92, "Guyana", "GY", "GUY"),
			(93, "Haiti", "HT", "HTI"),
			(94, "Heard and Mc Donald Islands", "HM", "HMD"),
			(95, "Honduras", "HN", "HND"),
			(96, "Hong Kong", "HK", "HKG"),
			(97, "Hungary", "HU", "HUN"),
			(98, "Iceland", "IS", "ISL"),
			(99, "India", "IN", "IND"),
			(100, "Indonesia", "ID", "IDN"),
			(101, "Iran (Islamic Republic of)", "IR", "IRN"),
			(102, "Iraq", "IQ", "IRQ"),
			(103, "Ireland", "IE", "IRL"),
			(104, "Israel", "IL", "ISR"),
			(105, "Italy", "IT", "ITA"),
			(106, "Jamaica", "JM", "JAM"),
			(107, "Japan", "JP", "JPN"),
			(108, "Jordan", "JO", "JOR"),
			(109, "Kazakhstan", "KZ", "KAZ"),
			(110, "Kenya", "KE", "KEN"),
			(111, "Kiribati", "KI", "KIR"),
			(112, "Korea, Democratic People\'s Republic of", "KP", "PRK"),
			(113, "Korea, Republic of", "KR", "KOR"),
			(114, "Kuwait", "KW", "KWT"),
			(115, "Kyrgyzstan", "KG", "KGZ"),
			(116, "Lao People\'s Democratic Republic", "LA", "LAO"),
			(117, "Latvia", "LV", "LVA"),
			(118, "Lebanon", "LB", "LBN"),
			(119, "Lesotho", "LS", "LSO"),
			(120, "Liberia", "LR", "LBR"),
			(121, "Libyan Arab Jamahiriya", "LY", "LBY"),
			(122, "Liechtenstein", "LI", "LIE"),
			(123, "Lithuania", "LT", "LTU"),
			(124, "Luxembourg", "LU", "LUX"),
			(125, "Macau", "MO", "MAC"),
			(126, "Macedonia, The Former Yugoslav Republic of", "MK", "MKD"),
			(127, "Madagascar", "MG", "MDG"),
			(128, "Malawi", "MW", "MWI"),
			(129, "Malaysia", "MY", "MYS"),
			(130, "Maldives", "MV", "MDV"),
			(131, "Mali", "ML", "MLI"),
			(132, "Malta", "MT", "MLT"),
			(133, "Marshall Islands", "MH", "MHL"),
			(134, "Martinique", "MQ", "MTQ"),
			(135, "Mauritania", "MR", "MRT"),
			(136, "Mauritius", "MU", "MUS"),
			(137, "Mayotte", "YT", "MYT"),
			(138, "Mexico", "MX", "MEX"),
			(139, "Micronesia, Federated States of", "FM", "FSM"),
			(140, "Moldova, Republic of", "MD", "MDA"),
			(141, "Monaco", "MC", "MCO"),
			(142, "Mongolia", "MN", "MNG"),
			(143, "Montenegro", "ME", "MNE"),
			(144, "Montserrat", "MS", "MSR"),
			(145, "Morocco", "MA", "MAR"),
			(146, "Mozambique", "MZ", "MOZ"),
			(147, "Myanmar", "MM", "MMR"),
			(148, "Namibia", "NA", "NAM"),
			(149, "Nauru", "NR", "NRU"),
			(150, "Nepal", "NP", "NPL"),
			(151, "Netherlands", "NL", "NLD"),
			(152, "Netherlands Antilles", "AN", "ANT"),
			(153, "New Caledonia", "NC", "NCL"),
			(154, "New Zealand", "NZ", "NZL"),
			(155, "Nicaragua", "NI", "NIC"),
			(156, "Niger", "NE", "NER"),
			(157, "Nigeria", "NG", "NGA"),
			(158, "Niue", "NU", "NIU"),
			(159, "Norfolk Island", "NF", "NFK"),
			(160, "Northern Mariana Islands", "MP", "MNP"),
			(161, "Norway", "NO", "NOR"),
			(162, "Oman", "OM", "OMN"),
			(163, "Pakistan", "PK", "PAK"),
			(164, "Palau", "PW", "PLW"),
			(165, "Panama", "PA", "PAN"),
			(166, "Papua New Guinea", "PG", "PNG"),
			(167, "Paraguay", "PY", "PRY"),
			(168, "Peru", "PE", "PER"),
			(169, "Philippines", "PH", "PHL"),
			(170, "Pitcairn", "PN", "PCN"),
			(171, "Poland", "PL", "POL"),
			(172, "Portugal", "PT", "PRT"),
			(173, "Puerto Rico", "PR", "PRI"),
			(174, "Qatar", "QA", "QAT"),
			(175, "Reunion", "RE", "REU"),
			(176, "Romania", "RO", "ROM"),
			(177, "Russian Federation", "RU", "RUS"),
			(178, "Rwanda", "RW", "RWA"),
			(179, "Saint Kitts and Nevis", "KN", "KNA"),
			(180, "Saint Lucia", "LC", "LCA"),
			(181, "Saint Vincent and the Grenadines", "VC", "VCT"),
			(182, "Samoa", "WS", "WSM"),
			(183, "San Marino", "SM", "SMR"),
			(184, "Sao Tome and Principe", "ST", "STP"),
			(185, "Saudi Arabia", "SA", "SAU"),
			(186, "Senegal", "SN", "SEN"),
			(187, "Serbia", "RS", "SRB"),
			(188, "Seychelles", "SC", "SYC"),
			(189, "Sierra Leone", "SL", "SLE"),
			(190, "Singapore", "SG", "SGP"),
			(191, "Slovakia (Slovak Republic)", "SK", "SVK"),
			(192, "Slovenia", "SI", "SVN"),
			(193, "Solomon Islands", "SB", "SLB"),
			(194, "Somalia", "SO", "SOM"),
			(195, "South Africa", "ZA", "ZAF"),
			(196, "South Georgia and the South Sandwich Islands", "GS", "SGS"),
			(197, "Spain", "ES", "ESP"),
			(198, "Sri Lanka", "LK", "LKA"),
			(199, "St. Helena", "SH", "SHN"),
			(200, "St. Pierre and Miquelon", "PM", "SPM"),
			(201, "Sudan", "SD", "SDN"),
			(202, "Suriname", "SR", "SUR"),
			(203, "Svalbard and Jan Mayen Islands", "SJ", "SJM"),
			(204, "Swaziland", "SZ", "SWZ"),
			(205, "Sweden", "SE", "SWE"),
			(206, "Switzerland", "CH", "CHE"),
			(207, "Syrian Arab Republic", "SY", "SYR"),
			(208, "Taiwan", "TW", "TWN"),
			(209, "Tajikistan", "TJ", "TJK"),
			(210, "Tanzania, United Republic of", "TZ", "TZA"),
			(211, "Thailand", "TH", "THA"),
			(212, "Togo", "TG", "TGO"),
			(213, "Tokelau", "TK", "TKL"),
			(214, "Tonga", "TO", "TON"),
			(215, "Trinidad and Tobago", "TT", "TTO"),
			(216, "Tunisia", "TN", "TUN"),
			(217, "Turkey", "TR", "TUR"),
			(218, "Turkmenistan", "TM", "TKM"),
			(219, "Turks and Caicos Islands", "TC", "TCA"),
			(220, "Tuvalu", "TV", "TUV"),
			(221, "Uganda", "UG", "UGA"),
			(222, "Ukraine", "UA", "UKR"),
			(223, "United Arab Emirates", "AE", "ARE"),
			(224, "United Kingdom", "GB", "GBR"),
			(225, "United States", "US", "USA"),
			(226, "United States Minor Outlying Islands", "UM", "UMI"),
			(227, "Uruguay", "UY", "URY"),
			(228, "Uzbekistan", "UZ", "UZB"),
			(229, "Vanuatu", "VU", "VUT"),
			(230, "Vatican City State (Holy See)", "VA", "VAT"),
			(231, "Venezuela", "VE", "VEN"),
			(232, "Viet Nam", "VN", "VNM"),
			(233, "Virgin Islands (British)", "VG", "VGB"),
			(234, "Virgin Islands (U.S.)", "VI", "VIR"),
			(235, "Wallis and Futuna Islands", "WF", "WLF"),
			(236, "Western Sahara", "EH", "ESH"),
			(237, "Yemen", "YE", "YEM"),
			(238, "Zaire", "ZR", "ZAR"),
			(239, "Zambia", "ZM", "ZMB"),
			(240, "Zimbabwe", "ZW", "ZWE")');
	}
	public function initBasePopups() {
		dbPps::query('INSERT INTO @__popup (id,label,active,original_id,params,html,css,img_preview,show_to,show_pages,type_id,date_created) VALUES 
			("1","List Building","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6MzM6e3M6OToiZW5iX2xhYmVsIjtzOjE6IjEiO3M6NToibGFiZWwiO3M6MTQwOiJUaGUgQmVzdCBXb3JkUHJlc3MgPGkgc3R5bGU9XCJjb2xvcjogIzAwNjlhNztcIj5Qb3BVcCBvcHRpbiBwbHVnaW48L2k+IHRvIGhlbHAgeW91IGdhaW4gbW9yZSBzdWJzY3JpYmVycywgc29jaWFsIGZvbGxvd2VycyBvciBhZHZlcnRpc2VtZW50LiI7czo1OiJ3aWR0aCI7czozOiI4MjQiO3M6MTM6IndpZHRoX21lYXN1cmUiO3M6MjoicHgiO3M6MTg6ImJnX292ZXJsYXlfb3BhY2l0eSI7czozOiIwLjUiO3M6OToiYmdfdHlwZV8wIjtzOjU6ImNvbG9yIjtzOjg6ImJnX2ltZ18wIjtzOjA6IiI7czoxMDoiYmdfY29sb3JfMCI7czo3OiIjZWJlYmViIjtzOjk6ImJnX3R5cGVfMSI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMSI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzEiO3M6NzoiIzA2NmRhYiI7czo5OiJiZ190eXBlXzIiO3M6NToiY29sb3IiO3M6ODoiYmdfaW1nXzIiO3M6MDoiIjtzOjEwOiJiZ19jb2xvcl8yIjtzOjc6IiMwMGVhZWEiO3M6OToiY2xvc2VfYnRuIjtzOjExOiJsaXN0c19ibGFjayI7czo3OiJidWxsZXRzIjtzOjExOiJsaXN0c19ncmVlbiI7czo5OiJlbmJfdHh0XzAiO3M6MToiMSI7czo5OiJlbmJfdHh0XzEiO3M6MToiMSI7czoxMzoiZW5iX2Zvb3Rfbm90ZSI7czoxOiIxIjtzOjk6ImZvb3Rfbm90ZSI7czoxMTg6IldlIHJlc3BlY3QgeW91ciBwcml2YWN5LiBZb3VyIGluZm9ybWF0aW9uIHdpbGwgbm90IGJlIHNoYXJlZCB3aXRoIGFueSB0aGlyZCBwYXJ0eSBhbmQgeW91IGNhbiB1bnN1YnNjcmliZSBhdCBhbnkgdGltZSAiO3M6MTM6ImVuYl9zdWJzY3JpYmUiO3M6MToiMSI7czo4OiJzdWJfZGVzdCI7czo5OiJ3b3JkcHJlc3MiO3M6MTk6InN1Yl9hd2ViZXJfbGlzdG5hbWUiO3M6MDoiIjtzOjEyOiJlbmJfc3ViX25hbWUiO3M6MToiMSI7czoxMzoic3ViX2J0bl9sYWJlbCI7czoxMDoiU3Vic2NyaWJlISI7czoxNToiZW5iX3NtX2ZhY2Vib29rIjtzOjE6IjEiO3M6MTc6ImVuYl9zbV9nb29nbGVwbHVzIjtzOjE6IjEiO3M6MTQ6ImVuYl9zbV90d2l0dGVyIjtzOjE6IjEiO3M6OToic21fZGVzaWduIjtzOjY6InNpbXBsZSI7czoxMzoiYW5pbV9kdXJhdGlvbiI7czowOiIiO3M6ODoiYW5pbV9rZXkiO3M6MDoiIjtzOjU6InR4dF8wIjtzOjE1NjoiPHA+PGEgaHJlZj1cImh0dHA6Ly9zdXBzeXN0aWMuY29tXCIgdGFyZ2V0PVwiX2JsYW5rXCI+PGltZyBzcmM9XCJbUFBTX01PRF9VUkxdaW1nL2Fzc2V0cy9zdXBzeXN0aWNfaWNvbi5wbmdcIiBhbHQ9XCJcIiBzdHlsZT1cIm1heC13aWR0aDogMjAwcHhcIiAvPjwvYT48L3A+IjtzOjU6InR4dF8xIjtzOjMxMzoiPHA+UG9wdXAgYnkgU3Vwc3lzdGljIGxldHMgeW91IGVhc2lseSBjcmVhdGUgZWxlZ2FudCBvdmVybGFwcGluZyB3aW5kb3dzIHdpdGggdW5saW1pdGVkIGZlYXR1cmVzLiBQb3AtdXBzIHdpdGggU2xpZGVyLCBMaWdodGJveCwgQ29udGFjdCBhbmQgU3Vic2NyaXB0aW9uIGZvcm1zIGFuZCBtb3JlOjwvcD48dWw+PGxpPlVubGltaXRlZCBDb250ZW50IEN1c3RvbWl6YXRpb248L2xpPjxsaT5BdXRvIE9wZW4gUG9wdXBzPC9saT48bGk+Q29udGFjdCBGb3JtIHdpdGggcG9wLXVwPC9saT48bGk+UG9wdXAgT3BlbmluZyBBbmltYXRpb25zPC9saT48L3VsPiI7fXM6MTA6Im9wdHNfYXR0cnMiO2E6Mjp7czo5OiJiZ19udW1iZXIiO3M6MToiMyI7czoxNjoidHh0X2Jsb2NrX251bWJlciI7czoxOiIyIjt9fQ==","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupListsShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose ppsPopupClose_[close_btn]\"></a>\r\n  	<div class=\"ppsInnerTblContent\">\r\n      <div class=\"ppsPopupListsInner ppsPopupInner\">\r\n          [if enb_label]\r\n              <div class=\"ppsPopupLabel ppsPopupListsLabel\">[label]</div>\r\n          [endif]\r\n          <div style=\"clear: both;\"></div>\r\n          [if enb_txt_0]\r\n          <div class=\"ppsPopupTxt ppsPopupClassyTxt ppsPopupClassyTxt_0 ppsPopupTxt_0\">\r\n              [txt_0]\r\n          </div>\r\n          [endif]\r\n          [if enb_txt_1]\r\n          <div class=\"ppsPopupTxt ppsPopupClassyTxt ppsPopupClassyTxt_1 ppsPopupTxt_1\">\r\n              [txt_1]\r\n          </div>\r\n          [endif]\r\n          <div style=\"clear: both;\"></div>\r\n      </div>\r\n      <div class=\"ppsRightCol\">\r\n        [if enb_subscribe]\r\n        <div class=\"ppsSubscribeShell\">\r\n            [sub_form_start]\r\n                [if enb_sub_name]\r\n                <input type=\"text\" name=\"name\" placeholder=\"Name\" />\r\n                [endif]\r\n                <input type=\"text\" name=\"email\" placeholder=\"E-Mail\" />\r\n                <input type=\"submit\" name=\"submit\" value=\"Sign-up!\" />\r\n            [sub_form_end]\r\n            <div style=\"clear: both;\"></div>\r\n        </div>\r\n        [endif]\r\n        [if enb_sm]\r\n        <div class=\"ppsSm\">\r\n          [sm_html]\r\n        </div>\r\n        [endif]\r\n        [if enb_foot_note]\r\n        <div class=\"ppsFootNote\">\r\n          [foot_note]\r\n        </div>\r\n        [endif]\r\n      <div>\r\n	</div>\r\n</div>","#ppsPopupShell_[ID] {\r\n	width: [width][width_measure];\r\n  	padding: 15px;\r\n  	font-family: Georgia, Times, serif;\r\n	font-size: 13px;\r\n	line-height: 21px;\r\n	font-weight: normal;\r\n	color: #000;\r\n}\r\n#ppsPopupShell_[ID] .ppsInnerTblContent {\r\n	display: table;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupInner {\r\n  {% if popup.params.tpl.enb_subscribe or popup.params.tpl.enb_foot_note or popup.params.tpl.enb_sm %}\r\n  	width: 66%;\r\n	[else]\r\n  	width: 100%;\r\n  	[endif]\r\n  	display: table-cell;\r\n	[if bg_type_0 == \'color\']\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}), color-stop(100%, [bg_color_0])); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}\', endColorstr=\'[bg_color_0]\',GradientType=1 ); /* ie6-9 */\r\n  	[elseif bg_type_0 == \'img\']\r\n  	background-image: url(\"[bg_img_0]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  	\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupLabel {\r\n	color: #000;\r\n	font-family: \"Myriad Pro\",\"Trebuchet MS\",\"Helvetica Neue\",Helvetica,Arial,Sans-Serif;\r\n	font-size: 30px;\r\n	letter-spacing: -1px;\r\n	line-height: 40px;\r\n	letter-spacing: -1px;\r\n	font-weight: bold;\r\n	margin-top: 15px;\r\n	margin-bottom: 16px;\r\n	padding-left: 20px;\r\n	text-shadow: 0px 0px 1px #000;\r\n	-moz-text-shadow: 0px 0px 1px #000;\r\n	-webkit-text-shadow: 0px 0px 1px #000;\r\n}\r\n#ppsPopupShell_[ID] .ppsRightCol {\r\n	display: table-cell;\r\n  	width: 34%;\r\n  	height: 100%;\r\n  	[if bg_type_1 == \'color\']\r\n  	background: -moz-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }}), color-stop(100%, [bg_color_1])); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* ie10+ */\r\n  background:radial-gradient(ellipse at center, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }}\', endColorstr=\'[bg_color_1]\',GradientType=1 ); /* ie6-9 */\r\n  	[elseif bg_type_1 == \'img\']\r\n  	background-image: url(\"[bg_img_1]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  \r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell form {\r\n	padding: 30px 30px 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input {\r\n	width: 100%;\r\n  	margin-bottom: 10px;\r\n  	height: 40px;\r\n  	border: 1px solid #d1b36d;\r\n  	border-radius: 10px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text] {\r\n	box-shadow: 2px 2px 2px #dcdcdc inset;\r\n  	padding-left: 10px;\r\n  	font-size: 17px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text][name=\"email\"] {\r\n	background-image: url(\"[PPS_MOD_URL]img/assets/mail-icon.png\");\r\n  	background-repeat: no-repeat;\r\n  	background-position: 90% center;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit] {\r\n	border-color: #000;\r\n  	[if bg_type_2 == \'color\']\r\n  	background: [bg_color_2];\r\n    background: -moz-linear-gradient(90deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -webkit-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -o-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -ms-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: linear-gradient(0deg, [bg_color_2]) 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n  	[elseif bg_type_2 == \'img\']\r\n  	background-image: url(\"[bg_img_2]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  \r\n  	color: #fff;\r\n    font-size: 20px;\r\n    text-shadow: 2px 2px 2px #000;\r\n  	cursor: pointer;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:hover {\r\n	box-shadow: inset 1px 1px 3px #666;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupTxt_0 {\r\n	float: left;\r\n  	width: 50%;\r\n  	text-align: center;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupTxt_1 {\r\n	float: right;\r\n    [if enb_txt_0]\r\n    width: 50%;\r\n    [else]\r\n    width: 100%;\r\n    [endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose {\r\n	background-repeat: no-repeat;\r\n  	cursor: pointer;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose.ppsPopupClose_lists_black {\r\n 	top: 0 !important;\r\n  	right: 0 !important;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsFootNote{\r\n	color: #585858;\r\n    font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;\r\n    font-size: x-small;\r\n    font-style: italic;\r\n    line-height: 14px;\r\n  	margin: 5px 30px;\r\n}","","1","1","1","2015-01-10 18:59:43"),
			("2","Classy","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6Mjk6e3M6OToiZW5iX2xhYmVsIjtzOjE6IjEiO3M6NToibGFiZWwiO3M6MTg6IlBvcFVwIGJ5IFN1cHN5c3RpYyI7czo1OiJ3aWR0aCI7czozOiI2MzAiO3M6MTM6IndpZHRoX21lYXN1cmUiO3M6MjoicHgiO3M6MTg6ImJnX292ZXJsYXlfb3BhY2l0eSI7czozOiIwLjUiO3M6OToiYmdfdHlwZV8wIjtzOjU6ImNvbG9yIjtzOjg6ImJnX2ltZ18wIjtzOjA6IiI7czoxMDoiYmdfY29sb3JfMCI7czo3OiIjZDFkMWQxIjtzOjk6ImJnX3R5cGVfMSI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMSI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzEiO3M6NzoiIzdmYjZjYiI7czo5OiJjbG9zZV9idG4iO3M6MTE6ImNsYXNzeV9ncmV5IjtzOjc6ImJ1bGxldHMiO3M6MTE6ImNsYXNzeV9ibHVlIjtzOjk6ImVuYl90eHRfMCI7czoxOiIxIjtzOjk6ImVuYl90eHRfMSI7czoxOiIxIjtzOjk6ImZvb3Rfbm90ZSI7czowOiIiO3M6MTM6ImVuYl9zdWJzY3JpYmUiO3M6MToiMSI7czo4OiJzdWJfZGVzdCI7czo5OiJ3b3JkcHJlc3MiO3M6MTk6InN1Yl9hd2ViZXJfbGlzdG5hbWUiO3M6MDoiIjtzOjEyOiJlbmJfc3ViX25hbWUiO3M6MToiMSI7czoxMzoic3ViX2J0bl9sYWJlbCI7czo4OiJTaWduLVVwISI7czoxNToiZW5iX3NtX2ZhY2Vib29rIjtzOjE6IjEiO3M6MTc6ImVuYl9zbV9nb29nbGVwbHVzIjtzOjE6IjEiO3M6MTQ6ImVuYl9zbV90d2l0dGVyIjtzOjE6IjEiO3M6OToic21fZGVzaWduIjtzOjY6InNpbXBsZSI7czoxMzoiYW5pbV9kdXJhdGlvbiI7czowOiIiO3M6ODoiYW5pbV9rZXkiO3M6MDoiIjtzOjU6InR4dF8wIjtzOjIwODoiPHA+UG9wdXAgYnkgU3Vwc3lzdGljIGxldHMgeW91IGVhc2lseSBjcmVhdGUgZWxlZ2FudCBvdmVybGFwcGluZyB3aW5kb3dzIHdpdGggdW5saW1pdGVkIGZlYXR1cmVzOjwvcD48dWw+PGxpPlVubGltaXRlZCBDb250ZW50IEN1c3RvbWl6YXRpb248L2xpPjxsaT5BdXRvIE9wZW4gUG9wdXBzPC9saT48bGk+Q29udGFjdCBGb3JtIHdpdGggcG9wLXVwPC9saT48L3VsPiI7czo1OiJ0eHRfMSI7czoxMjA6IjxwPjxpbWcgc3JjPVwiW1BQU19NT0RfVVJMXWltZy9hc3NldHMvc3Vwc3lzdGljX2ljb24ucG5nXCIgYWx0PVwiXCIgc3R5bGU9XCJtYXgtd2lkdGg6IDE4MHB4OyBtYXgtaGVpZ2h0OiBhdXRvO1wiIC8+PC9wPiI7fXM6MTA6Im9wdHNfYXR0cnMiO2E6Mjp7czo5OiJiZ19udW1iZXIiO3M6MToiMiI7czoxNjoidHh0X2Jsb2NrX251bWJlciI7czoxOiIyIjt9fQ==","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupClassyShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose\"></a>\r\n	<div class=\"ppsPopupClassyInner\">\r\n		[if enb_label]\r\n			<div class=\"ppsPopupLabel ppsPopupClassyLabel\">[label]</div>\r\n		[endif]\r\n		[if enb_txt_0]\r\n		<div class=\"ppsPopupTxt ppsPopupClassyTxt ppsPopupClassyTxt_0\">\r\n			[txt_0]\r\n		</div>\r\n		[endif]\r\n		[if enb_txt_1]\r\n		<div class=\"ppsPopupTxt ppsPopupClassyTxt ppsPopupClassyTxt_1\">\r\n			[txt_1]\r\n		</div>\r\n		[endif]\r\n      	[if enb_sm]\r\n        <div class=\"ppsSm\">\r\n            [sm_html]\r\n        </div>\r\n        [endif]\r\n		<div style=\"clear: both;\"></div>\r\n	</div>\r\n	[if enb_subscribe]\r\n	<div class=\"ppsSubscribeShell\">\r\n		[sub_form_start]\r\n          	[if enb_sub_name]\r\n			<input type=\"text\" name=\"name\" placeholder=\"Name\" />\r\n          	[endif]\r\n			<input type=\"text\" name=\"email\" placeholder=\"E-Mail\" />\r\n			<input type=\"submit\" name=\"submit\" value=\"[sub_btn_label]\" />\r\n		[sub_form_end]\r\n	</div>\r\n  	[endif]\r\n  	[if enb_foot_note]\r\n  	<div class=\"ppsFootNote\">[foot_note]</div>\r\n  	[endif]\r\n</div>","#ppsPopupShell_[ID] {\r\n  	width: [width][width_measure];\r\n	[if bg_type_0 == \'color\']\r\n	background-color: {{ popup.params.tpl.bg_color_0 }};\r\n	[elseif bg_type_0 == \'img\']\r\n	background-image: url(\"[bg_img_0]\");\r\n	background-size: 100%;\r\n  	background-repeat: no-repeat;\r\n	[endif]\r\n	padding: 7px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyInner {\r\n	padding: 15px;\r\n	border: {{ adjust_brightness(popup.params.tpl.bg_color_0, 30) }};\r\n	[if bg_type_0 == \'color\']\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ popup.params.tpl.bg_color_0 }}), color-stop(100%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }})); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ popup.params.tpl.bg_color_0 }}\', endColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }}\',GradientType=1 ); /* ie6-9 */\r\n	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input {\r\n	height: 48px;\r\n	font-size: 27px;\r\n	border: none;\r\n	padding: 1px 8px 0;\r\n	width: calc((100% - 50px) / 3);\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text] {\r\n	[if bg_type_0 == \'color\']\r\n	-webkit-box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	-moz-box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	background-color: {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }};\r\n	color: {{ adjust_brightness(popup.params.tpl.bg_color_0, -100) }};\r\n	[endif]\r\n	margin-right: 5px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit] {\r\n	color: #eee;\r\n	margin-right: 0;\r\n	cursor: pointer;\r\n	[if bg_type_1 == \'color\']\r\n	text-shadow: -1px -1px 1px {{ adjust_brightness(popup.params.tpl.bg_color_1, -80) }};\r\n	border: 1px solid {{ adjust_brightness(popup.params.tpl.bg_color_1, -40) }};\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ popup.params.tpl.bg_color_1 }}), color-stop(100%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }})); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ popup.params.tpl.bg_color_1 }}\', endColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }}\',GradientType=1 ); /* ie6-9 */\r\n\r\n	-webkit-box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	-moz-box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	[elseif bg_type_1 == \'img\']\r\n	background-image: url(\"[bg_img_1]\");\r\n	background-size: 100%;\r\n  	background-repeat: no-repeat;\r\n	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:disabled {\r\n	color: transparent;\r\n  	cursor: progress;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell {\r\n	padding-top: 10px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell form {\r\n	margin: 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyLabel {\r\n	width: 100%;\r\n	text-align: center;\r\n	color: #555555;\r\n	text-shadow: 1px 1px 1px #ffffff;\r\n	font-size: 35px;\r\n	margin-top: 26px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSuccessMsg {\r\n	color: #555555;\r\n  	border: 1px solid #555555;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyTxt {\r\n	font-size: 20px;\r\n	line-height: 160%;\r\n	color: rgb(85, 85, 85);\r\n	padding-top: 14px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyTxt_0 {\r\n	float: left;\r\n  	[if enb_txt_1]\r\n	width: 70%;\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyTxt_1 {\r\n	float: right;\r\n	width: 30%;\r\n}\r\n#ppsPopupShell_[ID] ul {\r\n	margin-top: 15px;\r\n	padding-left: 0;\r\n}\r\n#ppsPopupShell_[ID] ul li {\r\n	list-style: inside none disc;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsFootNote {\r\n	color: #999;\r\n    font-size: 12px;\r\n    padding-top: 10px;\r\n    text-align: center;\r\n}\r\n/*SM*/\r\n#ppsPopupShell_[ID] .ppsSm {\r\n	padding: 10px 10px 0 10px;\r\n  	text-align: center;\r\n}\r\n#ppsPopupShell_1_435376 .ppsSm > div {\r\n	vertical-align: top !important;\r\n}\r\n#ppsPopupShell_[ID] .ppsSm .ppsSmBtn {\r\n	margin-right: 5px;\r\n}","","1","1","1","2015-01-03 17:00:43"),
			("3","Fastest and the Easiest","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6MzM6e3M6OToiZW5iX2xhYmVsIjtzOjE6IjEiO3M6NToibGFiZWwiO3M6NTU6IkluY3JlYXNlIHN1YnNjcmliZXJzLCBzb2NpYWwgZm9sbG93ZXJzIG9yIGFkdmVydGlzZW1lbnQiO3M6NToid2lkdGgiO3M6MzoiNzcwIjtzOjEzOiJ3aWR0aF9tZWFzdXJlIjtzOjI6InB4IjtzOjE4OiJiZ19vdmVybGF5X29wYWNpdHkiO3M6MzoiMC41IjtzOjk6ImJnX3R5cGVfMCI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMCI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzAiO3M6NzoiI2U1ZTVlNSI7czo5OiJiZ190eXBlXzEiO3M6MzoiaW1nIjtzOjg6ImJnX2ltZ18xIjtzOjM2OiJbUFBTX01PRF9VUkxdaW1nL2Fzc2V0cy9idXR0b24tMS5wbmciO3M6MTA6ImJnX2NvbG9yXzEiO3M6MDoiIjtzOjk6ImJnX3R5cGVfMiI7czozOiJpbWciO3M6ODoiYmdfaW1nXzIiO3M6Mzk6IltQUFNfTU9EX1VSTF1pbWcvYXNzZXRzL2JpZy1hcnJvdy0xLnBuZyI7czoxMDoiYmdfY29sb3JfMiI7czowOiIiO3M6OToiY2xvc2VfYnRuIjtzOjExOiJsaXN0c19ibGFjayI7czo3OiJidWxsZXRzIjtzOjExOiJsaXN0c19ncmVlbiI7czo5OiJlbmJfdHh0XzAiO3M6MToiMSI7czo5OiJlbmJfdHh0XzEiO3M6MToiMSI7czoxMzoiZW5iX2Zvb3Rfbm90ZSI7czoxOiIxIjtzOjk6ImZvb3Rfbm90ZSI7czoxMTc6IldlIHJlc3BlY3QgeW91ciBwcml2YWN5LiBZb3VyIGluZm9ybWF0aW9uIHdpbGwgbm90IGJlIHNoYXJlZCB3aXRoIGFueSB0aGlyZCBwYXJ0eSBhbmQgeW91IGNhbiB1bnN1YnNjcmliZSBhdCBhbnkgdGltZSI7czoxMzoiZW5iX3N1YnNjcmliZSI7czoxOiIxIjtzOjg6InN1Yl9kZXN0IjtzOjk6IndvcmRwcmVzcyI7czoxOToic3ViX2F3ZWJlcl9saXN0bmFtZSI7czowOiIiO3M6MTI6ImVuYl9zdWJfbmFtZSI7czoxOiIxIjtzOjEzOiJzdWJfYnRuX2xhYmVsIjtzOjU6IkpvaW4hIjtzOjE1OiJlbmJfc21fZmFjZWJvb2siO3M6MToiMSI7czoxNzoiZW5iX3NtX2dvb2dsZXBsdXMiO3M6MToiMSI7czoxNDoiZW5iX3NtX3R3aXR0ZXIiO3M6MToiMSI7czo5OiJzbV9kZXNpZ24iO3M6Njoic2ltcGxlIjtzOjEzOiJhbmltX2R1cmF0aW9uIjtzOjA6IiI7czo4OiJhbmltX2tleSI7czowOiIiO3M6NToidHh0XzAiO3M6NDQ6IjxwPlRoZSBCZXN0IFdvcmRQcmVzcyBQb3BVcCBvcHRpbiBwbHVnaW48L3A+IjtzOjU6InR4dF8xIjtzOjMxMzoiPHA+UG9wdXAgYnkgU3Vwc3lzdGljIGxldHMgeW91IGVhc2lseSBjcmVhdGUgZWxlZ2FudCBvdmVybGFwcGluZyB3aW5kb3dzIHdpdGggdW5saW1pdGVkIGZlYXR1cmVzLiBQb3AtdXBzIHdpdGggU2xpZGVyLCBMaWdodGJveCwgQ29udGFjdCBhbmQgU3Vic2NyaXB0aW9uIGZvcm1zIGFuZCBtb3JlOjwvcD48dWw+PGxpPlVubGltaXRlZCBDb250ZW50IEN1c3RvbWl6YXRpb248L2xpPjxsaT5BdXRvIE9wZW4gUG9wdXBzPC9saT48bGk+Q29udGFjdCBGb3JtIHdpdGggcG9wLXVwPC9saT48bGk+UG9wdXAgT3BlbmluZyBBbmltYXRpb25zPC9saT48L3VsPiI7fXM6MTA6Im9wdHNfYXR0cnMiO2E6Mjp7czo5OiJiZ19udW1iZXIiO3M6MToiMyI7czoxNjoidHh0X2Jsb2NrX251bWJlciI7czoxOiIyIjt9fQ==","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupListsShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose\"></a>\r\n  	<div class=\"ppsPopupListsInner\">\r\n  		[if enb_txt_0]\r\n  		<div class=\"ppsPopupTxt ppsPopupTxt_0\">\r\n          <div class=\"ppsTxtContent\">[txt_0]</div>\r\n      	</div>\r\n  		[endif]\r\n      	<div class=\"ppsPopupTblCols\">\r\n          <div class=\"ppsPopupLeftCol\">\r\n            [if enb_label]\r\n            <div class=\"ppsPopupLabel\">[label]</div>\r\n            [endif]\r\n            [if enb_txt_1]\r\n            <div class=\"ppsPopupTxt ppsPopupTxt_1\">[txt_1]</div>\r\n            [endif]\r\n          </div>\r\n          <div class=\"ppsPopupRightCol\">\r\n            [if enb_subscribe]\r\n            <div class=\"ppsSubscribeShell\">\r\n              <div class=\"ppsBigArrow\"></div>\r\n              [sub_form_start]\r\n              <input type=\"text\" name=\"name\" placeholder=\"Name\" />\r\n              <input type=\"text\" name=\"email\" placeholder=\"E-Mail\" />\r\n              <input type=\"submit\" name=\"submit\" value=\"[sub_btn_label]\" />\r\n              [sub_form_end]\r\n            </div>\r\n            [endif]\r\n            [if enb_sm]\r\n            <div class=\"ppsSm\">\r\n              [sm_html]\r\n            </div>\r\n            [endif]\r\n            [if enb_foot_note]\r\n            <div class=\"ppsFootNote\">\r\n              [foot_note]\r\n            </div>\r\n            [endif]\r\n          </div>\r\n      	</div>\r\n  	</div>\r\n  	\r\n</div>","#ppsPopupShell_[ID] {\r\n	width: [width][width_measure];\r\n  	font-family: Helvetica,Arial,sans-serif;\r\n  	font-size: 14px;\r\n  \r\n  	[if bg_type_0 == \'img\']\r\n  	background-image: url(\"[bg_img_0]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupLabel {\r\n	color: #414141;\r\n    font-size: 28px;\r\n    font-style: normal;\r\n    font-weight: bold;\r\n    letter-spacing: -1px;\r\n    line-height: 35px;\r\n    text-shadow: 0 1px 1px #000;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupListsInner {\r\n  	[if bg_type_0 == \'color\']\r\n    background: {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}; /* Old browsers */\r\n    background: -moz-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* FF3.6+ */\r\n    background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}), color-stop(100%,[bg_color_0])); /* Chrome,Safari4+ */\r\n    background: -webkit-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%,[bg_color_0] 100%); /* Chrome10+,Safari5.1+ */\r\n    background: -o-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%,[bg_color_0] 100%); /* Opera 12+ */\r\n    background: -ms-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%,[bg_color_0] 100%); /* IE10+ */\r\n    background: radial-gradient(ellipse at center, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%,[bg_color_0] 100%); /* W3C */\r\n    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}\', endColorstr=\'#eaeaea\',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupTxt_0 {\r\n	width: 100%;\r\n  	border-bottom: 1px solid {{ adjust_brightness(popup.params.tpl.bg_color_0, -50) }};\r\n  	clear: both;\r\n  	font-size: 13px;\r\n    font-weight: bold;\r\n  	font-family: Helvetica,Arial,sans-serif;\r\n  	[if bg_type_0 == \'color\']\r\n  	background: [bg_color_0]; /* Old browsers */\r\n    background: -moz-linear-gradient(top, [bg_color_0] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }} 100%); /* FF3.6+ */\r\n    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,[bg_color_0]), color-stop(100%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }})); /* Chrome,Safari4+ */\r\n    background: -webkit-linear-gradient(top, [bg_color_0] 0%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }} 100%); /* Chrome10+,Safari5.1+ */\r\n    background: -o-linear-gradient(top, [bg_color_0] 0%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }} 100%); /* Opera 11.10+ */\r\n    background: -ms-linear-gradient(top, [bg_color_0] 0%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }} 100%); /* IE10+ */\r\n    background: linear-gradient(to bottom, [bg_color_0] 0%,{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }} 100%); /* W3C */\r\n    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'[bg_color_0]\', endColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, 20) }}\',GradientType=0 ); /* IE6-9 */\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupTxt_0 .ppsTxtContent {\r\n	padding: 10px 10px 10px 50px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupTblCols {\r\n	display: table;\r\n  	padding: 10px 10px 10px 50px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupLeftCol {\r\n	display: table-cell;\r\n  	[if enb_subscribe]\r\n  	width: 64%;\r\n  	[else]\r\n  	width: 100%;\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupRightCol {\r\n  	width: 36%;\r\n	display: table-cell;\r\n  	position: relative;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell form {\r\n	padding: 30px 30px 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input {\r\n	width: 100%;\r\n  	margin-bottom: 10px;\r\n  	height: 40px;\r\n  	border: 1px solid #000;\r\n  	border-radius: 10px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text] {\r\n	box-shadow: 2px 2px 2px #dcdcdc inset;\r\n  	padding-left: 10px;\r\n  	font-size: 17px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text][name=\"email\"] {\r\n	background-image: url(\"[PPS_MOD_URL]img/assets/mail-icon.png\");\r\n  	background-repeat: no-repeat;\r\n  	background-position: 90% center;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit] {\r\n	border: none;\r\n  	[if bg_type_1 == \'color\']\r\n  	background: [bg_color_1];\r\n    background: -moz-linear-gradient(90deg, [bg_color_1] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 100) }} 63%);\r\n    background: -webkit-linear-gradient(270deg, [bg_color_1] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 100) }} 63%);\r\n    background: -o-linear-gradient(270deg, [bg_color_1] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 100) }} 63%);\r\n    background: -ms-linear-gradient(270deg, [bg_color_1] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 100) }} 63%);\r\n    background: linear-gradient(0deg, [bg_color_1]) 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 100) }} 63%);\r\n  	[elseif bg_type_1 == \'img\']\r\n  	background-image: url(\"[bg_img_1]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  \r\n  	color: #000;\r\n    font-size: 20px;\r\n    text-shadow: 2px 2px 2px #000;\r\n  	cursor: pointer;\r\n  	max-width: 230px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:hover {\r\n	box-shadow: inset 1px 1px 3px #666;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose {\r\n	background-repeat: no-repeat;\r\n  	cursor: pointer;\r\n}\r\n#ppsPopupShell_[ID] .ppsFootNote {\r\n    color: #979696;\r\n    font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;\r\n    font-size: xx-small;\r\n    font-style: italic;\r\n    line-height: 14px;\r\n    text-align: left;\r\n  	margin-left: 30px;\r\n}\r\n#ppsPopupShell_[ID] .ppsBigArrow {\r\n	[if bg_type_2 == \'img\']\r\n  	position: absolute;\r\n  	width: 102px;\r\n    height: 81px;\r\n  	background-image: url(\"[bg_img_2]\");\r\n  	background-repeat: no-repeat;\r\n  	top: -20px;\r\n  	right: 20px;\r\n  	z-index: 100;\r\n  	[else]\r\n  	display: none;\r\n  	[endif]\r\n}","","1","1","1","2015-01-13 19:22:48"),
			("4","Facebook Like","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6MTI6e3M6NToid2lkdGgiO3M6MzoiMzAwIjtzOjEzOiJ3aWR0aF9tZWFzdXJlIjtzOjI6InB4IjtzOjY6ImhlaWdodCI7czowOiIiO3M6MTQ6ImhlaWdodF9tZWFzdXJlIjtzOjI6InB4IjtzOjEyOiJmYl9saWtlX29wdHMiO2E6NTp7czo0OiJocmVmIjtzOjU3OiJodHRwczovL3d3dy5mYWNlYm9vay5jb20vcGFnZXMvU3Vwc3lzdGljLzEzODkzOTAxOTgwMjg5OTkiO3M6MTE6ImNvbG9yc2NoZW1lIjtzOjU6ImxpZ2h0IjtzOjY6ImhlYWRlciI7czoxOiIxIjtzOjExOiJzaG93X2JvcmRlciI7czoxOiIxIjtzOjEwOiJzaG93X2ZhY2VzIjtzOjE6IjEiO31zOjE4OiJiZ19vdmVybGF5X29wYWNpdHkiO3M6MzoiMC41IjtzOjk6ImJnX3R5cGVfMCI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMCI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzAiO3M6NzoiI2ZmZmZmZiI7czo5OiJjbG9zZV9idG4iO3M6MTE6ImNsYXNzeV9ncmV5IjtzOjEzOiJhbmltX2R1cmF0aW9uIjtzOjE6IjEiO3M6ODoiYW5pbV9rZXkiO3M6NDoibm9uZSI7fXM6MTA6Im9wdHNfYXR0cnMiO2E6Mjp7czo5OiJiZ19udW1iZXIiO3M6MToiMSI7czoxNjoidHh0X2Jsb2NrX251bWJlciI7czoxOiIwIjt9fQ==","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupFbLikeShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose\"></a>\r\n  	[fb_like_widget_html]\r\n</div>","#ppsPopupShell_[ID] {\r\n	width: [width][width_measure];\r\n  	/*height: [height][height_measure];*/\r\n  	[if bg_type_0 == \'color\']\r\n  	background-color: [bg_color_0];\r\n  	[elseif bg_type_0 == \'img\']\r\n  	background-repeat: no_repeat;\r\n  	background-image: url(\"[bg_img_0]\");\r\n  	background-size: cover;\r\n  	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose {\r\n	z-index: 99;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n","","1","1","2","2015-01-16 19:35:51"),
			("5","Video List","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6MzA6e3M6NToibGFiZWwiO3M6NTk6IkNoZWNrIHZpZGVvIGFuZCA8aSBzdHlsZT1cImNvbG9yOiAjYzIyNTJmO1wiPnN1YnNjcmliZSE8L2k+IjtzOjk6InZpZGVvX3VybCI7czo0MzoiaHR0cHM6Ly93d3cueW91dHViZS5jb20vd2F0Y2g/dj1uS0l1OXllbjVuYyI7czo1OiJ3aWR0aCI7czozOiI4MjQiO3M6MTM6IndpZHRoX21lYXN1cmUiO3M6MjoicHgiO3M6NjoiaGVpZ2h0IjtzOjM6IjQwMCI7czoxNDoiaGVpZ2h0X21lYXN1cmUiO3M6MjoicHgiO3M6MTg6ImJnX292ZXJsYXlfb3BhY2l0eSI7czozOiIwLjUiO3M6OToiYmdfdHlwZV8wIjtzOjU6ImNvbG9yIjtzOjg6ImJnX2ltZ18wIjtzOjA6IiI7czoxMDoiYmdfY29sb3JfMCI7czo3OiIjZWJlYmViIjtzOjk6ImJnX3R5cGVfMSI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMSI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzEiO3M6NzoiIzA2NmRhYiI7czo5OiJiZ190eXBlXzIiO3M6NToiY29sb3IiO3M6ODoiYmdfaW1nXzIiO3M6MDoiIjtzOjEwOiJiZ19jb2xvcl8yIjtzOjc6IiM4NTAwMDgiO3M6OToiY2xvc2VfYnRuIjtzOjExOiJsaXN0c19ibGFjayI7czoxMzoiZW5iX2Zvb3Rfbm90ZSI7czoxOiIxIjtzOjk6ImZvb3Rfbm90ZSI7czoxMTg6IldlIHJlc3BlY3QgeW91ciBwcml2YWN5LiBZb3VyIGluZm9ybWF0aW9uIHdpbGwgbm90IGJlIHNoYXJlZCB3aXRoIGFueSB0aGlyZCBwYXJ0eSBhbmQgeW91IGNhbiB1bnN1YnNjcmliZSBhdCBhbnkgdGltZSAiO3M6MTM6ImVuYl9zdWJzY3JpYmUiO3M6MToiMSI7czo4OiJzdWJfZGVzdCI7czo5OiJ3b3JkcHJlc3MiO3M6MTk6InN1Yl9hd2ViZXJfbGlzdG5hbWUiO3M6MDoiIjtzOjEyOiJlbmJfc3ViX25hbWUiO3M6MToiMSI7czoxMzoic3ViX2J0bl9sYWJlbCI7czoxMDoiU3Vic2NyaWJlISI7czoxNToiZW5iX3NtX2ZhY2Vib29rIjtzOjE6IjEiO3M6MTc6ImVuYl9zbV9nb29nbGVwbHVzIjtzOjE6IjEiO3M6MTQ6ImVuYl9zbV90d2l0dGVyIjtzOjE6IjEiO3M6OToic21fZGVzaWduIjtzOjY6InNpbXBsZSI7czoxMzoiYW5pbV9kdXJhdGlvbiI7czowOiIiO3M6ODoiYW5pbV9rZXkiO3M6MDoiIjt9czoxMDoib3B0c19hdHRycyI7YTozOntzOjk6ImJnX251bWJlciI7czoxOiIzIjtzOjE2OiJ0eHRfYmxvY2tfbnVtYmVyIjtzOjE6IjAiO3M6MjE6InZpZGVvX2hlaWdodF9hc19wb3B1cCI7czoxOiIxIjt9fQ==","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupListsShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose ppsPopupClose_[close_btn]\"></a>\r\n  	<div class=\"ppsInnerTblContent\">\r\n      <div class=\"ppsPopupListsInner ppsPopupInner\">\r\n          [if enb_label]\r\n        	<div class=\"ppsPopupLabel ppsPopupListsLabel\">[label]</div>\r\n        	<div style=\"clear: both;\"></div>\r\n          [endif]\r\n          <div class=\"ppsPopupVideo \">\r\n              [video_html]\r\n          </div>\r\n          <div style=\"clear: both;\"></div>\r\n      </div>\r\n      <div class=\"ppsRightCol\">\r\n        [if enb_subscribe]\r\n        <div class=\"ppsSubscribeShell\">\r\n            [sub_form_start]\r\n                [if enb_sub_name]\r\n                <input type=\"text\" name=\"name\" placeholder=\"Name\" />\r\n                [endif]\r\n                <input type=\"text\" name=\"email\" placeholder=\"E-Mail\" />\r\n                <input type=\"submit\" name=\"submit\" value=\"Sign-up!\" />\r\n            [sub_form_end]\r\n            <div style=\"clear: both;\"></div>\r\n        </div>\r\n        [endif]\r\n        [if enb_sm]\r\n        <div class=\"ppsSm\">\r\n          [sm_html]\r\n        </div>\r\n        [endif]\r\n        [if enb_foot_note]\r\n        <div class=\"ppsFootNote\">\r\n          [foot_note]\r\n        </div>\r\n        [endif]\r\n      <div>\r\n	</div>\r\n</div>","#ppsPopupShell_[ID] {\r\n	width: [width][width_measure];\r\n  	padding: 15px;\r\n  	font-family: Georgia, Times, serif;\r\n	font-size: 13px;\r\n	line-height: 21px;\r\n	font-weight: normal;\r\n	color: #000;\r\n}\r\n#ppsPopupShell_[ID] iframe {\r\n	width: 100% !important;\r\n}\r\n#ppsPopupShell_[ID] .ppsInnerTblContent {\r\n	display: table;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupInner {\r\n  {% if popup.params.tpl.enb_subscribe or popup.params.tpl.enb_foot_note or popup.params.tpl.enb_sm %}\r\n  	width: 66%;\r\n	[else]\r\n  	width: 100%;\r\n  	[endif]\r\n  	display: table-cell;\r\n	[if bg_type_0 == \'color\']\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}), color-stop(100%, [bg_color_0])); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }} 0%, [bg_color_0] 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, 50) }}\', endColorstr=\'[bg_color_0]\',GradientType=1 ); /* ie6-9 */\r\n  	[elseif bg_type_0 == \'img\']\r\n  	background-image: url(\"[bg_img_0]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  	\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupLabel {\r\n	color: #000;\r\n	font-family: \"Myriad Pro\",\"Trebuchet MS\",\"Helvetica Neue\",Helvetica,Arial,Sans-Serif;\r\n	font-size: 30px;\r\n	letter-spacing: -1px;\r\n	line-height: 40px;\r\n	letter-spacing: -1px;\r\n	font-weight: bold;\r\n	margin-top: 15px;\r\n	margin-bottom: 16px;\r\n	padding-left: 20px;\r\n	text-shadow: 0px 0px 1px #000;\r\n	-moz-text-shadow: 0px 0px 1px #000;\r\n	-webkit-text-shadow: 0px 0px 1px #000;\r\n}\r\n#ppsPopupShell_[ID] .ppsRightCol {\r\n	display: table-cell;\r\n  	width: 34%;\r\n  	height: 100%;\r\n  	vertical-align: top;\r\n  	[if bg_type_1 == \'color\']\r\n  	background: -moz-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }}), color-stop(100%, [bg_color_1])); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* ie10+ */\r\n  background:radial-gradient(ellipse at center, {{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }} 0%, [bg_color_1] 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_1, 50) }}\', endColorstr=\'[bg_color_1]\',GradientType=1 ); /* ie6-9 */\r\n  	[elseif bg_type_1 == \'img\']\r\n  	background-image: url(\"[bg_img_1]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  \r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell form {\r\n	padding: 30px 30px 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input {\r\n	width: 100%;\r\n  	margin-bottom: 10px;\r\n  	height: 40px;\r\n  	border: 1px solid #d1b36d;\r\n  	border-radius: 10px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text] {\r\n	box-shadow: 2px 2px 2px #dcdcdc inset;\r\n  	padding-left: 10px;\r\n  	font-size: 17px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text][name=\"email\"] {\r\n	background-image: url(\"[PPS_MOD_URL]img/assets/mail-icon.png\");\r\n  	background-repeat: no-repeat;\r\n  	background-position: 90% center;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit] {\r\n	border-color: #000;\r\n  	[if bg_type_2 == \'color\']\r\n  	background: [bg_color_2];\r\n    background: -moz-linear-gradient(90deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -webkit-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -o-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: -ms-linear-gradient(270deg, [bg_color_2] 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n    background: linear-gradient(0deg, [bg_color_2]) 0%, {{ adjust_brightness(popup.params.tpl.bg_color_2, 100) }} 63%);\r\n  	[elseif bg_type_2 == \'img\']\r\n  	background-image: url(\"[bg_img_2]\");\r\n  	background-repeat: no-repeat;\r\n  	background-size: cover;\r\n  	[endif]\r\n  \r\n  	color: #fff;\r\n    font-size: 20px;\r\n    text-shadow: 2px 2px 2px #000;\r\n  	cursor: pointer;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:hover {\r\n	box-shadow: inset 1px 1px 3px #666;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupVideo {\r\n  	width: 100%;\r\n  	line-height: 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose {\r\n	background-repeat: no-repeat;\r\n  	cursor: pointer;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose.ppsPopupClose_lists_black {\r\n 	top: 0 !important;\r\n  	right: 0 !important;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsFootNote{\r\n	color: #585858;\r\n    font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;\r\n    font-size: x-small;\r\n    font-style: italic;\r\n    line-height: 14px;\r\n  	margin: 5px 30px;\r\n}","","1","1","3","2015-01-10 18:59:43"),
			("6","Video Classy","1","0","YTozOntzOjQ6Im1haW4iO2E6NDp7czo3OiJzaG93X29uIjtzOjk6InBhZ2VfbG9hZCI7czoyMzoic2hvd19vbl9wYWdlX2xvYWRfZGVsYXkiO3M6MDoiIjtzOjc6InNob3dfdG8iO3M6ODoiZXZlcnlvbmUiO3M6MTA6InNob3dfcGFnZXMiO3M6MzoiYWxsIjt9czozOiJ0cGwiO2E6MjU6e3M6NToibGFiZWwiO3M6MTg6Ikxvb2sgb3VyIG5ldyB2aWRlbyI7czo5OiJ2aWRlb191cmwiO3M6NDM6Imh0dHBzOi8vd3d3LnlvdXR1YmUuY29tL3dhdGNoP3Y9bktJdTl5ZW41bmMiO3M6NToid2lkdGgiO3M6MzoiNjMwIjtzOjEzOiJ3aWR0aF9tZWFzdXJlIjtzOjI6InB4IjtzOjY6ImhlaWdodCI7czozOiI0ODAiO3M6MTQ6ImhlaWdodF9tZWFzdXJlIjtzOjI6InB4IjtzOjE4OiJiZ19vdmVybGF5X29wYWNpdHkiO3M6MzoiMC41IjtzOjk6ImJnX3R5cGVfMCI7czo1OiJjb2xvciI7czo4OiJiZ19pbWdfMCI7czowOiIiO3M6MTA6ImJnX2NvbG9yXzAiO3M6NzoiI2VjZTBkMSI7czo5OiJiZ190eXBlXzEiO3M6NToiY29sb3IiO3M6ODoiYmdfaW1nXzEiO3M6MDoiIjtzOjEwOiJiZ19jb2xvcl8xIjtzOjc6IiM3ZmI2Y2IiO3M6OToiY2xvc2VfYnRuIjtzOjExOiJjbGFzc3lfZ3JleSI7czo5OiJmb290X25vdGUiO3M6MDoiIjtzOjg6InN1Yl9kZXN0IjtzOjk6IndvcmRwcmVzcyI7czoxOToic3ViX2F3ZWJlcl9saXN0bmFtZSI7czowOiIiO3M6MTI6ImVuYl9zdWJfbmFtZSI7czoxOiIxIjtzOjEzOiJzdWJfYnRuX2xhYmVsIjtzOjg6IlNpZ24tVXAhIjtzOjE1OiJlbmJfc21fZmFjZWJvb2siO3M6MToiMSI7czoxNzoiZW5iX3NtX2dvb2dsZXBsdXMiO3M6MToiMSI7czoxNDoiZW5iX3NtX3R3aXR0ZXIiO3M6MToiMSI7czo5OiJzbV9kZXNpZ24iO3M6Njoic2ltcGxlIjtzOjEzOiJhbmltX2R1cmF0aW9uIjtzOjA6IiI7czo4OiJhbmltX2tleSI7czowOiIiO31zOjEwOiJvcHRzX2F0dHJzIjthOjQ6e3M6OToiYmdfbnVtYmVyIjtzOjE6IjIiO3M6MTY6InR4dF9ibG9ja19udW1iZXIiO3M6MToiMCI7czoyMDoidmlkZW9fd2lkdGhfYXNfcG9wdXAiO3M6MToiMSI7czoyMToidmlkZW9faGVpZ2h0X2FzX3BvcHVwIjtzOjE6IjEiO319","<div id=\"ppsPopupShell_[ID]\" class=\"ppsPopupShell ppsPopupClassyShell\">\r\n	<a href=\"#\" class=\"ppsPopupClose\"></a>\r\n	<div class=\"ppsPopupClassyInner\">\r\n		[if enb_label]\r\n			<div class=\"ppsPopupLabel ppsPopupClassyLabel\">[label]</div>\r\n		[endif]\r\n		<div class=\"ppsPopupVideo\">\r\n			[video_html]\r\n		</div>\r\n      	[if enb_sm]\r\n        <div class=\"ppsSm\">\r\n            [sm_html]\r\n        </div>\r\n        [endif]\r\n		<div style=\"clear: both;\"></div>\r\n	</div>\r\n	[if enb_subscribe]\r\n	<div class=\"ppsSubscribeShell\">\r\n		[sub_form_start]\r\n          	[if enb_sub_name]\r\n			<input type=\"text\" name=\"name\" placeholder=\"Name\" />\r\n          	[endif]\r\n			<input type=\"text\" name=\"email\" placeholder=\"E-Mail\" />\r\n			<input type=\"submit\" name=\"submit\" value=\"[sub_btn_label]\" />\r\n		[sub_form_end]\r\n	</div>\r\n  	[endif]\r\n  	[if enb_foot_note]\r\n  	<div class=\"ppsFootNote\">[foot_note]</div>\r\n  	[endif]\r\n</div>","#ppsPopupShell_[ID] {\r\n  	width: [width][width_measure];\r\n	[if bg_type_0 == \'color\']\r\n	background-color: {{ popup.params.tpl.bg_color_0 }};\r\n	[elseif bg_type_0 == \'img\']\r\n	background-image: url(\"[bg_img_0]\");\r\n	background-size: 100%;\r\n  	background-repeat: no-repeat;\r\n	[endif]\r\n	padding: 7px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyInner {\r\n	padding: 0;\r\n	border: {{ adjust_brightness(popup.params.tpl.bg_color_0, 30) }};\r\n	[if bg_type_0 == \'color\']\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ popup.params.tpl.bg_color_0 }}), color-stop(100%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }})); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ popup.params.tpl.bg_color_0 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }} 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ popup.params.tpl.bg_color_0 }}\', endColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }}\',GradientType=1 ); /* ie6-9 */\r\n	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input {\r\n	height: 48px;\r\n	font-size: 27px;\r\n	border: none;\r\n	padding: 1px 8px 0;\r\n	width: calc((100% - 60px) / 3);\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=text] {\r\n	[if bg_type_0 == \'color\']\r\n	-webkit-box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	-moz-box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	box-shadow: inset 4px 4px 4px 0px {{ adjust_brightness(popup.params.tpl.bg_color_0, -40) }};\r\n	background-color: {{ adjust_brightness(popup.params.tpl.bg_color_0, -20) }};\r\n	color: {{ adjust_brightness(popup.params.tpl.bg_color_0, -100) }};\r\n	[endif]\r\n	margin-right: 5px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit] {\r\n	color: #eee;\r\n	margin-right: 0;\r\n	cursor: pointer;\r\n	[if bg_type_1 == \'color\']\r\n	text-shadow: -1px -1px 1px {{ adjust_brightness(popup.params.tpl.bg_color_1, -80) }};\r\n	border: 1px solid {{ adjust_brightness(popup.params.tpl.bg_color_1, -40) }};\r\n	background: -moz-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* ff3.6+ */\r\n	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, {{ popup.params.tpl.bg_color_1 }}), color-stop(100%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }})); /* safari4+,chrome */\r\n	background:-webkit-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* safari5.1+,chrome10+ */\r\n	background: -o-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* opera 11.10+ */\r\n	background: -ms-radial-gradient(center, ellipse cover, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* ie10+ */\r\n	background:radial-gradient(ellipse at center, {{ popup.params.tpl.bg_color_1 }} 0%, {{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }} 100%); /* w3c */\r\n	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'{{ popup.params.tpl.bg_color_1 }}\', endColorstr=\'{{ adjust_brightness(popup.params.tpl.bg_color_1, -50) }}\',GradientType=1 ); /* ie6-9 */\r\n\r\n	-webkit-box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	-moz-box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	box-shadow: inset 0px 0px 2px 2px {{ adjust_brightness(popup.params.tpl.bg_color_1, 10) }};\r\n	[elseif bg_type_1 == \'img\']\r\n	background-image: url(\"[bg_img_1]\");\r\n	background-size: 100%;\r\n  	background-repeat: no-repeat;\r\n	[endif]\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:disabled {\r\n	color: transparent;\r\n  	cursor: progress;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell input[type=submit]:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell {\r\n	padding-top: 10px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSubscribeShell form {\r\n	margin: 0;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyLabel {\r\n	width: 100%;\r\n	text-align: center;\r\n	color: #555555;\r\n	text-shadow: 1px 1px 1px #ffffff;\r\n	font-size: 35px;\r\n	margin-top: 26px;\r\n}\r\n#ppsPopupShell_[ID] .ppsSuccessMsg {\r\n	color: #555555;\r\n  	border: 1px solid #555555;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClassyTxt {\r\n	font-size: 20px;\r\n	line-height: 160%;\r\n	color: rgb(85, 85, 85);\r\n	padding-top: 14px;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupVideo {\r\n	width: 100%;\r\n  	line-height: 0;\r\n  	padding: 0;\r\n  	margin: 0;\r\n}\r\n#ppsPopupShell_[ID] ul {\r\n	margin-top: 15px;\r\n	padding-left: 0;\r\n}\r\n#ppsPopupShell_[ID] ul li {\r\n	list-style: inside none disc;\r\n}\r\n#ppsPopupShell_[ID] .ppsPopupClose:hover {\r\n	opacity: 0.8;\r\n}\r\n#ppsPopupShell_[ID] .ppsFootNote {\r\n	color: #999;\r\n    font-size: 12px;\r\n    padding-top: 10px;\r\n    text-align: center;\r\n}\r\n/*SM*/\r\n#ppsPopupShell_[ID] .ppsSm {\r\n	padding: 10px 10px 0 10px;\r\n  	text-align: center;\r\n}\r\n#ppsPopupShell_1_435376 .ppsSm > div {\r\n	vertical-align: top !important;\r\n}\r\n#ppsPopupShell_[ID] .ppsSm .ppsSmBtn {\r\n	margin-right: 5px;\r\n}","","1","1","3","2015-01-03 17:00:43")');
		dbPps::query("ALTER TABLE `@__popup` AUTO_INCREMENT = 100;");
	}
}
