# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Each release must:

- be labelled as a level-2 heading
- link to view a diff with the previous release
- be dated in ISO format (`YYYY-MM-DD`)

Allowed sections (level-3 headings) for each release are:

- Added
- Changed
- Fixed
- Removed


## [Unreleased]

## [2.0.1] - 2024-04-18

### Fixed 

- fix a bug on multi-tenant sites that caused translations from the last site of a given language to be returned instead
  of the current site's translations


## [2.0.0] - 2024-04-18

### Added

- Add Craft 5 compatibility

### Changed

- [BREAKING] change module namespace to `\nstcactus\craftcms\modules\translations`


## [1.1.0] - 2023-10-13

### Added

- Automatically add missing translatable items in the database


## [1.0.0] - 2022-11-10

- First version of the module


[unreleased]: https://github.com/nstCactus/craft-translations-module/compare/2.0.1...main
[2.0.1]: https://github.com/nstCactus/craft-translations-module/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/nstCactus/craft-translations-module/compare/1.1.0...2.0.0
[1.1.0]: https://github.com/nstCactus/craft-translations-module/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/nstCactus/craft-translations-module/releases/tag/1.0.0
