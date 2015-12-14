<?php
namespace XMarkDown;
class ListMD implements Block, NeedsClosing {

	const TYPE_OL = 'ol';
	const TYPE_UL = 'ul';

	private $document;
	private $items = [];
	private $open = false;
	private $blocks = 0;
	private $type;

	public function __construct(\DomDocument $document, $format, $type) {
		$this->document = $document;
		$this->format = $format;
		$this->type = $type;
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
				$this->items[count($this->items)-1]['nested'] .= ' ' . $block;
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
			else if ($this->isNestedItem($line)) $this->items[count($this->items)-1]['nested'] .= ' ' . trim($line);
			else $this->items[count($this->items)-1]['maintext'] .= ' ' . $line;
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
		if ($this->type === self::TYPE_UL) return $this->isListItemUL($str);
		else if ($this->type === self::TYPE_OL) return $this->isListItemOL($str);
	}

	private function isListItemUL($str) {
		$markers = ['+', '-', '*'];
		$spaces = [' ', "\t"];

		if (strlen(trim($str)) > 0 && in_array(trim($str)[0], $markers) && in_array(trim($str)[1], $spaces)) {
			return trim($str, "\t\n " . implode($markers));
		} 
		else return false;
	}

	private function isListItemOL($str) {
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

		$ol = $this->document->createElement($this->type);
		foreach ($this->items as $item) {
			
			$li = $this->document->createElement('li');
			
			if ($this->blocks > 1) {
				$li->appendChild($this->document->createElement('p', $item['maintext']));
			}
			else $li->appendChild($this->document->createTextNode($item['maintext']));

			$this->appendNested($item, $li);
			$ol->appendChild($li);
		}

		$this->document->documentElement->appendChild($ol);
		$this->items = [];
		$this->open = false;
		$this->blocks = 0;
	}

	private function appendNested($item, $li) {
		if ($item['nested'] !== '') {
			$format = $this->format;
			$parser = new $format($item['nested']);
			$nested = $parser->parse();

			foreach ($nested->documentElement->childNodes as $child) {
				$li->appendChild($this->document->importNode($child, true));
			}
		}
	}

}