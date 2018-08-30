<?php

/*
 * This file is part of the Silence project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MarcW\Silence\Util;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * AudioDurationExtractor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudioDurationExtractor
{
    public function extract(string $in)
    {
        $process = new Process(['soxi', '-D', $in]);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        return round($process->getOutput());
    }
}
