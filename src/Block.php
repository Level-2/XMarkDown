<?php
namespace XMarkDown;
interface Block {
	const NOMATCH = 1;
	const INCOMPLETE = 2;
	const MATCH = 3;

	public function parse($block);
}