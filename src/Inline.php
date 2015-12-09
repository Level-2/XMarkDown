<?php
namespace XMarkDown;
class Inline {
	private $document;
	private $emphasis = [
					'`' => 'code',
					'**' => 'strong',
					 '__' => 'strong',
					 '*' => 'em',
					 '_' => 'em'
					 
	];

	public function __construct(\DomDocument $document) {
		$this->document = $document;
	}

	public function inject(\DomElement $parent, $str) {
		$lastPos = 0;

		for ($i = 0; $i < strlen($str); $i++) {
			foreach ($this->emphasis as $char => $tag) {
				if (($pos = $this->processEmpahasis($parent, $str, $i, $char, $lastPos, $tag)) !== false) {
					$lastPos = $pos;
					$i  = $pos;
				}
			}
		}
		$parent->appendChild($this->document->createTextNode(substr($str, $lastPos)));
	}

	private function injectIfNotCode($chr, \DomElement $element, $string) {
		if ($chr === '`') $element->appendChild($this->document->createTextNode($string));
		else $this->inject($element, $string);
	}

	private function processEmpahasis($parent, $str, $i, $char, $pos, $tag) {
		if (substr($str, $i, strlen($char)) === $char) {
			$before = substr($str, $pos, $i-$pos);
			$parent->appendChild($this->document->createTextNode($before));
			
			for ($j = $i+strlen($char)+1; $j < strlen($str); $j++) {
				if (substr($str, $j, strlen($char)) === $char) {
					$element = $this->document->createElement($tag);

					$this->injectIfNotCode($char, $element, substr($str, $i+strlen($char), $j-$i-strlen($char)));
					$parent->appendChild($element);
					return  $j+strlen($char);
				}
			}
		}
		else return false;
	}
}