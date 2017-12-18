<?php
// Creator: Mats J Svensson, CAnMove

class XMLFile {

	protected $dom;
	protected $workbook;
	protected $styles;
	protected $style;
	protected $worksheet;
	protected $xmltable;
	protected $xmlrow;
	protected $xmlcell;
	protected $xmldata;
	protected $nullvalue;
	protected $cellindex;

	function __construct($filename, $content) {
		// Prepare XML document
		//header("Pragma: public");
		//header("Expires: 0");
		//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Cache-Control: private", false);
		//header("Content-Type: application/vnd.ms-excel"); 
		//header("Content-Disposition: attachment; filename=\"".$filename."\";");

		// construct skeleton
		$this->dom = new DOMDocument('1.0', 'utf-8');
		$this->dom->formatOutput = $this->dom->preserveSpaces = true; // optional
		$this->dom->appendChild(new DOMProcessingInstruction('mso-application', 'progid="Excel.Sheet"'));

		$this->workbook = $this->dom->appendChild(new DOMElement('Workbook'));
		$this->workbook->setAttribute('xmlns','urn:schemas-microsoft-com:office:spreadsheet');
		$this->workbook->setAttribute('xmlns:o','urn:schemas-microsoft-com:office:office');
		$this->workbook->setAttribute('xmlns:x','urn:schemas-microsoft-com:office:excel');
		$this->workbook->setAttribute('xmlns:ss','urn:schemas-microsoft-com:office:spreadsheet');
		$this->workbook->setAttribute('xmlns:html','http://www.w3.org/TR/REC-html40');

		$this->styles = $this->workbook->appendChild(new DOMElement('Styles'));
		$this->style = $this->styles->appendChild(new DOMElement('Style'));
		$this->style->setAttribute('ss:ID','Default');
		$this->worksheet = $this->workbook->appendChild(new DOMElement('Worksheet'));
		$this->worksheet->setAttribute('ss:Name',$content);
		$this->xmltable = $this->worksheet->appendChild(new DOMElement('Table'));

	}

	function add_row() {
		$this->xmlrow = $this->xmltable->appendChild(new DOMElement('Row'));
		$this->nullvalue = false;
		$this->cellindex = 0;
	}

	function add_cell($cell_value, $cell_type) {
		$this->cellindex++;
		if ($cell_value==NULL) {
			$this->nullvalue = true;
		} else {
			$this->xmlcell = $this->xmlrow->appendChild(new DOMElement('Cell'));
			if ($this->nullvalue) {
				$this->xmlcell->setAttribute('ss:Index', $this->cellindex);
				$this->nullvalue = false;
			}
			$this->xmldata = $this->xmlcell->appendChild(new DOMElement('Data', $cell_value));
			$this->xmldata->setAttribute('ss:Type', $cell_type);
		}
	}

	function savefile($filename) {
		echo $this->dom->save($filename);
	}
}

?>
