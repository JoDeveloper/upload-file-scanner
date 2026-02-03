<?php

use Jodeveloper\UploadFileScanner\ClamAvScanner;
use Jodeveloper\UploadFileScanner\Exceptions\ScanFailedException;
use Jodeveloper\UploadFileScanner\ScanResult;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

test('it returns clean result on exit code zero', function () {
    /** @var Process&MockObject $process */
    $process = $this->createMock(Process::class);
    $process->expects($this->once())->method('run');
    $process->expects($this->once())->method('getExitCode')->willReturn(0);
    $process->expects($this->once())->method('getOutput')->willReturn('OK');
    $process->expects($this->once())->method('setTimeout')->with(30)->willReturnSelf();

    $scannerMock = $this->getMockBuilder(ClamAvScanner::class)
        ->setConstructorArgs(['clamscan', 30, ['--no-summary']])
        ->onlyMethods(['createProcess'])
        ->getMock();

    $scannerMock->expects($this->once())
        ->method('createProcess')
        ->willReturn($process);

    $result = $scannerMock->scan('/path/to/file');

    expect($result)
        ->toBeInstanceOf(ScanResult::class)
        ->isClean()->toBeTrue()
        ->hasVirus()->toBeFalse();
});

test('it returns infected result on exit code one', function () {
    /** @var Process&MockObject $process */
    $process = $this->createMock(Process::class);
    $process->expects($this->once())->method('run');
    $process->expects($this->once())->method('getExitCode')->willReturn(1);
    $process->expects($this->once())->method('getOutput')->willReturn('/path/to/file: Eicar-Test-Signature FOUND');
    $process->expects($this->once())->method('setTimeout')->with(30)->willReturnSelf();

    $scannerMock = $this->getMockBuilder(ClamAvScanner::class)
        ->setConstructorArgs(['clamscan', 30, ['--no-summary']])
        ->onlyMethods(['createProcess'])
        ->getMock();

    $scannerMock->expects($this->once())
        ->method('createProcess')
        ->willReturn($process);

    $result = $scannerMock->scan('/path/to/file');

    expect($result)
        ->toBeInstanceOf(ScanResult::class)
        ->isClean()->toBeFalse()
        ->hasVirus()->toBeTrue();
});

test('it throws exception on exit code two', function () {
    $this->expectException(ScanFailedException::class);
    $this->expectExceptionMessage('ClamAV scan failed');

    /** @var Process&MockObject $process */
    $process = $this->createMock(Process::class);
    $process->expects($this->once())->method('run');
    $process->expects($this->once())->method('getExitCode')->willReturn(2);
    $process->expects($this->once())->method('getErrorOutput')->willReturn('clamscan: cannot access /path/to/file: No such file or directory');
    $process->expects($this->once())->method('setTimeout')->with(30)->willReturnSelf();

    $scannerMock = $this->getMockBuilder(ClamAvScanner::class)
        ->setConstructorArgs(['clamscan', 30, ['--no-summary']])
        ->onlyMethods(['createProcess'])
        ->getMock();

    $scannerMock->expects($this->once())
        ->method('createProcess')
        ->willReturn($process);

    $scannerMock->scan('/path/to/file');
});

test('it throws exception on unknown exit code', function () {
    $this->expectException(ScanFailedException::class);

    /** @var Process&MockObject $process */
    $process = $this->createMock(Process::class);
    $process->expects($this->once())->method('run');
    $process->expects($this->once())->method('getExitCode')->willReturn(127);
    $process->expects($this->once())->method('getErrorOutput')->willReturn('clamscan: command not found');
    $process->expects($this->once())->method('setTimeout')->with(30)->willReturnSelf();

    $scannerMock = $this->getMockBuilder(ClamAvScanner::class)
        ->setConstructorArgs(['clamscan', 30, ['--no-summary']])
        ->onlyMethods(['createProcess'])
        ->getMock();

    $scannerMock->expects($this->once())
        ->method('createProcess')
        ->willReturn($process);

    $scannerMock->scan('/path/to/file');
});

test('scan result output returns process output', function () {
    $output = 'Scan complete';

    /** @var Process&MockObject $process */
    $process = $this->createMock(Process::class);
    $process->expects($this->once())->method('run');
    $process->expects($this->once())->method('getExitCode')->willReturn(0);
    $process->expects($this->once())->method('getOutput')->willReturn($output);
    $process->expects($this->once())->method('setTimeout')->with(30)->willReturnSelf();

    $scannerMock = $this->getMockBuilder(ClamAvScanner::class)
        ->setConstructorArgs(['clamscan', 30, ['--no-summary']])
        ->onlyMethods(['createProcess'])
        ->getMock();

    $scannerMock->expects($this->once())
        ->method('createProcess')
        ->willReturn($process);

    $result = $scannerMock->scan('/path/to/file');

    expect($result->output())->toBe($output);
});
