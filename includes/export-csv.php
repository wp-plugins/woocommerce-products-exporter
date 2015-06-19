<?php
function woo_cd_load_phpexcel_sed_csv_writer() {

	if( class_exists( 'PHPExcel_IOFactory' ) ) {
		PHPExcel_IOFactory::addSearchLocation( 'IWriter', WOO_CD_PATH . 'includes/export-csv.php', 'PHPExcel_Writer_SED_CSV_{0}' );
	} else {
		return false;
	}

	/** My custom writer */
	class PHPExcel_Writer_SED_CSV extends PHPExcel_Writer_CSV {
	
		/**
		 * PHPExcel object
		 *
		 * @var PHPExcel
		 */
		private $_phpExcel;
	
		/**
		 * Delimiter
		 *
		 * @var string
		 */
		private $_delimiter	= ',';
	
		/**
		 * Enclosure
		 *
		 * @var string
		 */
		private $_enclosure	= '"';
	
		/**
		 * Line ending
		 *
		 * @var string
		 */
		private $_lineEnding	= PHP_EOL;
	
		/**
		 * Sheet index to write
		 *
		 * @var int
		 */
		private $_sheetIndex	= 0;
	
		/**
		 * Whether to write a BOM (for UTF8).
		 *
		 * @var boolean
		 */
		private $_useBOM = false;
	
		/**
		 * Whether to write a fully Excel compatible CSV file.
		 *
		 * @var boolean
		 */
		private $_excelCompatibility = false;
	
		/**
		 * Create a new PHPExcel_Writer_CSV
		 *
		 * @param	PHPExcel	$phpExcel	PHPExcel object
		 */
		public function __construct( PHPExcel $phpExcel ) {
			$this->_phpExcel	= $phpExcel;
		}

		/**
		 * Set delimiter
		 *
		 * @param	string	$pValue		Delimiter, defaults to ,
		 * @return PHPExcel_Writer_CSV
		 */
		public function setDelimiter($pValue = ',') {
			$this->_delimiter = $pValue;
			return $this;
		}

		public function save($pFilename = null) {
			// Fetch sheet
			$sheet = $this->_phpExcel->getSheet($this->_sheetIndex);
	
			$saveDebugLog = PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->getWriteDebugLog();
			PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog(FALSE);
			$saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
			PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);
	
			// Open file
			$fileHandle = fopen($pFilename, 'wb+');
			if ($fileHandle === false) {
				throw new PHPExcel_Writer_Exception("Could not open file $pFilename for writing.");
			}
	
			if ($this->_excelCompatibility) {
				fwrite($fileHandle, "\xEF\xBB\xBF");	//	Enforce UTF-8 BOM Header
				$this->setEnclosure('"');				//	Set enclosure to "
				$this->setDelimiter(";");			    //	Set delimiter to a semi-colon
	            $this->setLineEnding("\r\n");
				fwrite($fileHandle, 'sep=' . $this->getDelimiter() . $this->_lineEnding);
			} elseif ($this->_useBOM) {
				// Write the UTF-8 BOM code if required
				fwrite($fileHandle, "\xEF\xBB\xBF");
			}
	
			//	Identify the range that we need to extract from the worksheet
			$maxCol = $sheet->getHighestDataColumn();
			$maxRow = $sheet->getHighestDataRow();
	
			// Write rows to file
			for($row = 1; $row <= $maxRow; ++$row) {
				// Convert the row to an array...
				$cellsArray = $sheet->rangeToArray('A'.$row.':'.$maxCol.$row,'', $this->_preCalculateFormulas);
				// ... and write to the file
				$this->_writeLine($fileHandle, $cellsArray[0]);
			}
	
			// Close file
			fclose($fileHandle);
	
			PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
			PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog($saveDebugLog);
		}

		public function _writeLine( $pFileHandle = null, $pValues = null ) {

			global $export;

			if (is_array($pValues)) {
				// No leading delimiter
				$writeDelimiter = false;
	
				// Build the line
				$line = '';
	
				foreach ($pValues as $element) {
					// Escape enclosures
					$element = str_replace($this->_enclosure, $this->_enclosure . $this->_enclosure, $element);
	
					// Add delimiter
					if ($writeDelimiter) {
						$line .= $this->_delimiter;
					} else {
						$writeDelimiter = true;
					}
	
					// Add enclosed string
					if( $export->escape_formatting == 'all' ) {
						$line .= $this->_enclosure . $element . $this->_enclosure;
					} else {
						$enclosure = ( ( !substr_count( $element, $this->_delimiter ) && !woo_ce_detect_value_string( $element ) ) ? '' : $this->_enclosure );
						$line .= $enclosure . $element . $enclosure;
					}
				}
	
				// Add line ending
				$line .= $this->_lineEnding;
	
				// Write to file
	            fwrite($pFileHandle, $line);
			} else {
				throw new PHPExcel_Writer_Exception("Invalid data row passed to CSV writer.");
			}
	
		}

	}

}
?>