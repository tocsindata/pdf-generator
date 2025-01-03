# pdf-generator

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![PHP Composer](https://github.com/famoser/pdf-generator/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/pdf-generator/actions/workflows/php.yml)

## About

Generates pdf files without any dependencies. Includes a layout engine to improve handling of flowing content (e.g. text
spanning more than one page).

```bash
composer require famoser/pdf-generator
```

This is still under active development ([contributions welcome!](./CONTRIBUTE.md)), and the public API is subject to
change. If you are looking for a more mature project, see https://github.com/tecnickcom/TCPDF.

## Getting started

Using the printer:

```php
// places "Hello world" at coordinate (15/60) coordinates
$document = new LinearDocument();
$bodyText = new TextStyle(Font::createFromDefault());
$printer = $document->createPrinter(0, 15, 60);
$printer->printText("Hello world", $bodyText);
file_put_contents('example.pdf', $document->save());
```

Using the layout engine:

```php
// places a 20x40 rectangle, followed by "Hello world.".
// placement is decided by Flow. 
$flow = new Flow();

$rectangle = new Rectangle(new DrawingStyle());
$rectangleContent = new ContentBlock($rectangle);
$rectangleContent->setWidth(20);
$rectangleContent->setHeight(40);
$flow->addContent($rectangleContent);

$paragraph = new Paragraph();
$paragraph->add($normalText, "Hello ");
$paragraph->add($normalText, "World.");
$flow->addContent($paragraph);

$document->add($flow);
file_put_contents('example.pdf', $document->save());
```

Layout engine particulars:

- Layouts can be nested (e.g. you can place a flow in another flow)
- `width` (`height`) includes the `padding`, but not the `margin`
- `margin` does not collapse with adjacent margins
- drawings are ignored during size calculations (e.g. `borderWidth` has no influence on the calculated `width` of an
  element
- `width` (`height`) of a child overrides `width` (`height`) of a parent

## Example

### Invoice

[Code](./examples/invoice.php) | [.pdf](./examples/invoice.pdf)
<table>
    <tbody>
        <tr>
            <td><img src="examples/invoice.png?raw=true" alt="Invoice"></td>
        </tr>
    </tbody>
</table>

### Book

[Code](./examples/book.php) | [.pdf](./examples/book.pdf)

<table>
    <tbody>
        <tr>
            <td><img src="examples/book-1.png?raw=true" alt="Book page 1"></td>
            <td><img src="examples/book-2.png?raw=true" alt="Book page 2"></td>
        </tr>
        <tr>
            <td><img src="examples/book-3.png?raw=true" alt="Book page 3"></td>
            <td><img src="examples/book-4.png?raw=true" alt="Book page 4"></td>
        </tr>
    </tbody>
</table>

