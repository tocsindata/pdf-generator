# Contribute

Contributions are very welcome. Before investing serious effort, please create an issue to discuss target functionality
and architecture.

TTF reader/writer:

- [x] read TTF
- [x] write TTF
- [x] create TTF subsets
- [x] provide font dimensions to measure text

PDF backend:

- [x] print images
- [x] print & style drawings (lines & rectangles)
- [x] print & style text
- [x] use TTF fonts
- [x] use UTF-8 text

paragraphs:

- [x] support different text styles in same paragraphs
- [x] calculate dimensions of text
- [x] automatic line-breaking
- [x] alignment (center, right-align, justify)

layouts:

- [x] design layout system
- [x] implement block
- [x] implement flow
- [x] implement grid
- [x] implement table

layout blocks:

- [x] margin
- [x] padding
- [x] border (color, thickness, stroke style)
- [x] background (color)

extended PDF support:

- [x] meta data
- [ ] tags
- [ ] more content types (png, svg, esp, ...)
- [ ] more drawings (circles, polynomials)

extend layout support:

- [ ] collapse margins (i.e. margin of child and parent not added, but MAXed)
- [ ] alignment for blocks
- [ ] column/row spans for grids, tables
- [ ] auto, contain, cover for content types
- [ ] top/right/bottom/left different weight borders

non-targets:

- [ ] forms -> PDF is meant for printing
- [ ] compress (string) streams -> produced PDFs should remain machine readable

technical:

- [ ] optimize rectangle position (do not modify transform matrix)

## Maintenance/architecture contributions

This is a large, long-lived project, and as such there are maintenance and architecture topics.

Topics:
- [ ] Use `enum` instead of `const`
- [ ] Add snapshot-based unit tests
- [ ] Add consistent edge-case testing (images of size 0, newline/spaces at the of a line, ...)
- [ ] Refactor compiler configuration approach
