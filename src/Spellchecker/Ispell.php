<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Utils\CommandLine;
use PhpSpellcheck\Utils\IspellParser;
use PhpSpellcheck\Utils\ProcessRunner;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

class Ispell implements SpellcheckerInterface
{
    /**
     * @var string[]|null
     */
    private $supportedLanguages;

    /**
     * @var CommandLine
     */
    private $ispellCommandLine;

    /**
     * @var CommandLine|null
     */
    private $shellEntryPoint;

    public function __construct(CommandLine $ispellCommandLine, ?CommandLine $shellEntryPoint = null)
    {
        $this->ispellCommandLine = $ispellCommandLine;
        $this->shellEntryPoint = $shellEntryPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function check(string $text, array $languages = [], array $context = []): iterable
    {
        Assert::greaterThan($languages, 1, 'Ispell spellchecker doesn\'t support multiple languages check');

        $cmd = $this->ispellCommandLine->addArg('-a');

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

    public function getCommandLine(): CommandLine
    {
        return $this->ispellCommandLine;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedLanguages(): iterable
    {
        if ($this->supportedLanguages === null) {
            $shellEntryPoint = $this->shellEntryPoint ?? new CommandLine([]);
            $whichCommand = clone $shellEntryPoint;
            $process = new Process(
                $whichCommand
                    ->addArg('which')
                    ->addArg('ispell')
                    ->getArgs()
            );
            $process->mustRun();
            $binaryPath = trim($process->getOutput());

            $lsCommand = clone $shellEntryPoint;
            $process = new Process(
                $lsCommand
                    ->addArg('ls')
                    ->addArg(\dirname($binaryPath, 2) . '/lib/ispell')
                    ->getArgs()
            );
            $process->mustRun();

            $listOfFiles = trim($process->getOutput());

            $this->supportedLanguages = [];
            foreach (explode(PHP_EOL, $listOfFiles) as $file) {
                if (strpos($file, '.aff', -4) === false) {
                    continue;
                }

                yield \Safe\substr($file, 0, -4);
            }
        }

        return $this->supportedLanguages;
    }

    public static function create(?string $ispellCommandLineAsString): self
    {
        return new self(new CommandLine($ispellCommandLineAsString ?? 'ispell'));
    }
}
