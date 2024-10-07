<?php

namespace App\Tests;

use App\Entity\FileImport;
use App\Entity\Supplier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\WorkflowInterface;

class ImportValidationTest extends KernelTestCase
{
    private WorkflowInterface $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflow = static::getContainer()->get('state_machine.import_validation');
    }

    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
        // test CSV format
        // test insertion of CSV
    }

    public function testWorkflow(): void
    {
        $supplier = new Supplier();
        $supplier->setName('TestKOL');
        $fileImport = new FileImport();
        $fileImport->setFilename('test.csv');
        $fileImport->setSupplier($supplier);
        $this->assertSame('draft', $fileImport->getStatus());
        $this->assertTrue($this->workflow->can($fileImport, 'to_review'));
        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));
    }

    public function testAdvanceWorkflow(): void
    {
        $supplier = new Supplier();
        $supplier->setName('TestKOL');
        $fileImport = new FileImport();
        $fileImport->setFilename('test.csv');
        $fileImport->setSupplier($supplier);
        $this->assertSame('draft', $fileImport->getStatus());
        $this->assertTrue($this->workflow->can($fileImport, 'to_review'));
        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));

        $this->workflow->apply($fileImport, 'to_review');

        $this->assertSame('imported', $fileImport->getStatus());

        $this->assertTrue($this->workflow->can($fileImport, 'approve'));
        $this->assertTrue($this->workflow->can($fileImport, 'reject'));
        $this->assertFalse($this->workflow->can($fileImport, 'to_review'));
    }

    public function testApproveWorkflow(): void
    {
        $supplier = new Supplier();
        $supplier->setName('TestKOL');
        $fileImport = new FileImport();
        $fileImport->setFilename('test.csv');
        $fileImport->setSupplier($supplier);
        $this->assertSame('draft', $fileImport->getStatus());
        $this->assertTrue($this->workflow->can($fileImport, 'to_review'));
        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));

        $this->workflow->apply($fileImport, 'to_review');
        $this->workflow->apply($fileImport, 'approve');

        $this->assertSame('approved', $fileImport->getStatus());

        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));
        $this->assertFalse($this->workflow->can($fileImport, 'to_review'));
    }

    public function testRejectWorkflow(): void
    {
        $supplier = new Supplier();
        $supplier->setName('TestKOL');
        $fileImport = new FileImport();
        $fileImport->setFilename('test.csv');
        $fileImport->setSupplier($supplier);
        $this->assertSame('draft', $fileImport->getStatus());
        $this->assertTrue($this->workflow->can($fileImport, 'to_review'));
        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));

        $this->workflow->apply($fileImport, 'to_review');
        $this->workflow->apply($fileImport, 'reject');

        $this->assertSame('rejected', $fileImport->getStatus());

        $this->assertFalse($this->workflow->can($fileImport, 'approve'));
        $this->assertFalse($this->workflow->can($fileImport, 'reject'));
        $this->assertFalse($this->workflow->can($fileImport, 'to_review'));
    }
}
