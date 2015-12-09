<?php
namespace XMarkDown;
class HeadingStyle2 implements Block {
	private $document;

	public function __construct(\DomDocument $document) {
		$this->document = $document;
	}


	public function parse($block) {
		$pos = 0;
		while ($block[0][$pos] === '#') {
			$pos++;
		}

		if ($pos == 0) return false;
		else {
			$this->document->documentElement->appendChild($this->document->createElement('h' . $pos+1, substr($block[0], $pos+1)));
			return Block::MATCH;
		}
	}
}