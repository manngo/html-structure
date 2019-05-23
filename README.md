# HTML Structure

This will generate an HTML structure from an indented list of elements.  
This is useful for starting a new layout.

To generate a new structure:

- List the layout elements, indenting for nested elements:

	```
	header
	main
		article
		aside
	nav
	footer
	```	
-	Select the list
-	Choose one of the options (below).

The result will be something like:

```html
<header>
</header>
<main>
	<article>
	</article>
	<aside>
	</aside>
</main>
<nav>
</nav>
<footer>
</footer>
```

##	Usage

```php
$text = … ;
$structure = HTMLStructure::make($text[,$html]);
```

This returns a string with HTML content.

###	`$text`

- Can be a simple string with the nested structure

	You can assign the text usiing `$text = '…'; `
	
	You can also read a file using `$text = file_get_contents(…);`
	
- Alternatively the text can be an array of lines

	You can read the lines using `$text = file(…);`
	
Note:

- All empty lines will be ignored
- Lines beginning with `#` will be treated as comments, and also ignored
- __Indentation must be strictly with the tab character__

###	`$html`

The optional second parameter is a boolean. If true, the result will include the rest of the HTML, including the `DOCTYPE` and `head` and `body` sections.


## Elements

Elements may be written in the following forms:

```
element[attributes]#id.class:place holder text
element[attributes]#id.class{PHP echo}
```

The components are:

| Code           | HTML        | Meaning                             |
|----------------|-------------|-------------------------------------|
| `element`      | `<element>` | HTML tag                            |
| `[attributes]` |             | additional attributes               |
| `#id`          | `id="…"`    | optional id                         |
| `.class`       | `class="…"` | optional class name                 |
| `:text`        |             | optional text which may be included |
| `{data}`       | <?= data ?> | data to be echoed through PHP       |

- Apart from the `element`, all other components are optional
- If used, the additional components _must_ be in the order above.

	For example `element.class#id` will not work as expected.

###	Void & Container Elements

-	By default, elements will will include closing tags:

	`<p> … </p>`

- The most common void elements, such as `img` , will not include closing tags:

	`<img>`

- If an element is not recognised as void, you can force the issue with a closing tag:

	`foo/` → `<foo>`

###	Place Holders & Text

-	Any text after a colon will be treated as element content:

	`element:content` → `<element>content</element>`

-	Any text after a space will also be treated as element content:

	`element content` → `<element>content</element>`

- Any text at the end inside braces will be treated as a PHP variable to be output:

	`element{content}` → `<element><?= $content ?></element>`

- Multiple variables can be included in braces, separated by a pipe (`|`). In this case, the output will be enclosed in double quotes, and the variables will be enclosed in braces:

	`element{content|more}` → `<element><?= "{$content}{$more}" ?></element>`

