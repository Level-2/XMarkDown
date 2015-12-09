<?php
namespace XMarkDown;
class Heading implements Block {
	private $tag;
	private $char;
	private $document;

	public function __construct($char, $tag, \DomDocument $document) {
		$this->char = $char;
		$this->tag = $tag;
		$this->document = $document;
	}

	public function parse($block) {
		$lines = explode("\n", $block);
		if (count($lines) == 2 && $lines[1][0] == $this->char) {
			$element = $this->document->createElement($this->tag);
			$inline = new Inline($this->document);
			$inline->inject($element, $lines[0]);
			$this->document->documentElement->appendChild($element);
			return Block::MATCH;
		}
	}
}