<?php

declare(strict_types=1);

/*
 * This file is part of the symsensor/actuator-bundle package.
 *
 * (c) Kevin Studer <kreemer@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymSensor\ActuatorBundle\Tests\Service\Info\Collector;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use SymSensor\ActuatorBundle\Service\Info\Collector\Git;

class GitTest extends TestCase
{
    /**
     * @var KernelInterface&MockObject
     */
    private KernelInterface $kernel;

    private vfsStreamDirectory $root;

    private Git $git;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('exampleDir');
        $this->kernel = self::createMock(KernelInterface::class);
        $this->kernel->method('getProjectDir')
            ->willReturn($this->root->url());

        $this->git = new Git($this->kernel);
    }

    /**
     * @test
     */
    public function willReturnGitAsName(): void
    {
        self::assertEquals('git', $this->git->collect()->name());
    }

    /**
     * @test
     */
    public function gitWillReturnEmptyIfNoGitDirectory(): void
    {
        // when
        $collect = $this->git->collect();

        // then
        self::assertTrue($collect->isEmpty());
    }

    /**
     * @test
     */
    public function willLoadBranchFromGitDirectory(): void
    {
        // given
        $gitDirectory = vfsStream::newDirectory('.git')->at($this->root);
        vfsStream::newFile('HEAD')->withContent('ref: refs/heads/branchName')->at($gitDirectory);
        $refsDirectory = vfsStream::newDirectory('refs')->at($gitDirectory);
        $branchDirectory = vfsStream::newDirectory('heads')->at($refsDirectory);
        vfsStream::newFile('branchName')->withContent('c00000')->at($branchDirectory);

        // when
        $collect = $this->git->collect();

        // then
        self::assertFalse($collect->isEmpty());
        self::assertArrayHasKey('branch', $collect->jsonSerialize());
        self::assertEquals('branchName', $collect->jsonSerialize()['branch']);
    }

    /**
     * @test
     */
    public function willHandleAsEmptyIfNoHeadsFile(): void
    {
        // given
        $gitDirectory = vfsStream::newDirectory('.git')->at($this->root);
        vfsStream::newFile('HEAD')->withContent('ref: refs/heads/branchName')->at($gitDirectory);

        // when
        $collect = $this->git->collect();

        // then
        self::assertTrue($collect->isEmpty());
    }

    /**
     * @test
     */
    public function willHandleCommitNumberAsBranch(): void
    {
        // given
        $gitDirectory = vfsStream::newDirectory('.git')->at($this->root);
        vfsStream::newFile('HEAD')->withContent('c00000')->at($gitDirectory);

        // when
        $collect = $this->git->collect();

        // then
        self::assertFalse($collect->isEmpty());
        self::assertArrayHasKey('branch', $collect->jsonSerialize());
        self::assertEquals('c00000', $collect->jsonSerialize()['branch']);
    }

    /**
     * @test
     */
    public function willHandleCommitNumberFromBranch(): void
    {
        // given
        $gitDirectory = vfsStream::newDirectory('.git')->at($this->root);
        vfsStream::newFile('HEAD')->withContent('ref: refs/heads/branchName')->at($gitDirectory);
        $refsDirectory = vfsStream::newDirectory('refs')->at($gitDirectory);
        $branchDirectory = vfsStream::newDirectory('heads')->at($refsDirectory);
        vfsStream::newFile('branchName')->withContent('c00000')->at($branchDirectory);

        // when
        $collect = $this->git->collect();

        // then
        self::assertFalse($collect->isEmpty());
        self::assertArrayHasKey('commit', $collect->jsonSerialize());
        self::assertEquals('c00000', $collect->jsonSerialize()['commit']);
    }
}
