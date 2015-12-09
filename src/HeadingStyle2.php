<?php
namespace XMarkDown;
class HeadingStyle2 implements Block {
	private $root;
	private $document;

	public function __construct(\DomDocument $document, \DomElement $root) {
		$this->root = $root;
		$this->document = $document;
	}


	public function parse($block) {
		$pos = 0;
		while ($block[0][$pos] === '#') {
			$pos++;
		}

		if ($pos == 0) return false;
		else {
			$this->root->appendChild($this->document->createElement('h' . $pos+1, substr($block[0], $pos+1)));
			return Block::MATCH;
		}
	}
}