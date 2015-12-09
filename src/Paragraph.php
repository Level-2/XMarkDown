<?php
namespace XMarkDown;
class Paragraph implements Block {
	private $root;
	private $document;

	public function __construct(\DomDocument $document, \DomElement $root) {
		$this->root = $root;
		$this->document = $document;
	}

	public function parse($block) {
		$p = $this->document->createElement('p');
		$inline = new Inline($this->document);
		$inline->inject($p, $block);
		$this->root->appendChild($p);
		return Block::MATCH;
	}
}