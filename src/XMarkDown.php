<?php
namespace XMarkDown;
class XMarkDown {
	private $markdown;
	private $root;
	private $document;

	public function __construct($markdown, \DomDocument $document) {
		//Replace windows line endings with unix line endings
		//And fix code blocks
		$this->markdown = str_replace(["\r\n", '```'], ["\n", "```\n"], trim($markdown));
		$this->document = $document;
		
		$this->root = $this->document->createElement('root');
		$this->document->appendChild($this->root);
	}

	public function parse($blockFormats) {
		
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