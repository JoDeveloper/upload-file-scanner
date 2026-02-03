<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Jodeveloper\UploadFileScanner\ClamAvScanner;
use Jodeveloper\UploadFileScanner\Rules\CleanFile;
use Jodeveloper\UploadFileScanner\ScanResult;
use Jodeveloper\UploadFileScanner\Tests\TestCase;

beforeEach(function () {
    Mockery::close();
});

test('validation rule passes for clean file', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1000);

    $scanner = Mockery::mock(ClamAvScanner::class);
    $scanner->shouldReceive('scan')
        ->once()
        ->with($file->getRealPath())
        ->andReturn(new ScanResult(clean: true, output: 'OK'));

    $rule = new CleanFile($scanner);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => $rule]
    );

    expect($validator->passes())->toBeTrue();
});

test('validation rule fails for infected file', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1000);

    $scanner = Mockery::mock(ClamAvScanner::class);
    $scanner->shouldReceive('scan')
        ->once()
        ->with($file->getRealPath())
        ->andReturn(new ScanResult(clean: false, output: 'Eicar-Test-Signature FOUND'));

    $rule = new CleanFile($scanner);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => $rule]
    );

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('file'))->toBe('The uploaded file contains a virus.');
});

test('validation rule fails for file without real path', function () {
    $file = Mockery::mock(UploadedFile::class);
    $file->shouldReceive('getRealPath')->andReturn(false);
    $file->shouldReceive('isValid')->andReturn(true);

    $scanner = Mockery::mock(ClamAvScanner::class);
    $scanner->shouldNotReceive('scan');

    $rule = new CleanFile($scanner);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => $rule]
    );

    expect($validator->passes())->toBeFalse();
});

test('scanner exception bubbles up', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1000);

    $scanner = Mockery::mock(ClamAvScanner::class);
    $scanner->shouldReceive('scan')
        ->once()
        ->with($file->getRealPath())
        ->andThrow(new \Exception('ClamAV binary not found'));

    $rule = new CleanFile($scanner);

    $validator = Validator::make(
        ['file' => $file],
        ['file' => $rule]
    );

    $validator->passes();
})->throws(\Exception::class, 'ClamAV binary not found');

test('scanner is bound in container', function () {
    expect(app()->bound(ClamAvScanner::class))->toBeTrue();
    expect(app(ClamAvScanner::class))->toBeInstanceOf(ClamAvScanner::class);
});
