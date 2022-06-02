<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Utils\CommandLine;
use PhpSpellcheck\Utils\IspellParser;
use PhpSpellcheck\Utils\ProcessRunner;
use Symfony\Component\Process\Process;

class Hunspell implements SpellcheckerInterface
{
    /**
     * @var CommandLine
     */
    private $binaryPath;

    public function __construct(CommandLine $binaryPath)
    {
        $this->binaryPath = $binaryPath;
    }

    /**
     * {@inheritdoc}
     */
    public function check(
        string $text,
        array $languages = [],
        array $context = []
    ): iterable {
        $cmd = $this->binaryPath->addArgs(['-i', 'UTF-8']);
        $cmd = $cmd->addArg('-a');

        if (!empty($languages)) {
            $cmd = $cmd->addArgs(['-d', implode(',', $languages)]);
        }

        $process = new Process($cmd->getArgs());
        // Add prefix characters putting Ispell's type of spellcheckers in terse-mode,
        // ignoring correct words and thus speeding execution
        $process->setInput('!' . PHP_EOL . IspellParser::adaptInputForTerseModeProcessing($text) . PHP_EOL . '%');

        $output = ProcessRunner::run($process)->getOutput();
        if ($process->getErrorOutput() !== '') {
            throw new ProcessHasErrorOutputException($process->getErrorOutput(), $text, $process->getCommandLine());
        }

        return IspellParser::parseMisspellingsFromOutput($output, $context);
    }

    public function getBinaryPath(): CommandLine
    {
        return $this->binaryPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedLanguages(): iterable
    {
        $languages = [];
        $cmd = $this->binaryPath->addArg('-D');
        $process = new Process($cmd->getArgs());
        $output = explode(PHP_EOL, ProcessRunner::run($process)->getErrorOutput());

        foreach ($output as $line) {
            $line = trim($line);
            if ('' === $line // Skip empty lines
                || \Safe\substr($line, -1) === ':' // Skip headers
                || strpos($line, ':') !== false // Skip search path
            ) {
                continue;
            }
            $name = basename($line);
            if (strpos($name, 'hyph_') === 0) {
                // Skip MySpell hyphen files
                continue;
            }
            $name = \Safe\preg_replace('/\.(aff|dic)$/', '', $name);
            $languages[$name] = true;
        }
        $languages = array_keys($languages);
        \Safe\sort($languages);

        return $languages;
    }

    public static function create(?string $binaryPathAsString = null): self
    {
        return new self(new CommandLine($binaryPathAsString ?? 'hunspell'));
    }
}
