# Development

This document is primarily for developers only.

## CSS changes

ecDB site uses `style.css` file, but this file is compiled from `less` files, so do not make and changes in `css` file,
instead change `less` files and compile them to `css` file.

:point_right: You need `npm` to compile `less` files to `css` file.

To compile `less` files to `css` file without minimizing css:
```bash
npm run dev
```

### Committing style changes

When committing `less` file changes, commit also `css` files. In that case there is no need to compile anything to get
latest ecDB code. Committed `css` should be minimized.

To compile `less` files to `css` file and minimizing it:
```bash
npm run build
```
