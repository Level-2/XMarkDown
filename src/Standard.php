<?php
namespace XMarkDown;
class Standard {
	private $markDown;

	public function __construct($markDown) {
		$this->document = new \DomDocument();
		$this->markDown = new XMarkDown($markDown, $this->document);
	}

	public function parse() {
		return $this->markDown->parse([
			new Heading('=', 'h1', $this->document),
			new Heading('-', 'h2', $this->document),
			new HeadingStyle2($this->document),
			new Code($this->document),			
			new ListMD($this->document, self::class, ListMD::TYPE_OL),
			new ListMD($this->document, self::class, ListMD::TYPE_UL),
			new Paragraph($this->document)
		]);
	}

}