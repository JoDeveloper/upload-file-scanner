<?php

use Symfony\Component\Process\Process;
use Jodeveloper\UploadFileScanner\ClamAvScanner;
use Jodeveloper\UploadFileScanner\Exceptions\ScanFailedException;
use Jodeveloper\UploadFileScanner\ScanResult;

beforeEach(function () {
    Mockery::close();
});

test('it returns clean result on exit code zero', function () {
    $process = Mockery::mock('overload:'.Process::class);
    $process->shouldReceive('run')->once();
    $process->shouldReceive('isSuccessful')->andReturn(true);
    $process->shouldReceive('getExitCode')->andReturn(0);
    $process->shouldReceive('getOutput')->andReturn('OK');
    $process->shouldReceive('setTimeout')->once()->with(30)->andReturnSelf();

    $scanner = new ClamAvScanner(
        binary: 'clamscan',
        timeout: 30,
        scanOptions: ['--no-summary'],
    );

    $result = $scanner->scan('/path/to/file');

    expect($result)
        ->toBeInstanceOf(ScanResult::class)
        ->isClean()->toBeTrue()
        ->hasVirus()->toBeFalse();
});

test('it returns infected result on exit code one', function () {
    $process = Mockery::mock('overload:'.Process::class);
    $process->shouldReceive('run')->once();
    $process->shouldReceive('isSuccessful')->andReturn(false);
    $process->shouldReceive('getExitCode')->andReturn(1);
    $process->shouldReceive('getOutput')->andReturn('/path/to/file: Eicar-Test-Signature FOUND');
    $process->shouldReceive('setTimeout')->once()->with(30)->andReturnSelf();

    $scanner = new ClamAvScanner(
        binary: 'clamscan',
        timeout: 30,
        scanOptions: ['--no-summary'],
    );

    $result = $scanner->scan('/path/to/file');

    expect($result)
        ->toBeInstanceOf(ScanResult::class)
        ->isClean()->toBeFalse()
        ->hasVirus()->toBeTrue();
});

test('it throws exception on exit code two', function () {
    $process = Mockery::mock('overload:'.Process::class);
    $process->shouldReceive('run')->once();
    $process->shouldReceive('isSuccessful')->andReturn(false);
    $process->shouldReceive('getExitCode')->andReturn(2);
    $process->shouldReceive('getErrorOutput')->andReturn('clamscan: cannot access /path/to/file: No such file or directory');
    $process->shouldReceive('setTimeout')->once()->with(30)->andReturnSelf();

    $scanner = new ClamAvScanner(
        binary: 'clamscan',
        timeout: 30,
        scanOptions: ['--no-summary'],
    );

    $scanner->scan('/path/to/file');
})->throws(ScanFailedException::class, 'ClamAV scan failed');

test('it throws exception on unknown exit code', function () {
    $process = Mockery::mock('overload:'.Process::class);
    $process->shouldReceive('run')->once();
    $process->shouldReceive('isSuccessful')->andReturn(false);
    $process->shouldReceive('getExitCode')->andReturn(127);
    $process->shouldReceive('getErrorOutput')->andReturn('clamscan: command not found');
    $process->shouldReceive('setTimeout')->once()->with(30)->andReturnSelf();

    $scanner = new ClamAvScanner(
        binary: 'clamscan',
        timeout: 30,
        scanOptions: ['--no-summary'],
    );

    $scanner->scan('/path/to/file');
})->throws(ScanFailedException::class);

test('scan result output returns process output', function () {
    $output = 'Scan complete';

    $process = Mockery::mock('overload:'.Process::class);
    $process->shouldReceive('run')->once();
    $process->shouldReceive('isSuccessful')->andReturn(true);
    $process->shouldReceive('getExitCode')->andReturn(0);
    $process->shouldReceive('getOutput')->andReturn($output);
    $process->shouldReceive('setTimeout')->once()->with(30)->andReturnSelf();

    $scanner = new ClamAvScanner(
        binary: 'clamscan',
        timeout: 30,
        scanOptions: ['--no-summary'],
    );

    $result = $scanner->scan('/path/to/file');

    expect($result->output())->toBe($output);
});
