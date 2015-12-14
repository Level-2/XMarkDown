<?php
namespace XMarkDown;
class Paragraph implements Block {
	private $document;

	public function __construct(\DomDocument $document) {
		$this->document = $document;
	}

	public function parse($block) {
		$p = $this->document->createElement('p');
		$inline = new Inline($this->document);
		$inline->inject($p, str_replace("\n", ' ', $block));
		$this->document->documentElement->appendChild($p);
		return Block::MATCH;
	}
}