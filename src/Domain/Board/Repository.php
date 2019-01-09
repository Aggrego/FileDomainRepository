<?php
/**
 * This file is part of the Aggrego.
 * (c) Tomasz Kunicki <kunicki.tomasz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Aggrego\FileDomainRepository\Domain\Board;

use Aggrego\Domain\Board\Board;
use Aggrego\Domain\Board\Exception\BoardExistException;
use Aggrego\Domain\Board\Exception\BoardNotFoundException;
use Aggrego\Domain\Board\Repository as DomainRepository;
use Aggrego\Domain\Board\Uuid;

class Repository implements DomainRepository
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var array
     */
    private $loaded = [];

    public function __construct()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'AggregoBoardRepository' . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->dir = $dir;
    }

    /**
     * @param  Uuid $uuid
     * @return Board
     * @throws BoardNotFoundException
     */
    public function getBoardByUuid(Uuid $uuid): Board
    {
        $boardUuidValue = $uuid->getValue();
        if (isset($this->loaded[$boardUuidValue])) {
            return $this->loaded[$boardUuidValue];
        }

        $filePath = $this->getSavingPath($uuid);
        if (!file_exists($filePath)) {
            throw new BoardExistException(sprintf('Given "%s" don\'t exists in "%s"', $boardUuidValue, $filePath));
        }

        $content = file_get_contents($filePath);
        if (!$content) {
            throw new BoardExistException(sprintf('Given "%s" don\'t have correct format in file.', $boardUuidValue));
        }
        /**
 * @var string $content
*/
        $unserialize = unserialize($content);
        $this->loaded[$boardUuidValue] = $unserialize;

        return $unserialize;
    }

    /**
     * @param  Board $board
     * @throws BoardExistException
     */
    public function addBoard(Board $board): void
    {
        $uuid = $board->getUuid();
        $filePath = $this->getSavingPath($uuid);
        if (file_exists($filePath)) {
            throw new BoardExistException(sprintf('Given "%s" exists', $uuid->getValue()));
        }

        $this->saveFile($board);
    }

    private function saveFile(Board $board): void
    {
        $filePath = $this->getSavingPath($board->getUuid());
        file_put_contents($filePath, serialize($board));
    }

    private function getSavingPath(Uuid $uuid): string
    {
        return $filePath = $this->dir . $uuid->getValue() . '.data';
    }

    public function __destruct()
    {
        foreach ($this->loaded as $board) {
            $this->saveFile($board);
        }
    }
}
