<?php

namespace App;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implements logic for class statistic
 *
 * @author Yuriy Filonenko <mail@gmail.com>
 */
class ClassStatisticCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('class-stat')
            ->setDescription('Show class statistic')
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                "'Full class name'"
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('class');
        
        $reflector = new ReflectionClass($class);
        
        $outputClassName = '';

        if ($reflector->isFinal()) {
            $outputClassName = "$class (Final class)";
        } elseif ($reflector->isAbstract()) {
            $outputClassName = "$class (Abstract class)";
        } else {
            $outputClassName = "$class";
        }

        $countOfPublicProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PUBLIC)
        );
        
        $countOfProtectedProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PROTECTED)
        );
        
        $countOfPrivateProperties = \count(
            $reflector->getProperties(\ReflectionProperty::IS_PRIVATE)
        );
        
        $countOfPublicMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
        
        $countOfProtectedMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PROTECTED)
        );
        
        $countOfPrivateMethods = \count(
            $reflector->getMethods(\ReflectionMethod::IS_PRIVATE)
        );
        
        $output->writeln(
            "Class: $outputClassName" . \PHP_EOL
            . 'Propertyes:' . \PHP_EOL
            . "\t" . "public: $countOfPublicProperties" . \PHP_EOL
            . "\t" . "protected: $countOfProtectedProperties" . \PHP_EOL
            . "\t" . "private: $countOfPrivateProperties" . \PHP_EOL
            . 'Methods:' . \PHP_EOL
            . "\t" . "public: $countOfPublicMethods" . \PHP_EOL
            . "\t" . "protected: $countOfProtectedMethods" . \PHP_EOL
            . "\t" . "private: $countOfPrivateMethods" . \PHP_EOL
        );
    }
}
