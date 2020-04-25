<?php

declare(strict_types=1);

namespace PhpSpellcheck\TextProcessor;

use PhpSpellcheck\TextInterface;

/**
 * @experimental
 *
 * Removes markdown while trying to keeping original lines and offset position of
 * characters in order to make spellchecking relevant.
 */
class MarkdownRemover implements TextProcessorInterface
{
    public function process(TextInterface $text): TextInterface
    {
        // Horizontal rules (stripListHeaders conflict with this rule, which is why it has been moved to the top)
        $output = \Safe\preg_replace('/^(-\s*?|\*\s*?|_\s*?){3,}(\s*)$/m', PHP_EOL . '$2', $text->getContent());

        // Github Flavored Markdown
        // Header
        $output = \Safe\preg_replace('/\n={2,}/', '\n', $output);
        /**
         * Fenced codeblocks.
         *
         *@TODO parse programming language comments from codeblock instead of removing whole block
         */
        $output = \Safe\preg_replace('/~{3}.*\n/', '', $output);
        // Strikethrough
        $output = \Safe\preg_replace('/~~/', '', $output);
        // Common Markdown
        // Remove HTML tags
        $output = \Safe\preg_replace('/<[^>]*>/', '', $output);
        // Remove setext-style headers
        $output = \Safe\preg_replace('/^[=\-]{2,}\s*$/', '', $output);
        // Remove footnotes?
        $output = \Safe\preg_replace('/\[\^.+?\](\: .*?$)?/', '', $output);
        $output = \Safe\preg_replace('/\s{0,2}\[.*?\]: .*?$/', '', $output);
        // Remove images
        $output = \Safe\preg_replace('/\!\[(.*?)\][\[\(].*?[\]\)]/', '$1', $output);
        // Remove inline links
        $output = \Safe\preg_replace('/\[(.*?)\][\[\(].*?[\]\)]/', '$1', $output);
        // Remove blockquotes
        $output = \Safe\preg_replace('/^\s{0,3}>\s?/', '', $output);
        // Remove reference-style links?
        $output = \Safe\preg_replace('/^\s{1,2}\[(.*?)\]: (\S+)( ".*?")?\s*$/', '', $output);
        /**
         * Remove atx-style headers.
         *
         *@TODO find a way to merge the two regex below
         * remove ## Heading ##
         */
        $output = \Safe\preg_replace('/^#{1,6}\s+(.*)(\s+#{1,6})$/m', '$1', $output);
        // remove ## Heading
        $output = \Safe\preg_replace('/^#{1,6}\s+(.*)$/m', '$1', $output);
        // Remove emphasis (repeat the line to remove double emphasis)
        $output = \Safe\preg_replace('/([\*_]{1,3})(\S.*?\S{0,1})\1/', '$2', $output);
        $output = \Safe\preg_replace('/([\*_]{1,3})(\S.*?\S{0,1})\1/', '$2', $output);
        // Remove list items
        $output = \Safe\preg_replace('/^([^\S\r\n]*)\*\s/m', '$1', $output);
        // Remove code blocks
        $output = \Safe\preg_replace('/^`{3,}(.*)*$/m', '', $output);
        // Remove inline code
        $output = \Safe\preg_replace('/`(.+?)`/', '$1', $output);

        return $text->replaceContent($output);
    }
}
