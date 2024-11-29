<?php

namespace Tests\Feature;

use App\Core\Shared\Sanitization\CapitalizeFirstWordProcessor;
use App\Core\Shared\Sanitization\CapitalizeWordsProcessor;
use App\Core\Shared\Sanitization\EscapeHTMLProcessor;
use App\Core\Shared\Sanitization\NormalizeSpacesProcessor;
use App\Core\Shared\Sanitization\NormalizeUnicodeProcessor;
use App\Core\Shared\Sanitization\OnlyAlphanumericAndSpacesProcessor;
use App\Core\Shared\Sanitization\OnlyAlphanumericProcessor;
use App\Core\Shared\Sanitization\OnlyLettersAndSpacesProcessor;
use App\Core\Shared\Sanitization\OnlyLettersProcessor;
use App\Core\Shared\Sanitization\OnlyLettersWithoutSpaceProcessor;
use App\Core\Shared\Sanitization\RemoveHTMLTagsProcessor;
use App\Core\Shared\Sanitization\RemoveWhitespaceProcessor;
use App\Core\Shared\Sanitization\ToLowerCaseProcessor;
use App\Core\Shared\Sanitization\ToUpperCaseProcessor;
use App\Core\Shared\Sanitization\TrimProcessor;
use Elseoclub\RepositoryPattern\Console\Commands\Shared\MakeSharedFilesCommand;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class SanitizationProcessorsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->executeCommand(new Filesystem());
    }

    private function executeCommand(Filesystem $files): void
    {
        $command = new MakeSharedFilesCommand($files);
        $command->setLaravel($this->app);

        $input  = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);
    }

    #[Test]
    public function test_trim_processor()
    {
        $processor = new TrimProcessor();
        $this->assertEquals(
            'example',
            $processor->sanitize('  example  ')
        );

    }

    #[Test]
    public function test_remove_whitespace_processor()
    {
        $processor = new RemoveWhitespaceProcessor();
        $this->assertEquals('exampletext', $processor->sanitize('example   text  '));
    }

    #[Test]
    public function test_normalize_spaces_processor()
    {
        $processor = new NormalizeSpacesProcessor();
        $this->assertEquals('example text ', $processor->sanitize('example   text  '));
    }

    #[Test]
    public function test_escape_html_processor()
    {
        $processor = new EscapeHTMLProcessor();
        $this->assertEquals('&lt;b&gt;bold&lt;/b&gt;', $processor->sanitize('<b>bold</b>'));
    }

    #[Test]
    public function test_remove_html_tags_processor()
    {
        $processor = new RemoveHTMLTagsProcessor();
        $this->assertEquals('bold', $processor->sanitize('<b>bold</b>'));
    }

    #[Test]
    public function test_to_lower_case_processor()
    {
        $processor = new ToLowerCaseProcessor();
        $this->assertEquals('example', $processor->sanitize('ExAmPlE'));
    }

    #[Test]
    public function test_to_upper_case_processor()
    {
        $processor = new ToUpperCaseProcessor();
        $this->assertEquals('EXAMPLE', $processor->sanitize('ExAmPlE'));
    }

    #[Test]
    public function test_capitalize_words_processor()
    {
        $processor = new CapitalizeWordsProcessor();
        $this->assertEquals('Example Text', $processor->sanitize('example text'));
    }

    #[Test]
    public function test_capitalize_first_word_processor()
    {
        $processor = new CapitalizeFirstWordProcessor();
        $this->assertEquals('Example text', $processor->sanitize('example text'));
    }

    #[Test]
    public function test_only_letters_processor()
    {
        $processor = new OnlyLettersProcessor();
        $this->assertEquals('exampletext', $processor->sanitize('example123 text!'));
        $this->assertEquals('Cañón', $processor->sanitize('Cañón 456!'));
        $this->assertEquals('Приветмир', $processor->sanitize('Привет мир!'));
        $this->assertEquals('例子示例', $processor->sanitize('例子 示例!'));
    }

    #[Test]
    public function test_only_letters_and_spaces_processor()
    {
        $processor = new OnlyLettersAndSpacesProcessor();
        $this->assertEquals('example text', $processor->sanitize('example123 text!'));
        $this->assertEquals('Cañón ', $processor->sanitize('Cañón 123!'));
        $this->assertEquals('Привет мир', $processor->sanitize('Привет мир!'));
        $this->assertEquals('例子 示例', $processor->sanitize('例子 示例!'));
    }

    #[Test]
    public function test_only_alphanumeric_processor()
    {
        $processor = new OnlyAlphanumericProcessor();
        $this->assertEquals('example123text', $processor->sanitize('example 123 text!'));
        $this->assertEquals('Cañón456', $processor->sanitize('Cañón 456!'));
        $this->assertEquals('Привет789', $processor->sanitize('Привет 789!'));
        $this->assertEquals('例子1010', $processor->sanitize('例子 1010!'));
    }

    #[Test]
    public function test_only_alphanumeric_and_spaces_processor()
    {
        $processor = new OnlyAlphanumericAndSpacesProcessor();
        $this->assertEquals('example123 text', $processor->sanitize('example123 text!'));
        $this->assertEquals('Cañón 123', $processor->sanitize('Cañón 123!'));
        $this->assertEquals('Привет 123', $processor->sanitize('Привет 123!'));
        $this->assertEquals('例子 123', $processor->sanitize('例子 123!'));
    }

    #[Test]
    public function test_normalize_unicode_processor()
    {
        if(!class_exists('Normalizer')) {
            $this->markTestSkipped('The Normalizer class requires the intl extension.');
        }
        $processor = new NormalizeUnicodeProcessor();

        $inputNFC = "\u{00E9}";

        $this->assertEquals("é", $processor->sanitize($inputNFC));
    }
}
