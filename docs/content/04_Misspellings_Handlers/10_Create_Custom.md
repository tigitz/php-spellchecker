# Create your custom Misspelling Handler

Misspelling handler has the responsibility to process misspellings found
through the `MisspellingFinder` class.

It must implement the `MisspellingHandlerInterface`.

Let's say you want to send an email with all the misspellings found in
 your blog articles.

Create your `EmailMisspellingHandler`:

```php
<?php
use PhpSpellcheck\MisspellingInterface;

class EmailMisspellingHandler implements MisspellingHandlerInterface
{
    /**
     * @var EmailSenderInterface
     */
    private $emailSender;

    public function __construct(EmailSenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param MisspellingInterface[] $misspellings
     */
    public function handle(iterable $misspellings)
    {
        $message = <<<MSG
Dear me,

You're bad at writing.

Here's the proof:

MSG;
        foreach ($misspellings as  $misspelling) {
            $message .= \Safe\sprintf(
                'You wrote "%s" at line %d in the article "%s" but it\'s a misspelling. Here are my suggestions: %s',
                    $misspelling->getWord(),
                    $misspelling->getLineNumber(),
                    $misspelling->getContext()['article-name'],
                    explode(',', $misspelling->getSuggestions())
                ).PHP_EOL;
        }

        $this->emailSender
            ->body($message)
            ->send();
    }
}
```

As you can see it iterates over your misspellings to build the email body and
then it sends it to you.

You just have to use your custom handler while calling `MisspellingFinder`:

```php
<?php
$misspellingFinder = new MisspellingFinder(
    Aspell::create(),
    new EmailMisspellingHandler(new EmailSender())
);

/** @var SourceInterface $blogArticlesSource */
$blogArticlesSource = ...;

$misspellingFinder->find($blogArticlesSource, ['en_US']);
```
