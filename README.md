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

## Elements

Elements may be written in the following form:

```
element#id.class:place holder text
```

The components are:

| Code            | Meaning                                           |
|-----------------|---------------------------------------------------|
| `element`       | HTML tag                                          |
| `#id`           | optional id                                       |
| `.class`        | optional class name                               |
| `:place holder` | optional text which may be used as a place holder |

