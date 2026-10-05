<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyTicket;
use Simtabi\Lacommerce\Tests\TestCase;

final class HasTicketNumberTest extends TestCase
{
    #[Test]
    public function it_generates_a_ticket_number_on_create(): void
    {
        $ticket = DummyTicket::create(['name' => 'Printer jam']);

        $this->assertMatchesRegularExpression('/^PRI-\d{10}$/', $ticket->ticket_number);
        $this->assertNull($ticket->sku);
    }

    #[Test]
    public function it_refreshes_the_ticket_number_when_the_source_column_changes(): void
    {
        $ticket = DummyTicket::create(['name' => 'Printer jam']);

        $ticket->update(['name' => 'Network down']);

        $this->assertMatchesRegularExpression('/^NET-\d{10}$/', $ticket->ticket_number);
    }

    #[Test]
    public function it_exposes_its_configs(): void
    {
        $ticket = new DummyTicket();

        $this->assertInstanceOf(TicketNumberConfigs::class, $ticket->ticketNumberConfigs());
        $this->assertSame('ticket_number', $ticket->ticketNumberConfig('destinationColumn'));
    }
}
