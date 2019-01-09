<?php
/**
 * This file is part of the Aggrego.
 * (c) Tomasz Kunicki <kunicki.tomasz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace spec\Aggrego\FileDomainRepository\Domain\Board;

use Aggrego\FileDomainRepository\Domain\Board\Repository;
use Aggrego\Domain\Board\Repository as DomainRepository;
use PhpSpec\ObjectBehavior;

class RepositorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Repository::class);
        $this->shouldHaveType(DomainRepository::class);
    }
}
