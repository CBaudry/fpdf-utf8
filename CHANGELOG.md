# Change Log
All notable changes to this project will be documented in this file.

## [Unreleased]

## [v3.0.1]

### Fixes
 - Fixed vendor name

## [v3.0.0]

### Changed

 - Added EAN 13, 128 and Code 39 support directly inside the main class
 - Added PDF Encryption directly inside the main class
 - Added file attachement
 - Added XMP data handling
 - Added WebP image format handling
 - Generated PDF are PDF/A compliant

## [v2.0.7]

### Fixes
- PHPDoc fixes

## [v2.0.6]

### Fixes
- Fixed unit test failing when using the default unifont files
- Font descriptor data was not utilised properly

### Changed
- New parameter added to MultiCell - which allows you to limit the number of lines the MultiCell should use at most. If the parameter is passed the Multicell will return a string with the remaining text, which did not fit.

## [v2.0.5]

### Fixes
- "U" style did not actually underline the text.

[Unreleased]: https://github.com/DocnetUK/tfpdf/compare/v2.0.7...HEAD
[v2.0.7]: https://github.com/DocnetUK/tfpdf/compare/v2.0.6...v2.0.7
[v2.0.6]: https://github.com/DocnetUK/tfpdf/compare/v2.0.5...v2.0.6
[v2.0.5]: https://github.com/DocnetUK/tfpdf/compare/v2.0.4...v2.0.5
