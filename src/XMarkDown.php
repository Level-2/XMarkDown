<?php
namespace XMarkDown;
class XMarkDown {
	private $markdown;
	private $root;
	private $document;

	public function __construct($markdown) {
		//Replace windows line endings with unix line endings
		//And fix code blocks
		$this->markdown = str_replace(["\r\n", '```'], ["\n", "```\n"], trim($markdown));

		$this->document = new \DomDocument();
		$this->root = $this->document->createElement('root');

		$this->document->appendChild($this->root);
	}

	public function parse() {
		//Register the block formats
		//TODO: Allow these to be edited by DI
		$blockFormats = [
			new Heading('=', 'h1', $this->document, $this->root),
			new Heading('-', 'h2', $this->document, $this->root),
			new HeadingStyle2($this->document, $this->root),
			new Code($this->document, $this->root),
			new ListMD($this->document, $this->root),
			new Paragraph($this->document, $this->root)
		];

		$blocks = explode("\n\n", $this->markdown);

		for ($i = 0; $i < count($blocks); $i++) {
			foreach ($blockFormats as $format) {
				while (($result = $this->processBlock($format, $blocks, $i))  === Block::INCOMPLETE) $i++;
				if ($result == Block::MATCH) break;
			}
		}

		$this->finalize($blockFormats);
		return $this->document;
	}

	private function finalize($formats) {
		//Some elements need closing if they're at the end of the file because they may be waiting for further input
		foreach ($formats as $format) {
			if ($format instanceof NeedsClosing) $format->close();
		}
	}

	private function processBlock($format, $blocks, $index) {
		if (isset($blocks[$index])) {
			return $format->parse($blocks[$index]);
		}
		else return false;
	}

}