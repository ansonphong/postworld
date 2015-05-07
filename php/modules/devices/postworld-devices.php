<?php
/*
  __  __       _     _ _        ____       _            _   
 |  \/  | ___ | |__ (_) | ___  |  _ \  ___| |_ ___  ___| |_ 
 | |\/| |/ _ \| '_ \| | |/ _ \ | | | |/ _ \ __/ _ \/ __| __|
 | |  | | (_) | |_) | | |  __/ | |_| |  __/ ||  __/ (__| |_ 
 |_|  |_|\___/|_.__/|_|_|\___| |____/ \___|\__\___|\___|\__|                 
*/
/**
 * Thanks to the following repositories:
 *
 * For the Mobile Detect core class:
 * @link https://github.com/serbanghita/Mobile-Detect
 *
 * For the functions:
 * @link https://github.com/scottsweb/mobble/blob/master/mobble.php
 */

if( pw_module_enabled( 'devices' ) ){

	/**
	 * Includes the Mobile Detect Class
	 * @link http://mobiledetect.net/
	 */
	include_once POSTWORLD_PATH . '/lib/Mobile-Detect/Mobile_Detect.php';

	$useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "";

	$pw_mobile_detect = new Mobile_Detect();
	$pw_mobile_detect->setDetectionType( 'extended' );

	/***************************************************************
	* Function is_iphone
	* Detect the iPhone
	***************************************************************/

	if( !function_exists( 'is_iphone' ) ){
		function is_iphone() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isIphone();
		}
	}

	/***************************************************************
	* Function is_ipad
	* Detect the iPad
	***************************************************************/

	if( !function_exists( 'is_ipad' ) ){
		function is_ipad() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isIpad();
		}
	}

	/***************************************************************
	* Function is_ipod
	* Detect the iPod, most likely the iPod touch
	***************************************************************/

	if( !function_exists( 'is_ipod' ) ){
		function is_ipod() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'iPod' );
		}
	}

	/***************************************************************
	* Function is_android
	* Detect an android device.
	***************************************************************/

	if( !function_exists( 'is_android' ) ){
		function is_android() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isAndroidOS();
		}
	}

	/***************************************************************
	* Function is_blackberry
	* Detect a blackberry device
	***************************************************************/

	if( !function_exists( 'is_blackberry' ) ){
		function is_blackberry() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isBlackBerry();
		}
	}

	/***************************************************************
	* Function is_opera_mobile
	* Detect both Opera Mini and hopefully Opera Mobile as well
	***************************************************************/

	if( !function_exists( 'is_opera_mobile' ) ){
		function is_opera_mobile() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isOpera();
		}
	}

	/***************************************************************
	* Function is_palm - to be phased out as not using new detect library?
	* Detect a webOS device such as Pre and Pixi
	***************************************************************/

	if( !function_exists( 'is_palm' ) ){
		function is_palm() {
			_deprecated_function( 'is_palm', '1.2', 'is_webos' );
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'webOS' );
		}
	}

	/***************************************************************
	* Function is_webos
	* Detect a webOS device such as Pre and Pixi
	***************************************************************/

	if( !function_exists( 'is_webos' ) ){
		function is_webos() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'webOS' );
		}
	}

	/***************************************************************
	* Function is_symbian
	* Detect a symbian device, most likely a nokia smartphone
	***************************************************************/

	if( !function_exists( 'is_symbian' ) ){
		function is_symbian() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'Symbian' );
		}
	}

	/***************************************************************
	* Function is_windows_mobile
	* Detect a windows smartphone
	***************************************************************/

	if( !function_exists( 'is_windows_mobile' ) ){
		function is_windows_mobile() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'WindowsMobileOS' ) || $pw_mobile_detect->is( 'WindowsPhoneOS' );
		}
	}

	/***************************************************************
	* Function is_lg
	* Detect an LG phone
	***************************************************************/

	if( !function_exists( 'is_lg' ) ){
		function is_lg() {
			_deprecated_function( 'is_lg', '1.2' );
			global $useragent;
			return preg_match( '/LG/i', $useragent );
		}
	}

	/***************************************************************
	* Function is_motorola
	* Detect a Motorola phone
	***************************************************************/

	if( !function_exists( 'is_motorola' ) ){
		function is_motorola() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'Motorola' );
		}
	}

	/***************************************************************
	* Function is_nokia
	* Detect a Nokia phone
	***************************************************************/

	if( !function_exists( 'is_nokia' ) ){
		function is_nokia() {
			_deprecated_function( 'is_nokia', '1.2' );
			global $useragent;
			return preg_match( '/Series60/i', $useragent ) || preg_match( '/Symbian/i', $useragent ) || preg_match( '/Nokia/i', $useragent );
		}
	}

	/***************************************************************
	* Function is_samsung
	* Detect a Samsung phone
	***************************************************************/

	if( !function_exists( 'is_samsung' ) ){
		function is_samsung() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'Samsung' );
		}
	}

	/***************************************************************
	* Function is_samsung_galaxy_tab
	* Detect the Galaxy tab
	***************************************************************/

	if( !function_exists( 'is_samsung_galaxy_tab' ) ){
		function is_samsung_galaxy_tab() {
			_deprecated_function( 'is_samsung_galaxy_tab', '1.2', 'is_samsung_tablet' );
			return is_samsung_tablet();
		}
	}

	/***************************************************************
	* Function is_samsung_tablet
	* Detect the Galaxy tab
	***************************************************************/

	if( !function_exists( 'is_samsung_tablet' ) ){
		function is_samsung_tablet() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'SamsungTablet' );
		}
	}

	/***************************************************************
	* Function is_kindle
	* Detect an Amazon kindle
	***************************************************************/

	if( !function_exists( 'is_kindle' ) ){
		function is_kindle() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'Kindle' );
		}
	}

	/***************************************************************
	* Function is_sony_ericsson
	* Detect a Sony Ericsson
	***************************************************************/

	if( !function_exists( 'is_sony_ericsson' ) ){
		function is_sony_ericsson() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->is( 'Sony' );
		}
	}

	/***************************************************************
	* Function is_nintendo
	* Detect a Nintendo DS or DSi
	***************************************************************/

	if( !function_exists( 'is_nintendo' ) ){
		function is_nintendo() {
			global $useragent;
			return preg_match( '/Nintendo DSi/i', $useragent ) || preg_match( '/Nintendo DS/i', $useragent );
		}
	}


	/***************************************************************
	* Function is_smartphone
	* Grade of phone A = Smartphone - currently testing this
	***************************************************************/

	if( !function_exists( 'is_smartphone' ) ){
		function is_smartphone() {
			global $pw_mobile_detect;
			$grade = $pw_mobile_detect->mobileGrade();
			if ( $grade == 'A' || $grade == 'B' ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/***************************************************************
	* Function is_handheld
	* Wrapper function for detecting ANY handheld device
	***************************************************************/

	if( !function_exists( 'is_handheld' ) ){
		function is_handheld() {
			return is_mobile() || is_iphone() || is_ipad() || is_ipod() || is_android() || is_blackberry() || is_opera_mobile() || is_webos() || is_symbian() || is_windows_mobile() || is_motorola() || is_samsung() || is_samsung_tablet() || is_sony_ericsson() || is_nintendo();
		}
	}

	/***************************************************************
	* Function is_mobile
	* For detecting ANY mobile phone device
	***************************************************************/

	if( !function_exists( 'is_mobile' ) ){
		function is_mobile() {

			// Allow global override for mobile development
			if( defined( 'IS_MOBILE' ) && IS_MOBILE === true )
				return true;

			global $pw_mobile_detect;
			if ( is_tablet() ) return false;
			return $pw_mobile_detect->isMobile();
		}
	}

	/***************************************************************
	* Function is_ios
	* For detecting ANY iOS/Apple device
	***************************************************************/

	if( !function_exists( 'is_ios' ) ){
		function is_ios() {
			global $pw_mobile_detect;
			return $pw_mobile_detect->isiOS();
		}
	}

	/***************************************************************
	* Function is_tablet
	* For detecting tablet devices (needs work)
	***************************************************************/

	if( !function_exists( 'is_tablet' ) ){
		function is_tablet() {
			
			// Allow global override for tablet development
			if( defined( 'IS_TABLET' ) && IS_TABLET === true )
				return true;

			global $pw_mobile_detect;
			return $pw_mobile_detect->isTablet();
		}
	}

	/***************************************************************
	* Function is_desktop
	* For detecting desktop devices
	***************************************************************/

	if( !function_exists( 'is_desktop' ) ){
		function is_desktop() {
			
			// Allow global override for tablet development
			if( defined( 'IS_DESKTOP' ) && IS_DESKTOP === true )
				return true;

			return ( !is_tablet() && !is_mobile() );
			
		}
	}

}


/**
 * Returns an array of information about
 * the current user's device.
 *
 * @return A_Array An associative array of device info.
 */
function pw_device_meta(){
	
	if( !pw_module_enabled( 'devices' ) )
		return false;

	$device = array();
	$device['is_desktop'] = ( !is_mobile() && !is_tablet() );
	$device['is_mobile'] = is_mobile();
	$device['is_tablet'] = is_tablet();
	$device['is_smartphone'] = is_smartphone();
	$device['is_handheld'] = is_handheld();
	$device['is_iphone'] = is_iphone();
	$device['is_ipad'] = is_ipad();
	$device['is_ipod'] = is_ipod();
	$device['is_android'] = is_android();
	$device['is_blackberry'] = is_blackberry();
	$device['is_ios'] = is_ios();

	return $device;

}


?>