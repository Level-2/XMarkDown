<?php
namespace XMarkDown;
class Code implements Block {
	private $document;
	private $codeStr = '';
	private $open = false;

	public function __construct(\DomDocument $document) {
		$this->document = $document;
	}

	public function parse($block) {
		$block = trim($block);
		$wasOpen = $this->open;
		$code = false;

		$result = Block::NOMATCH;

		$lines = explode("\n", trim($block));

		$code = $this->checkCodeTag($lines, 0);
		$code = $code || $this->checkCodeTag($lines, count($lines)-1);

		$this->writeCode($code, $lines);

		if (($wasOpen && !$this->open) || ($code && !$this->open)) {
			$this->complete();
			return Block::MATCH;
		}
		else return $this->open ? Block::INCOMPLETE : Block::NOMATCH;

	}

	private function checkCodeTag(&$lines, $index) {
		if (isset($lines[$index]) && strpos(trim($lines[$index]), '```') === 0) {
			//If it#s a code tag, remove it from the $lines array and return true
			unset($lines[$index]);

			//A new code tag has been enountered, it's either a closing or opening tag depending on the current state so switch it
			$this->open = !$this->open;
			return true;
		}
		else return false;
	}

	private function complete() {
		//When complete, build a <pre> tag with the current code string
		$this->document->documentElement->appendChild($this->document->createElement('pre', $this->codeStr));
		//Clear the code string and mark the tag as closed
		$this->codeStr = '';
		$this->open = false;
	}

	private function writeCode($wasCodeTag, $lines) {
		//Is the block inside a ``` and ```? If so write the block to codeStr
		if ($this->open || $wasCodeTag) {
			$this->codeStr .= implode("\n", $lines);
		}
	}
}
