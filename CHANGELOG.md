# Changelog

All notable changes to `upload-file-scanner` will be documented in this file.

## 2.0.0 - 2026-02-05

### Added
- **Contracts**: Introduced `Jodeveloper\UploadFileScanner\Contracts\Scanner` interface.
- **Facades**: Added `Jodeveloper\UploadFileScanner\Facades\Scanner` for fluent usage.
- **DTO**: `ScanResult` is now a `readonly` class with public properties.

### Changed
- **Refactor**: Renamed `SkeletonServiceProvider` to `UploadFileScannerServiceProvider`.
- **Improvement**: `CleanFile` validation rule now automatically resolves the scanner instance if not provided. Also simplified usage to `new CleanFile()`.
- **Breaking**: `ScanResult` properties are now public and `readonly`. Method accessors are still available but optional.

### Removed
- `SkeletonServiceProvider` (Renamed).


## 1.0.0 - 2026-02-03

**Full Changelog**: https://github.com/JoDeveloper/upload-file-scanner/commits/1.0.0
