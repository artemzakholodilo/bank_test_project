<?php

namespace App\AdminBundle\Command;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UploadCommand extends Command
{
    protected static $defaultName = 'app:import';

    private $em;

    private $validator;


    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        string $name = null
    ) {
        parent::__construct($name);
        $this->em = $em;
        $this->validator = $validator;
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'CSV Filename')
            ->setDescription('Importsfrom CSV file.')
            ->setHelp('Import from csv');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            $io->error('File does not exist');
            return Command::FAILURE;
        }

        if (($handle = fopen($filename, "r")) === false) {
            $io->error('Cannot open file');
            return Command::FAILURE;
        }

        $imported_count = 0;
        $failed_count = 0;
        $counter = 0;

        $header = fgetcsv($handle, 1000, ";");

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            if(count($data) !== count($header)) {
                $failed_count++;
                continue;
            }


            foreach($header as $colIndex => $locale) {
            }


            if(!$this->validator->validate($city)) {
                $failed_count++;
                continue;
            }

            $this->em->persist($city);
            $imported_count++;

            $counter++;
            if($counter > 100) {
                $this->em->flush();
                $counter = 0;
            }
        }
        fclose($handle);

        $io->success('Cities imported successfully');
        $io->writeln("Imported rows: $imported_count");
        $io->writeln("Failed rows:   $failed_count");

        return Command::SUCCESS;
    }
}
