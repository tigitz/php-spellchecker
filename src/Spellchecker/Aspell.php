<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Utils\CommandLine;
use PhpSpellcheck\Utils\IspellParser;
use PhpSpellcheck\Utils\ProcessRunner;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

class Aspell implements SpellcheckerInterface
{
    /**
     * @var CommandLine
     */
    private $binaryPath;

    public function __construct(CommandLine $binaryPath)
    {
        $this->binaryPath = $binaryPath;
    }

    public function check(string $text, array $languages = [], array $context = []): iterable
    {
        Assert::maxCount($languages, 1, 'Aspell spellchecker doesn\'t support multiple languages check');

        $cmd = $this->binaryPath->addArgs(['--encoding', 'utf-8']);
        $cmd = $cmd->addArg('-a');

        if (!empty($languages)) {
            $cmd = $cmd->addArg('--lang=' . implode(',', $languages));
        }

        $process = new Process($cmd->getArgs());
        // Add prefix characters putting Ispell's type of spellcheckers in terse-mode,
        // ignoring correct words and thus speeding up the execution
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

    public function getSupportedLanguages(): iterable
    {
        $languages = [];
        $cmd = $this->binaryPath->addArgs(['dump', 'dicts']);
        $process = new Process($cmd->getArgs());
        $output = explode(PHP_EOL, ProcessRunner::run($process)->getOutput());

        foreach ($output as $line) {
            $name = trim($line);
            if (strpos($name, '-variant') !== false || $name === '') {
                // Skip variants
                continue;
            }
            $languages[$name] = true;
        }
        $languages = array_keys($languages);
        \Safe\sort($languages);

        return $languages;
    }

    public static function create(?string $binaryPathAsString = null): self
    {
        return new self(new CommandLine($binaryPathAsString ?? 'aspell'));
    }
}
