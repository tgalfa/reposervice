<?php

namespace tgalfa\RepoService\Tests;

class GenerateRepositoryServiceCommandTest extends TestCase
{
    /**
     * Test a console command.
     *
     * @return void
     */
    public function test_console_command()
    {
        $classes = $this->getTestClassNames();

        $this->runConsoleCommand()
             ->expectsOutput("The contract [{$classes['interfaces']['repository']}] has been created.")
             ->expectsOutput("The repository [{$classes['repository']}] has been created.")
             ->expectsOutput("The contract [{$classes['interfaces']['service']}] has been created.")
             ->expectsOutput("The service [{$classes['service']}] has been created.")
             ->assertExitCode(0);
    }

    /**
     * Test a console command file overwrites.
     *
     * @return void
     */
    public function test_console_command_overwrite()
    {
        $classes = $this->getTestClassNames();

        // Pre run Console Command to generate Files.
        $this->runConsoleCommand()->assertExitCode(0);

        $this->runConsoleCommand()
             ->expectsConfirmation(
                 "The contract [{$classes['interfaces']['repository']}] already exists. Do you want to overwrite it?",
                 'yes'
             )
             ->expectsConfirmation(
                 "The repository [{$classes['repository']}] already exists. Do you want to overwrite it?",
                 'yes'
             )
             ->expectsConfirmation(
                 "The contract [{$classes['interfaces']['service']}] already exists. Do you want to overwrite it?",
                 'yes'
             )
             ->expectsConfirmation(
                 "The service [{$classes['service']}] already exists. Do you want to overwrite it?",
                 'yes'
             )
             ->assertExitCode(0);
    }

    /**
     * Test a console command skip file overwrites.
     *
     * @return void
     */
    public function test_console_command_no_overwrite()
    {
        $classes = $this->getTestClassNames();

        // Pre run Console Command to generate Files.
        $this->runConsoleCommand()->assertExitCode(0);

        $this->runConsoleCommand()
             ->expectsConfirmation(
                 "The contract [{$classes['interfaces']['repository']}] already exists. Do you want to overwrite it?",
                 'no'
             )
             ->expectsOutput("The contract [{$classes['interfaces']['repository']}] will not be overwritten.")
             ->expectsConfirmation(
                 "The repository [{$classes['repository']}] already exists. Do you want to overwrite it?",
                 'no'
             )
             ->expectsOutput("The repository [{$classes['repository']}] will not be overwritten.")
             ->expectsConfirmation(
                 "The contract [{$classes['interfaces']['service']}] already exists. Do you want to overwrite it?",
                 'no'
             )
             ->expectsOutput("The contract [{$classes['interfaces']['service']}] will not be overwritten.")
             ->expectsConfirmation(
                 "The service [{$classes['service']}] already exists. Do you want to overwrite it?",
                 'no'
             )
             ->expectsOutput("The service [{$classes['service']}] will not be overwritten.")
             ->assertExitCode(0);
    }

    /**
     * Test a console command new Model creation.
     *
     * @return void
     */
    public function test_console_command_new_model()
    {
        $model = $this->getTestNewModelName();

        $this->runConsoleCommand($model)
             ->expectsConfirmation("Model [{$model}] does not exist. Would you like to create it?", 'yes')
             ->expectsOutput("Model [{$model}] has been successfully created.")
             ->assertExitCode(0);
    }

    /**
     * Test a console command skip new Model creation.
     *
     * @return void
     */
    public function test_console_command_skip_new_model()
    {
        $model = $this->getTestNewModelName();

        $this->runConsoleCommand($model)
             ->expectsConfirmation("Model [{$model}] does not exist. Would you like to create it?", 'no')
             ->expectsOutput("Model [{$model}] is not being created.")
             ->expectsOutput("Model [{$model}] doesn't exist. Please create it!")
             ->assertExitCode(0);
    }
}
