<?php

namespace Lib16\XML\Tests;

require_once 'vendor/autoload.php';

use Lib16\Utils\Enums\CSS\Media;
use Lib16\XML\{Xml, Attributes, Space};

const MULTILINE = "lorem\nipsum\ndolor\nsit";

class XmlTest extends XmlTestCase
{
	public function provider(): array
	{
		return [
			[Tml::cs()->append(null), ''],
			[Tml::cs()->append(''), ''],
			[Tml::cs()->append(null, 'content'), 'content'],
			[Tml::cs()->append('', 'content'), 'content'],
			[Tml::cs()->append('e'), '<e/>'],
			[Tml::cs()->append('e', ''), '<e/>'],
			[Tml::cs()->append('e', 'content'), '<e>content</e>'],
			[Tml::cs()->append('e'), '<e/>'],
			[Tml::cs()->append('e', ...[]), '<e/>'],

			// append() htmlMode
			[Hml::cs()->append(null), ''],
			[Hml::cs()->append(''), ''],
			[Hml::cs()->append(null, 'content'), 'content'],
			[Hml::cs()->append('', 'content'), 'content'],
			[Hml::cs()->append('e'), '<e>'],
			[Hml::cs()->append('e', ''), '<e></e>'],
			[Hml::cs()->append('e', 'content'), '<e>content</e>'],

			// append(): multiple content
			[
				Lml::cs()
						->append('e', 'lorem', 'ipsum', 'dolor')
						->setAttributes((new Attributes())->set('foo', 'bar')),
				'<e foo="bar">lorem</e><e>ipsum</e><e>dolor</e>'
			],

			// append(): indentation
			[Tml::cs()->append('')->append('e'), '<e/>'],
			[
				function() {
					$xml = Tml::cs();
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
					$xml = Tml::cs();
					$xml->append('e')->append('e', '1.1');
					$xml->append('e')->append('e', '2.1')->getParent()->append('e', '2.2');
					return $xml;
				},
				"<e>\n\t<e>1.1</e>\n</e>\n<e>\n\t<e>2.1</e>\n\t<e>2.2</e>\n</e>"
			],
			[
				Tml::cs()
						->append('e', '1')
						->append('e', '1.1')->getParent()
						->append('e', '1.2'),
				"<e>\n\t1\n\t<e>1.1</e>\n\t<e>1.2</e>\n</e>"
			],

			// appendText(), comment()
			[
				Tml::cs()
						->append('e')->getParent()
						->comment('comment')->getParent()
						->append('f', 'content 1')->appendText('content 2'),
				"<e/>\n<!-- comment -->\n<f>\n\tcontent 1\n\tcontent 2\n</f>"
			],
			[Tml::cs()->append('e', '')->appendText('content'), "<e>\n\tcontent\n</e>"],
			[Tml::cs('e')->appendText('one')->appendText('two'), "<e>\n\tone\n\ttwo\n</e>"],
			[
				function() {
					$xml = TML::cs('section')->append('div');
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
						->inject(Tml::cs()->append('f')->getRoot()),
				"<e a>\n\t<f/>\n</e>"
			],

			// getChild()
			[
				function()  {
					$xml = Tml::cs('p');
					$xml->append('c', 'one', 'two', 'three');
					return $xml->getChild(1)->getMarkup();
				},
				'<c>two</c>'
			],

			// countChildElements()
			[
				function()  {
					$xml = Tml::cs('p');
					$xml->append('c', 'one', 'two', 'three');
					return 'count: ' . $xml->countChildElements();
				},
				'count: 3'
			],

			// getParent()
			[
				Lml::cs('e')->append('f')->append('g')->getParent()->getMarkup(),
				'<f><g/></f>'
			],
			[
				Lml::cs('e')->append('f')->append('g')->append('h')->getParent(2)->getMarkup(),
				'<f><g><h/></g></f>'
			],
			[
				Lml::cs('e')->append('f')->append('g')->append('h')->getParent(3)->getMarkup(),
				'<e><f><g><h/></g></f></e>'
			],
			[
				Lml::cs('e')->append('f')->append('g')->append('h')->getParent(10),
				null
			],
			[
				Lml::cs('e')
						->append('f')
						->inject(Lml::cs('g')->append('h')->getParent())
						->getParent(2)
						->getMarkup(),
				'<e><f><g><h/></g></f></e>'
			],

			// getRoot()
			[
				Lml::cs('e')->append('f')->append('g')->append('h')->getRoot()->getMarkup(),
				'<e><f><g><h/></g></f></e>'
			],
			[
				function()  {
					$e1 = Tml::cs('r1')->disableLineBreak()->append('e1');
					$e2 = Hml::cs('r2')->append('e2');
					$e1->inject($e2->getRoot());
					return $e2->getRoot()->getMarkup();
				},
				'<r1><e1><r2><e2></r2></e1></r1>'
			],

			// cdata()
			[Tml::cs()->append('e')->cdata(), '<e/>'],
			[Tml::cs()->append('e', 'content')->cdata(), '<e><![CDATA[content]]></e>'],
			[
				Tml::cs()->append('e')->cdata()->append('f', 'content'),
				"<e>\n<![CDATA[\n\t<f>content</f>\n]]>\n</e>"
			],

			// setXmlns()
			[
				Tml::cs('r')->setXmlns('http://example->com/foo', 'foo'),
				"<r xmlns:foo=\"http://example->com/foo\"/>"
			],
			[
				Tml::cs('r')->setXmlns('http://example->com/foo', ''),
				"<r xmlns=\"http://example->com/foo\"/>"
			],
			[
				Tml::cs('r')->setXmlns('http://example->com/foo'),
				"<r xmlns=\"http://example->com/foo\"/>"
			],
			[
				Tml::cs('r')->setXmlns(),
				'<r/>'
			],
			[
				Nml::cs()->append('r')->setXmlns(),
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
			[Tml::cs()->addProcessingInstruction('target', '')->getMarkup(), '<?target ?>'],
			[Xml::createRoot('e'), self::XML_DECL . "\n<e/>"],

			// disableLineBreak(), disableIndentation(), disableTextIndentation()
			[
				Tml::cs('e')
						->disableLineBreak()->append('f')
						->disableLineBreak(false)->append('g'),
				"<e><f>\n\t<g/>\n</f></e>"
			],
			[
				Tml::cs('e')
						->disableIndentation()->append('f')
						->disableIndentation(false)->append('g'),
				"<e>\n<f>\n\t<g/>\n</f>\n</e>"
			],
			[
				Tml::cs('e')->disableLineBreak()->append('f')->append('g', 'a', 'b'),
				"<e><f><g>a</g><g>b</g></f></e>"
			],
			[
				Tml::cs()
						->append('e')->appendText(MULTILINE)->getRoot()
						->append('f')->disableTextIndentation()->appendText(MULTILINE),
				"<e>\n\tlorem\n\tipsum\n\tdolor\n\tsit\n</e>\n" .
				"<f>\n\tlorem\nipsum\ndolor\nsit\n</f>"
			],
			[
				Tml::cs('e')
						->append('f', MULTILINE)->getParent()
						->append('g', MULTILINE)->disableTextIndentation(),
				"<e>\n\t<f>lorem\n\tipsum\n\tdolor\n\tsit</f>" .
				"\n\t<g>lorem\nipsum\ndolor\nsit</g>\n</e>"
			],
			[ // combined
				Tml::cs('e')
						->disableTextIndentation()
						->disableIndentation()
						->append('f', MULTILINE),
				"<e>\n<f>lorem\nipsum\ndolor\nsit</f>\n</e>"
			],

			// attrib()
			[Tml::cs('e')->attrib('a', 'foo'),  '<e a="foo"/>'],
			[Tml::cs('e')->attrib('a', ''),     '<e a=""/>'],
			[Tml::cs('e')->attrib('a', null),   '<e/>'],
			[Tml::cs('e')->attrib('a'), '<e a="a"/>'],
			[Tml::cs('e')->attrib('a', true), '<e a="a"/>'],
			[Tml::cs('e')->attrib('a', false), '<e/>'],
			[Hml::cs('e')->attrib('a', true), '<e a>'],
			[Hml::cs('e')->attrib('a', false), '<e>'],
			[Tml::cs('e')->attrib('a', Media::ALL()), '<e a="all"/>'],
			[Tml::cs('e')->attrib('a', 1920), '<e a="1920"/>'],
			[Tml::cs('e')->attrib('a', 1.23456), '<e a="1.23456"/>'],

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
