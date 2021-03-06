<?php
declare(strict_types=1);

namespace Genkgo\TestMail\Unit\Protocol\Imap\Request;

use Genkgo\Mail\Protocol\Imap\Request\CheckCommand;
use Genkgo\Mail\Protocol\Imap\Tag;
use Genkgo\TestMail\AbstractTestCase;

final class CheckCommandTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_creates_a_stream(): void
    {
        $command = new CheckCommand(Tag::fromNonce(1));

        $this->assertSame('TAG1 CHECK', (string)$command->toStream());
        $this->assertSame('TAG1', (string)$command->getTag());
    }
}
