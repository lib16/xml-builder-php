<?php

namespace Lib16\XML\Tests;

require_once 'vendor/autoload.php';

use Lib16\Utils\Enums\CSS\Media;
use Lib16\XML\{Xml, Attributes};

const MULTILINE = "lorem\nipsum\ndolor\nsit";

class XmlTest extends XmlTestCase
{
	public function provider(): array
	{
		return [
			[Tml::c()->append(null), ''],
			[Tml::c()->append(''), ''],
			[Tml::c()->append(null, 'content'), 'content'],
			[Tml::c()->append('', 'content'), 'content'],
			[Tml::c()->append('e'), '<e/>'],
			[Tml::c()->append('e', ''), '<e/>'],
			[Tml::c()->append('e', 'content'), '<e>content</e>'],
			[Tml::c()->append('e'), '<e/>'],
			[Tml::c()->append('e', ...[]), '<e/>'],

			// append() htmlMode
			[Hml::c()->append(null), ''],
			[Hml::c()->append(''), ''],
			[Hml::c()->append(null, 'content'), 'content'],
			[Hml::c()->append('', 'content'), 'content'],
			[Hml::c()->append('e'), '<e>'],
			[Hml::c()->append('e', ''), '<e></e>'],
			[Hml::c()->append('e', 'content'), '<e>content</e>'],

			// append(): multiple content
			[
				Tml::c()
						->append('e', 'lorem', 'ipsum', 'dolor')
						->setAttributes((new Attributes())->set('foo', 'bar')),
				"<e foo=\"bar\">lorem</e>\n<e>ipsum</e>\n<e>dolor</e>"
			],

			// append(): indentation
			[Tml::c()->append('')->append('e'), '<e/>'],
			[
				function() {
					$xml = Tml::c();
					$xml->append(null, 'content 1');
					$xml->append('e', 'content 2');
					$xml->append(null, 'content 3');
					$xml->append('e', 'content 4');
					$xml->append('e', 'content 5');
					return $xml;
				},
				"content 1\n<e>content 2</e>\ncontent 3\n<e>content 4</e>\n<e>content 5</e>"
			],
			[
				function() {
					$xml = Tml::c();
					$xml->append('e')->append('e', '1.1');
					$xml->append('e')->append('e', '2.1')->getParent()->append('e', '2.2');
					return $xml;
				},
				"<e>\n\t<e>1.1</e>\n</e>\n<e>\n\t<e>2.1</e>\n\t<e>2.2</e>\n</e>"
			],
			[
				Tml::c()
						->append('e', '1')
						->append('e', '1.1')->getParent()
						->append('e', '1.2'),
				"<e>\n\t1\n\t<e>1.1</e>\n\t<e>1.2</e>\n</e>"
			],

			// appendText(), comment()
			[
				Tml::c()
						->append('e')->getParent()
						->comment('comment')
						->append('f', 'content 1')->appendText('content 2'),
				"<e/>\n<!-- comment -->\n<f>\n\tcontent 1\n\tcontent 2\n</f>"
			],
			[Tml::c()->append('e', '')->appendText('content'), "<e>\n\tcontent\n</e>"],
			[Tml::c('e')->appendText('one')->appendText('two'), "<e>\n\tone\n\ttwo\n</e>"],
			[Tml::c('e')->appendText('one', 'two'), "<e>\n\tone\n\ttwo\n</e>"],
			[
				function() {
					$xml = Tml::c('section')->append('div');
					$xml->append('p', MULTILINE);
					$xml->append('p')->appendText(MULTILINE);
					return $xml;
				},
				"<section>\n\t<div>\n" .
				"\t\t<p>lorem\n\t\tipsum\n\t\tdolor\n\t\tsit</p>\n" .
				"\t\t<p>\n\t\t\tlorem\n\t\t\tipsum\n\t\t\tdolor\n\t\t\tsit\n\t\t</p>\n" .
				"\t</div>\n</section>"
			],

			// inject()
			[
				Hml::createRoot('e')
						->setAttributes((new Attributes())->set('a', true))
						->inject(Nml::createRoot('f')->append('g')->getRoot()),
				"<e a>\n\t<f xmlns=\"" . Nml::XML_NAMESPACE . "\">\n\t\t<g/>\n\t</f>\n</e>"
			],
			[
				Hml::createRoot('e')
						->setAttributes((new Attributes())->set('a', true))
						->inject(Tml::c()->append('f')->getRoot()),
				"<e a>\n\t<f/>\n</e>"
			],

			// getChild()
			[
				function()  {
					$xml = Tml::c('p');
					$xml->append('c', 'one', 'two', 'three');
					return $xml->getChild(1)->getMarkup();
				},
				'<c>two</c>'
			],

			// countChildElements()
			[
				function() {
					$xml = Tml::c('p');
					$xml->append('c', 'one', 'two', 'three');
					return 'count: ' . $xml->countChildElements();
				},
				'count: 3'
			],

			// getParent()
			[
				Tml::c('e')->append('f')->append('g')->getParent()->getMarkup(),
				"<f>\n\t<g/>\n</f>"
			],
			[
				Tml::c('e')->append('f')->append('g')->append('h')->getParent(2)->getMarkup(),
				"<f>\n\t<g>\n\t\t<h/>\n\t</g>\n</f>"
			],
			[
				Tml::c('e')->append('f')->append('g')->append('h')->getParent(3)->getMarkup(),
				"<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>"
			],
			[
				Tml::c('e')->append('f')->append('g')->append('h')->getParent(10),
				null
			],
			[
				Tml::c('e')
						->append('f')
						->inject(Tml::c('g')->append('h')->getParent())
						->getParent(2)
						->getMarkup(),
				"<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>"
			],

			// getRoot()
			[
				Tml::c('e')->append('f')->append('g')->append('h')->getRoot()->getMarkup(),
				"<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>"
			],
			[
				function()  {
					$e1 = Tml::c('r1')->append('e1');
					$e2 = Hml::c('r2')->append('e2');
					$e1->inject($e2->getRoot());
					return $e2->getRoot()->getMarkup();
				},
				"<r1>\n\t<e1>\n\t\t<r2>\n\t\t\t<e2>\n\t\t</r2>\n\t</e1>\n</r1>"
			],

			// cdata()
			[Tml::c()->append('e')->cdata(), '<e/>'],
			[Tml::c()->append('e', 'content')->cdata(), '<e><![CDATA[content]]></e>'],
			[
				Tml::c()->append('e')->cdata()->append('f', 'content'),
				"<e>\n<![CDATA[\n\t<f>content</f>\n]]>\n</e>"
			],

			// setXmlns()
			[
				Tml::c('r')->setXmlns('http://example->com/foo', 'foo'),
				"<r xmlns:foo=\"http://example->com/foo\"/>"
			],
			[
				Tml::c('r')->setXmlns('http://example->com/foo', ''),
				"<r xmlns=\"http://example->com/foo\"/>"
			],
			[
				Tml::c('r')->setXmlns('http://example->com/foo'),
				"<r xmlns=\"http://example->com/foo\"/>"
			],
			[
				Tml::c('r')->setXmlns(),
				'<r/>'
			],
			[
				Nml::c()->append('r')->setXmlns(),
				'<r xmlns="' . Nml::XML_NAMESPACE . '"/>'
			],

			// XML Head, addProcessingInstruction()
			[Hml::createRoot('txml'), '<txml>'],
			[
				function() {
					$xml = Dnml::createRoot('txml');
					$xml->addProcessingInstruction('target1', 'content');
					$xml->addProcessingInstruction('target2')->attrib('attrib', 'value');
					return $xml;
				},
				self::XML_DECL . "\n<?target1 content ?>\n<?target2 attrib=\"value\" ?>\n" .
				Dml::DOCTYPE . "\n<txml xmlns=\"" . Nml::XML_NAMESPACE . "\"/>"
			],
			[Tml::c()->addProcessingInstruction('target', '')->getMarkup(), '<?target ?>'],
			[Xml::createRoot('e'), self::XML_DECL . "\n<e/>"],

			// setCharacterEncoding() setLineBreak(), setIndentation()
			[
				function() {
					Xml::setCharacterEncoding('ISO-8859-15');
					Xml::setLineBreak('');
					Xml::setIndentation('    ');
					$xml = Xml::createRoot('r')->append('e')->getRoot()->getMarkup();
					Xml::setCharacterEncoding('UTF-8');
					Xml::setLineBreak("\n");
					Xml::setIndentation("\t");
					return $xml;
				},
				'<?xml version="1.0" encoding="ISO-8859-15" ?><r><e/></r>'
			],
			[
				function() {
					Dml::setCharacterEncoding('ISO-8859-15');
					Dml::setLineBreak("\r");
					Dml::setIndentation('    ');
					$xml = Dml::createRoot('r')->append('e')->getRoot()->getMarkup();
					Xml::setCharacterEncoding('UTF-8');
					Xml::setLineBreak("\n");
					Xml::setIndentation("\t");
					return $xml;
				},
				"<?xml version=\"1.0\" encoding=\"ISO-8859-15\" ?>\r" . Dml::DOCTYPE .
				"\r<r>\r    <e/>\r</r>"
			],


			// disableTextIndentation()
			[
				Tml::c()
						->append('e')->appendText(MULTILINE)->getRoot()
						->append('f')->disableTextIndentation()->appendText(MULTILINE),
				"<e>\n\tlorem\n\tipsum\n\tdolor\n\tsit\n</e>\n" .
				"<f>\n\tlorem\nipsum\ndolor\nsit\n</f>"
			],
			[
				Tml::c('e')
						->append('f', MULTILINE)->getParent()
						->append('g', MULTILINE)->disableTextIndentation(),
				"<e>\n\t<f>lorem\n\tipsum\n\tdolor\n\tsit</f>" .
				"\n\t<g>lorem\nipsum\ndolor\nsit</g>\n</e>"
			],

			// attrib()
			[Tml::c('e')->attrib('a', 'foo'),  '<e a="foo"/>'],
			[Tml::c('e')->attrib('a', ''),     '<e a=""/>'],
			[Tml::c('e')->attrib('a', null),   '<e/>'],
			[Tml::c('e')->attrib('a'), '<e a="a"/>'],
			[Tml::c('e')->attrib('a', true), '<e a="a"/>'],
			[Tml::c('e')->attrib('a', false), '<e/>'],
			[Hml::c('e')->attrib('a', true), '<e a>'],
			[Hml::c('e')->attrib('a', false), '<e>'],
			[Tml::c('e')->attrib('a', Media::ALL()), '<e a="all"/>'],
			[Tml::c('e')->attrib('a', 1920), '<e a="1920"/>'],
			[Tml::c('e')->attrib('a', 1.23456), '<e a="1.23456"/>'],

			// boolAttrib()
			[
				Tml::c('e')->attrib('e', 'foo')
						->append('f')->attrib('f', 'bar')->getParent()
						->append('f')->attrib('f', 'baz')->getParent()
						->append('f')->attrib('f', 'foo')->getParent()
						->boolAttrib('bool', 'baz', 'f'),
				"<e e=\"foo\">" .
				"\n\t<f f=\"bar\"/>" .
				"\n\t<f f=\"baz\" bool=\"bool\"/>" .
				"\n\t<f f=\"foo\"/>" .
				"\n</e>"
			],
			[
				Tml::c('e')->attrib('e', 'foo')
						->append('f')->attrib('f', 'bar')->getParent()
						->append('f')->attrib('f', 'baz')->getParent()
						->append('f')->attrib('f', 'foo')->getParent()
						->boolAttrib('bool', ['zab', 'foo', 'bar'], 'f'),
				"<e e=\"foo\">" .
				"\n\t<f f=\"bar\" bool=\"bool\"/>" .
				"\n\t<f f=\"baz\"/>" .
				"\n\t<f f=\"foo\" bool=\"bool\"/>" .
				"\n</e>"
			],
			[
				Tml::c('e')->attrib('e', 'foo')
						->append('f')->attrib('f', 'bar')->getParent()
						->append('f')->attrib('f', 'baz')->getParent()
						->append('f')->attrib('f', 'foo')->getParent()
						->boolAttrib('bool', true),
				"<e e=\"foo\" bool=\"bool\">" .
				"\n\t<f f=\"bar\"/>" .
				"\n\t<f f=\"baz\"/>" .
				"\n\t<f f=\"foo\"/>" .
				"\n</e>"
			],

			// getContentDispositionHeaderfield(), getContentTypeHeaderfield()
			[
				Xml::getContentDispositionHeaderfield('test'),
				'Content-Disposition: attachment; filename="test.xml"'
			],
			[
				Fml::getContentDispositionHeaderfield('test.kml'),
				'Content-Disposition: attachment; filename="test.kml"'
			],
			[
				Xml::getContentTypeHeaderfield(),
				'Content-Type: application/xml; charset=UTF-8'
			]
		];
	}
}
