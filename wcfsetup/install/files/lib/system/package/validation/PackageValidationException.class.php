<?php
namespace wcf\system\package\validation;
use wcf\system\exception\SystemException;
use wcf\system\package\PackageArchive;
use wcf\system\WCF;

/**
 * Represents exceptions occured during validation of a package archive. This exception
 * does not cause the details to be logged.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2014 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.package.validation
 * @category	Community Framework
 */
class PackageValidationException extends SystemException {
	/**
	 * list of additional details for each subtype
	 * @var	array<string>
	 */
	protected $details = array();
	
	/**
	 * missing archive, expects the detail 'archive' and optionally 'targetArchive' (extracting archive from the archive)
	 * @var	integer
	 */
	const FILE_NOT_FOUND = 1;
	
	/**
	 * missing package.xml, expects the detail 'archive'
	 * @var	integer
	 */
	const MISSING_PACKAGE_XML = 2;
	
	/**
	 * package name violates WCF's schema, expects the detail 'packageName'
	 * @var	integer
	 */
	const INVALID_PACKAGE_NAME = 3;
	
	/**
	 * package version violates WCF's schema, expects the detail 'packageVersion'
	 * @var	integer
	 */
	const INVALID_PACKAGE_VERSION = 4;
	
	/**
	 * package contains no install instructions and an update is not possible, expects the detail 'packageName'
	 * @var	integer
	 */
	const NO_INSTALL_PATH = 5;
	
	/**
	 * package is already installed and cannot be updated using current archive, expects the details 'packageName', 'packageVersion' and 'deliveredPackageVersion'
	 * @var	integer
	 */
	const NO_UPDATE_PATH = 6;
	
	/**
	 * packages which exclude the current package, expects the detail 'packages' (list of \wcf\data\package\Package)
	 * @var	integer
	 */
	const EXCLUDING_PACKAGES = 7;
	
	/**
	 * packages which are excluded by current package, expects the detail 'packages' (list of \wcf\data\package\Package)
	 * @var	integer
	 */
	const EXCLUDED_PACKAGES = 8;
	
	/**
	 * Creates a new PackageArchiveValidationException.
	 * 
	 * @param	integer		$code
	 * @param	array<string>	$details
	 */
	public function __construct($code, array $details = array()) {
		parent::__construct($this->getLegacyMessage(), $code);
		
		$this->details = $details;
	}
	
	/**
	 * Returns exception details.
	 * 
	 * @return	array<string>
	 */
	public function getDetails() {
		return $this->details;
	}
	
	/**
	 * Returns the readable error message.
	 * 
	 * @return	string
	 */
	public function getErrorMessage() {
		return WCF::getLanguage()->getDynamicVariable('wcf.package.validation.errorCode.' . $this->getCode(), $this->getDetails());
	}
	
	/**
	 * Returns legacy error messages to mimic WCF 2.0.x PackageArchive's exceptions.
	 * 
	 * @return	string
	 */
	protected function getLegacyMessage() {
		switch ($this->getCode()) {
			case self::FILE_NOT_FOUND:
				if (isset($this->details['targetArchive'])) {
					return "tar archive '".$this->details['targetArchive']."' not found in '".$this->details['archive']."'.";
				}
				
				return "unable to find package file '".$this->details['archive']."'";
			break;
			
			case self::MISSING_PACKAGE_XML:
				return "package information file '".PackageArchive::INFO_FILE."' not found in '".$this->details['archive']."'";
			break;
			
			case self::INVALID_PACKAGE_NAME:
				return "'".$this->details['packageName']."' is not a valid package name.";
			break;
			
			case self::INVALID_PACKAGE_VERSION:
				return "package version '".$this->details['packageVersion']."' is invalid";
			break;
			
			default:
				return 'Using getMessage() is discouraged, please use getErrorMessage() instead';
			break;
		}
	}
	
	/**
	 * @see	\wcf\system\exception\LoggedException::logError()
	 */
	protected function logError() {
		// do not log errors
	}
}
