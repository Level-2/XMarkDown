<?php
class MarkDownTest extends PHPUnit_Framework_TestCase {

	private function stripTabs($str) {
		return trim(str_replace(["\t", "\n", "\r"], '', $str));
	}



	public function testBasic() {

		$markdown = '
Heading
=======

Paragraph one
Paragraph one

Paragraph two
Paragraph two
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<h1>Heading</h1>
			<p>Paragraph oneParagraph one</p>
			<p>Paragraph twoParagraph two</p>

		</root>'), $this->stripTabs($xml));
	}


	public function testH2Basic() {

		$markdown = '
Heading
-------
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<h2>Heading</h2>
		</root>'), $this->stripTabs($xml));
	}


	public function testBold1() {

		$markdown = '
This is **bold** in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>This is <strong>bold</strong> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}


	public function testBold2() {

		$markdown = '
This is __bold__ in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>This is <strong>bold</strong> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}


	public function testItalic1() {

		$markdown = '
This is *italic* in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>This is <em>italic</em> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}

	public function testItalic2() {

		$markdown = '
This is _italic_ in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>This is <em>italic</em> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}


	public function testCode() {

		$markdown = '
This is `code` in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>This is <code>code</code> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}

	public function testNestedBoldItalic() {

		$markdown = '
This is ** bold and *italic* ** in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root><p>This is <strong> bold and <em>italic</em> </strong> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}

	public function testNestedCode() {

		$markdown = '
This is `code but **this is not bold** code` in a paragraph
		';


		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root><p>This is <code>code but **this is not bold** code</code> in a paragraph</p>
		</root>'), $this->stripTabs($xml));
	}

	public function testEmpahasisInHeading() {
		$markdown = '
Heading **with** bold
---------------------
		';

		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();


		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<h2>Heading <strong>with</strong> bold</h2>
		</root>'), $this->stripTabs($xml));

	}


	public function testBasicCodeBlock() {
				$markdown = '
paragraph 1

```
function code() {					
}
```

paragraph 2
';
	
		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root><p>paragraph 1</p>
			<pre>function code() {}</pre><p>paragraph 2</p>
		</root>'), $this->stripTabs($xml));

	}


		public function testCodeWithSpacing() {
				$markdown = '
paragraph 1

```



function code() {	


$x = 1;

}



```

paragraph 2
';
	
		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root><p>paragraph 1</p>
			<pre>function code() {$x = 1;}</pre><p>paragraph 2</p>
		</root>'), $this->stripTabs($xml));

	}

	public function testCodeAlone() {
				$markdown = '

```
function code() {	

$x = 1;

}
```
';
	
		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<pre>function code() {$x = 1;}</pre>
		</root>'), $this->stripTabs($xml));

	}


	public function testList() {
		$markdown = '

paragraph

1. One
2. Two
3. Three

paragraph
		';

		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>paragraph</p>
			<ol>
				<li>One</li>
				<li>Two</li>
				<li>Three</li>
			</ol>
			<p>paragraph</p>
		</root>'), $this->stripTabs($xml));
	}


	public function testListWithParagraphs() {
		$markdown = '

paragraph

1. One

2. Two

3. Three

paragraph
		';

		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>paragraph</p>
			<ol>
				<li><p>One</p></li>
				<li><p>Two</p></li>
				<li><p>Three</p></li>
			</ol>
			<p>paragraph</p>
		</root>'), $this->stripTabs($xml));
	}

	public function testNestedLists() {
			$markdown = '

paragraph

1. One
	1. One A
	2. One B 
	3. One C 

2. Two

3. Three

paragraph
		';

		$XMarkDown = new \XMarkDown\XMarkDown($markdown);
		$xml = $XMarkDown->parse()->saveXML();

		$this->assertEquals($this->stripTabs('
			<?xml version="1.0"?>
			<root>
			<p>paragraph</p>
			<ol>
				<li><p>One</p>
				<ol>
					<li>One A</li>
					<li>One B</li>
					<li>One C</li>
				</ol>
				</li>
				<li><p>Two</p></li>
				<li><p>Three</p></li>
			</ol>
			<p>paragraph</p>
		</root>'), $this->stripTabs($xml));	
	}
}