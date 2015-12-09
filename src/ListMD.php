<?php
namespace XMarkDown;
class ListMD implements Block, NeedsClosing {
	private $document;
	private $root;
	private $items = [];
	private $open = false;
	private $blocks = 0;

	public function __construct(\DomDocument $document, \DomElement $root) {
		$this->document = $document;
		$this->root = $root;
	}

	public function parse($block) {
		$lines = explode("\n", $block);


		if ($this->isListItem($lines[0])) { 
			$this->open = true;
			$this->blocks++;

			$this->processListBlock($lines);
			return Block::INCOMPLETE;
		}
		else if ($this->open) {
			if (strpos($block, "\t") === 0) {
				$this->items['nested'] .= "\n\n" . $block;
				$this->blocks++;
				return Block::INCOMPLETE;
			}
			else {
				$this->close();
				return Block::NOMATCH;	
			}
			
		} 
		else return Block::NOMATCH;
	}

	private function processListBlock($lines) {
		foreach ($lines as $line) {
			if ($text = $this->isListItem($line)) {
				$this->items[] = ['maintext' => trim($text), 'nested' => ''];
			}
			else if ($this->isNestedItem($line)) $this->items[count($this->items)-1]['nested'] .= "\n" . trim($line);
			else $this->items[count($this->items)-1]['maintext'] .= ' ' + $line;
		}
	}

	private function isNestedItem($str) {
		if (strpos($str, "\t") === 0) {
			$this->blocks++;
			return true;
		}
		else return false;
	}

	private function isListItem($str) {
		$i = 0;
		while ($i < 0 && $str[$i] == ' ') {
			$str = substr($str, 1);
		}

		$dot = strpos($str, '.');
		if ($dot === false) return false;

		$start = substr($str, 0, $dot);
		if (is_numeric($str[0]) && is_numeric($start)) return substr($str, $dot+1);
		else return false;
	}

	public function close() {
		if (empty($this->items)) return;

		$ol = $this->document->createElement('ol');
		foreach ($this->items as $item) {
			
			$li = $this->document->createElement('li');
			
			if ($this->blocks > 1) {
				$li->appendChild($this->document->createElement('p', $item['maintext']));
			}
			else $li->appendChild($this->document->createTextNode($item['maintext']));

			$this->appendNested($item, $li);
			$ol->appendChild($li);
		}

		$this->root->appendChild($ol);
		$this->items = [];
		$this->open = false;
		$this->blocks = 0;
	}

	private function appendNested($item, $li) {
		if ($item['nested'] !== '') {
			$parser = new XMarkDown($item['nested']);
			$nested = $parser->parse();

			foreach ($nested->documentElement->childNodes as $child) {
				$li->appendChild($this->document->importNode($child, true));
			}
		}
	}

}