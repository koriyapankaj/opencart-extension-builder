<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/builder.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

$application = new Application('OpenCart Extension Builder', '1.0');


$application->register('build')
    ->setDescription('Creates an OpenCart extension with specified details')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');

        // Questions to ask the user
        $questions = [
            'extension' => 'Select the OpenCart version (required):',
            'extension_name' => 'Enter the extension name (required):',
            'extension_dir_name' => 'Enter the extension directory name:',
            'extension_description' => 'Enter the extension description (required):',
            'version' => 'Enter the extension version:',
            'author' => 'Enter the author name (required):',
            'link' => 'Enter the extension link (required):'
        ];

        $answers = [];

        foreach ($questions as $key => $questionText) {
            do {
                if ($key === 'extension') {
                    // Use a ChoiceQuestion for the extension selection
                    $question = new ChoiceQuestion(
                        $questionText,
                        ['OC3' => 'OpenCart 3 (currently unavailable)', 'OC4' => 'OpenCart 4'],
                        'OC4' // Default value
                    );
                    $question->setErrorMessage('Value %s is invalid.');
                } else {
                    $question = new Question($questionText);
                    // Check if the field is required
                    if (in_array($key, ['extension_name', 'extension_description', 'author', 'link'])) {
                        $question->setValidator(function ($answer) {
                            if (empty($answer)) {
                                throw new \RuntimeException('This field cannot be empty. Please provide a value.');
                            }
                            return $answer;
                        });
                    }
                }

                $answers[$key] = $helper->ask($input, $output, $question);
            } while (empty($answers[$key]) && in_array($key, ['extension_name', 'extension_description', 'author', 'link']));
        }

        try {

            $builder = new Builder($answers);
            $builder->build();

        } catch (\Throwable $th) {
            echo $th->getMessage();
        }

    });

$application->run();
