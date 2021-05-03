<?php

namespace taguz91\PhpBuild;

class Writer
{

    /** @var string */
    const COLOR_WHITE = 'white';

    /** @var string */
    const COLOR_GREEN = 'green';

    /** @var string */
    const COLOR_RED = 'red';

    /** @var string */
    const COLOR_YELLOW = 'yellow';

    const COLORS = [
        self::COLOR_WHITE => '1;37',
        self::COLOR_GREEN => '0;32',
        self::COLOR_RED => '0;31',
        self::COLOR_YELLOW => '1;33',
    ];

    public function print(string $text, string $color = null)
    {
        $cliColor = $this->getCliColor($color);

        if ($cliColor === null) {
            echo "{$text}";
        } else {
            echo "\e[{$cliColor}m$text\e[0m";
        }
        echo "\r\n";
        return $this;
    }

    public function success(string $text)
    {
        return $this->print($text, self::COLOR_GREEN);
    }

    public function warning(string $text)
    {
        return $this->print($text, self::COLOR_YELLOW);
    }

    public function error(string $text)
    {
        return $this->print($text, self::COLOR_RED);
    }

    protected function getCliColor(?string $color)
    {
        if ($color === null) return null;
        return self::COLORS[$color] ?? null;
    }
}
